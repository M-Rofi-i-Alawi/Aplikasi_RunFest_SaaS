<?php

namespace App\Http\Controllers;

use App\Models\EventLari;
use App\Models\KategoriLari;
use App\Models\PembayaranLari;
use App\Models\PendaftaranLari;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Form pendaftaran event (Runner).
     */
    public function create(string $slug)
    {
        $event = EventLari::where('slug', $slug)
            ->where('status_event', 'Publikasi')
            ->with('kategori')
            ->firstOrFail();

        // Cek identitas runner
        $user = auth()->user();
        if ($user->isRunner() && (empty($user->nik) || strlen($user->nik) !== 16 || empty($user->foto_identitas))) {
            return redirect()->route('account.settings')
                ->with('warning', '⚠️ Wajib Lengkapi Identitas: Harap isi NIK valid (16 digit angka) dan unggah Foto KTP/Kartu Pelajar sebelum mendaftar event.');
        }

        // Cek pendaftaran aktif: Pisahkan antara Lunas dan Pending (Opsi B Anti-Spam)
        $sudahLunas = PendaftaranLari::where('id_event', $event->id_event)
            ->where('id_runner', $user->id_user)
            ->where('status_pembayaran', 'Lunas')
            ->exists();

        if ($sudahLunas) {
            return redirect()->route('runner.dashboard')
                ->with('warning', 'Anda sudah terdaftar dan memiliki tiket resmi untuk event ini.');
        }

        $adaPending = PendaftaranLari::where('id_event', $event->id_event)
            ->where('id_runner', $user->id_user)
            ->where('status_pembayaran', 'Pending')
            ->exists();

        if ($adaPending) {
            return redirect()->route('runner.dashboard')
                ->with('error', 'Kamu masih memiliki tagihan yang belum dibayar untuk event ini. Selesaikan atau batalkan terlebih dahulu.');
        }

        return view('runner.register-event', compact('event'));
    }

    /**
     * Proses pendaftaran event.
     * Menggunakan DB::transaction() dan lockForUpdate() untuk mencegah race condition.
     * Opsi A: Nomor BIB HANYA diterbitkan jika pembayaran sudah Lunas (atau event gratis).
     * Opsi B: Membatasi maksimal 1 tiket pending per runner per event.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_event'         => ['required', 'exists:event_lari,id_event'],
            'id_kategori'      => ['required', 'exists:kategori_lari,id_kategori'],
            'ukuran_jersey'    => ['required', 'in:S,M,L,XL,XXL'],
            'metode_pembayaran' => ['nullable', 'string', 'max:50'],
        ]);

        $user = auth()->user();

        if ($user->isRunner() && (empty($user->nik) || strlen($user->nik) !== 16 || empty($user->foto_identitas))) {
            return redirect()->route('account.settings')
                ->with('warning', '⚠️ Wajib Lengkapi Identitas: Harap isi NIK valid (16 digit angka) dan unggah Foto KTP/Kartu Pelajar sebelum mendaftar event.');
        }

        try {
            $result = DB::transaction(function () use ($validated, $user) {
                $kategori = KategoriLari::where('id_kategori', $validated['id_kategori'])
                    ->lockForUpdate()
                    ->firstOrFail();

                // Validasi tiket Lunas
                $sudahLunas = PendaftaranLari::where('id_event', $validated['id_event'])
                    ->where('id_runner', $user->id_user)
                    ->where('status_pembayaran', 'Lunas')
                    ->exists();

                if ($sudahLunas) {
                    throw new \Exception('Anda sudah terdaftar dan memiliki tiket resmi untuk event ini.');
                }

                // Opsi B: Anti-Spam - Cegah pendaftaran baru jika masih ada yang Pending
                $adaPending = PendaftaranLari::where('id_event', $validated['id_event'])
                    ->where('id_runner', $user->id_user)
                    ->where('status_pembayaran', 'Pending')
                    ->exists();

                if ($adaPending) {
                    throw new \Exception('Kamu masih memiliki tagihan yang belum dibayar untuk event ini. Selesaikan atau batalkan terlebih dahulu.');
                }

                if ($kategori->terisi >= $kategori->kuota_peserta) {
                    throw new \Exception('Maaf, kuota untuk kategori ini sudah penuh.');
                }

                $isGratis = ((int) $kategori->harga) === 0;

                // Generate QR Code Token
                $qrCodeToken = hash('sha256', $user->id_user . '-' . $validated['id_event'] . '-' . Str::uuid() . '-' . microtime(true));

                // Opsi A: Nomor BIB awalnya NULL jika berbayar (Pending).
                // Hanya diisi jika event Gratis (langsung Lunas)
                $pendaftaran = PendaftaranLari::create([
                    'id_event'          => $validated['id_event'],
                    'id_runner'         => $user->id_user,
                    'id_kategori'       => $validated['id_kategori'],
                    'bib_number'        => null,
                    'ukuran_jersey'     => $validated['ukuran_jersey'],
                    'qr_code_token'     => $qrCodeToken,
                    'status_pembayaran' => $isGratis ? 'Lunas' : 'Pending',
                    'status_racepack'   => 'Belum Diambil',
                ]);

                // Jika event gratis, langsung terbitkan nomor BIB resmi
                if ($isGratis) {
                    $pendaftaran->assignBibNumber();
                }

                // Format unik order_id: TRX-{id_pendaftaran}-{time()}
                $kodeTransaksi = $isGratis
                    ? 'FREE-' . $pendaftaran->id_pendaftaran . '-' . time()
                    : 'TRX-' . $pendaftaran->id_pendaftaran . '-' . time();

                $pembayaran = PembayaranLari::create([
                    'id_pendaftaran'    => $pendaftaran->id_pendaftaran,
                    'kode_transaksi'    => $kodeTransaksi,
                    'metode_pembayaran' => $isGratis ? 'Gratis' : ($validated['metode_pembayaran'] ?? 'Midtrans Gateway'),
                    'total_bayar'       => $kategori->harga,
                    'payment_type'      => $isGratis ? 'free' : null,
                    'status_transaksi'  => $isGratis ? 'settlement' : 'pending',
                    'waktu_bayar'       => $isGratis ? now() : null,
                ]);

                $kategori->increment('terisi');

                return [
                    'pendaftaran' => $pendaftaran,
                    'pembayaran'  => $pembayaran,
                    'kategori'    => $kategori,
                    'is_gratis'   => $isGratis,
                ];
            });

            $pendaftaran = $result['pendaftaran'];
            $pembayaran  = $result['pembayaran'];
            $kategori    = $result['kategori'];
            $isGratis    = $result['is_gratis'];

            // Event GRATIS: langsung redirect tanpa Midtrans
            if ($isGratis) {
                return redirect()->route('runner.dashboard')
                    ->with('success', 'Pendaftaran berhasil! Event ini GRATIS. E-Ticket & QR Code Anda sudah aktif. BIB: ' . $pendaftaran->bib_number);
            }

            // Event BERBAYAR: request Snap Token ke Midtrans
            $event = EventLari::find($validated['id_event']);
            try {
                $snapToken = $this->requestSnapToken($pembayaran, $kategori, $event, $user, false, $pendaftaran);
            } catch (\Exception $snapEx) {
                Log::error('Midtrans Snap Error: ' . $snapEx->getMessage());
                return redirect()->route('runner.dashboard')
                    ->with('warning', 'Pendaftaran berhasil dibuat, namun token pembayaran belum siap: ' . $snapEx->getMessage() . '. Silakan klik "Lanjut ke Pembayaran" pada tiket Anda.')
                    ->with('error', 'Gagal Midtrans: ' . $snapEx->getMessage());
            }

            return redirect()->route('runner.dashboard')
                ->with('success', 'Pendaftaran berhasil dibuat! Silakan selesaikan pembayaran. Nomor BIB resmi akan diterbitkan otomatis setelah pembayaran lunas.')
                ->with('snap_token', $snapToken);

        } catch (\Exception $e) {
            Log::error('Midtrans Snap Error: ' . $e->getMessage());
            return back()->with('error', 'Pendaftaran Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Buat/ambil ulang Snap Token untuk pendaftaran yang snap_token-nya masih kosong.
     * Dipanggil dari tombol "Lanjut ke Pembayaran" di dashboard ketika token null.
     */
    public function getPaymentToken(int $id)
    {
        try {
            $pendaftaran = PendaftaranLari::with(['pembayaran', 'kategori', 'event'])
                ->where('id_pendaftaran', $id)
                ->where('id_runner', auth()->user()->id_user)
                ->where('status_pembayaran', 'Pending')
                ->firstOrFail();

            $pembayaran = $pendaftaran->pembayaran;

            if (!$pembayaran) {
                return back()->with('error', 'Data pembayaran tidak ditemukan.');
            }

            if (!$pembayaran->snap_token) {
                // Request Snap Token baru ke Midtrans dengan order_id unik
                $snapToken = $this->requestSnapToken(
                    $pembayaran,
                    $pendaftaran->kategori,
                    $pendaftaran->event,
                    auth()->user(),
                    true,
                    $pendaftaran
                );
            } else {
                $snapToken = $pembayaran->snap_token;
            }

            if (!$snapToken) {
                throw new \Exception('Snap Token kosong dari gateway Midtrans.');
            }

            return redirect()->route('runner.dashboard')
                ->with('snap_token', $snapToken)
                ->with('success', 'Silakan selesaikan pembayaran pada pop-up yang muncul.');
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal Midtrans: ' . $e->getMessage());
        }
    }

    /**
     * Helper: request Snap Token ke API Midtrans.
     * Menyimpan token ke kolom snap_token di tabel pembayaran_lari.
     */
    private function requestSnapToken($pembayaran, $kategori, $event, $user, bool $forceNewOrderId = false, $pendaftaran = null): string
    {
        $serverKey    = env('MIDTRANS_SERVER_KEY') ?: config('midtrans.server_key');
        $isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        $snapUrl      = $isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        // Cegah request jika Server Key masih placeholder / belum diisi
        if (empty($serverKey) || str_contains($serverKey, 'YOUR_SERVER_KEY_HERE')) {
            throw new \Exception('MIDTRANS_SERVER_KEY di .env belum diisi atau masih placeholder. Harap masukkan Server Key dari dashboard.midtrans.com.');
        }

        $pendaftaran = $pendaftaran ?? $pembayaran->pendaftaran;
        $idPendaftaran = $pendaftaran ? $pendaftaran->id_pendaftaran : ($pembayaran->id_pendaftaran ?? $pembayaran->id_pembayaran);

        // Format order_id unik: TRX-{id_pendaftaran}-{time()}
        $orderId = 'TRX-' . $idPendaftaran . '-' . time();
        $pembayaran->update(['kode_transaksi' => $orderId]);
        $pembayaran->refresh();

        // Validasi & Normalisasi Payload Midtrans:
        $eventObj = ($pendaftaran && $pendaftaran->event) ? $pendaftaran->event : $event;
        $kategoriObj = ($pendaftaran && $pendaftaran->kategori) ? $pendaftaran->kategori : $kategori;

        $itemPrice = (int) $kategoriObj->harga;
        $grossAmount = max(1, $itemPrice);

        $namaEvent = $eventObj->nama_event ?? 'Event Lari';
        $namaKategori = $kategoriObj->nama_kategori ?? 'Kategori';
        $itemName = \Illuminate\Support\Str::limit($namaEvent . ' - ' . $namaKategori, 45, '...');

        $itemDetails = [
            [
                'id'       => 'KAT-' . ($kategoriObj->id_kategori ?? 1),
                'price'    => $grossAmount,
                'quantity' => 1,
                'name'     => $itemName,
            ]
        ];

        $response = Http::timeout(15)
            ->withBasicAuth($serverKey, '')
            ->withHeaders([
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->post($snapUrl, [
                'transaction_details' => [
                    'order_id'     => $orderId,
                    'gross_amount' => $grossAmount,
                ],
                'customer_details' => [
                    'first_name' => $user->nama,
                    'email'      => $user->email,
                    'phone'      => $user->no_hp ?? '081234567890',
                ],
                'item_details' => $itemDetails,
                'callbacks'    => [
                    'finish'   => route('runner.dashboard'),
                    'unfinish' => route('runner.dashboard'),
                    'error'    => route('runner.dashboard'),
                ],
            ]);

        if ($response->successful()) {
            $snapToken = $response->json('token');
            if (empty($snapToken)) {
                throw new \Exception('Respon Midtrans sukses namun tidak mengembalikan token pembayaran.');
            }
            $pembayaran->update(['snap_token' => $snapToken]);
            return $snapToken;
        }

        $errorMessages = $response->json('error_messages');
        $errorBody = !empty($errorMessages)
            ? (is_array($errorMessages) ? implode(', ', (array) $errorMessages) : (string) $errorMessages)
            : $response->body();

        throw new \Exception('Midtrans API [' . $response->status() . ']: ' . $errorBody);
    }

    /**
     * Batalkan pendaftaran event (soft-cancel + kembalikan kuota).
     * Runner hanya bisa membatalkan tiket yang masih berstatus Pending.
     */
    public function cancelRegistration(int $id)
    {
        $pendaftaran = PendaftaranLari::where('id_pendaftaran', $id)
            ->where('id_runner', auth()->user()->id_user)
            ->firstOrFail();

        if ($pendaftaran->status_pembayaran !== 'Pending') {
            return back()->with('error', 'Hanya tiket dengan status Pending yang dapat dibatalkan.');
        }

        try {
            DB::transaction(function () use ($pendaftaran) {
                $pendaftaran->update([
                    'status_pembayaran' => 'Gagal',
                    'bib_number'        => null,
                ]);
                $pendaftaran->pembayaran()->update(['status_transaksi' => 'cancel']);

                $kategori = KategoriLari::where('id_kategori', $pendaftaran->id_kategori)
                    ->lockForUpdate()
                    ->first();

                if ($kategori && $kategori->terisi > 0) {
                    $kategori->decrement('terisi');
                }
            });

            return redirect()->route('runner.dashboard')
                ->with('success', 'Pendaftaran berhasil dibatalkan. Kuota telah dikembalikan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan Invoice / Bukti Pembayaran Resmi.
     */
    public function invoice(int $id)
    {
        $pendaftaran = PendaftaranLari::with(['event', 'kategori', 'pembayaran', 'runner'])
            ->where('id_pendaftaran', $id)
            ->where('id_runner', auth()->user()->id_user)
            ->firstOrFail();

        if ($pendaftaran->status_pembayaran !== 'Lunas') {
            return redirect()->route('runner.dashboard')
                ->with('error', 'Invoice hanya tersedia untuk tiket yang sudah berstatus Lunas.');
        }

        return view('runner.invoice', compact('pendaftaran'));
    }

    /**
     * Generate prefix BIB berdasarkan nama kategori.
     */
    private function generateBibPrefix(string $namaKategori): string
    {
        if (preg_match('/(\d+K)/i', $namaKategori, $matches)) {
            return strtoupper($matches[1]);
        }
        if (stripos($namaKategori, 'half marathon') !== false) {
            return 'HM';
        }
        if (stripos($namaKategori, 'marathon') !== false) {
            return 'FM';
        }
        $words  = explode(' ', $namaKategori);
        $prefix = '';
        foreach ($words as $word) {
            $prefix .= strtoupper(substr($word, 0, 1));
        }
        return $prefix ?: 'RF';
    }
}
