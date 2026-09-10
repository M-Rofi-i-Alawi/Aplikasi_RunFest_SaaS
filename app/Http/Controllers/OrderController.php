<?php

namespace App\Http\Controllers;

use App\Models\EventLari;
use App\Models\KategoriLari;
use App\Models\PembayaranLari;
use App\Models\PendaftaranLari;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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

        // Cek pendaftaran aktif
        $sudahDaftar = PendaftaranLari::where('id_event', $event->id_event)
            ->where('id_runner', $user->id_user)
            ->whereNotIn('status_pembayaran', ['Gagal'])
            ->exists();

        if ($sudahDaftar) {
            return redirect()->route('runner.dashboard')
                ->with('warning', 'Anda sudah terdaftar di event ini.');
        }

        return view('runner.register-event', compact('event'));
    }

    /**
     * Proses pendaftaran event.
     * Menggunakan DB::transaction() dan lockForUpdate() untuk mencegah race condition pada kuota & BIB.
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

                $sudahDaftar = PendaftaranLari::where('id_event', $validated['id_event'])
                    ->where('id_runner', $user->id_user)
                    ->whereNotIn('status_pembayaran', ['Gagal'])
                    ->exists();

                if ($sudahDaftar) {
                    throw new \Exception('Anda sudah terdaftar di event ini.');
                }

                if ($kategori->terisi >= $kategori->kuota_peserta) {
                    throw new \Exception('Maaf, kuota untuk kategori ini sudah penuh.');
                }

                // Generate BIB Number
                $prefix = $this->generateBibPrefix($kategori->nama_kategori);
                $maxBib = PendaftaranLari::where('id_event', $validated['id_event'])
                    ->where('bib_number', 'like', $prefix . '-%')
                    ->selectRaw("MAX(CAST(SUBSTRING_INDEX(bib_number, '-', -1) AS UNSIGNED)) as max_num")
                    ->value('max_num');
                $nextNumber = ($maxBib ?? 0) + 1;
                $bibNumber  = $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

                // Generate QR Code Token
                $qrCodeToken = hash('sha256', $user->id_user . '-' . $validated['id_event'] . '-' . Str::uuid() . '-' . microtime(true));

                $isGratis = ((int) $kategori->harga) === 0;
                $kodeTransaksi = $isGratis
                    ? 'FREE-' . strtoupper(Str::random(3)) . '-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT)
                    : 'TRX-' . strtoupper(Str::random(3)) . '-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

                $pendaftaran = PendaftaranLari::create([
                    'id_event'          => $validated['id_event'],
                    'id_runner'         => $user->id_user,
                    'id_kategori'       => $validated['id_kategori'],
                    'bib_number'        => $bibNumber,
                    'ukuran_jersey'     => $validated['ukuran_jersey'],
                    'qr_code_token'     => $qrCodeToken,
                    'status_pembayaran' => $isGratis ? 'Lunas' : 'Pending',
                    'status_racepack'   => 'Belum Diambil',
                ]);

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
            $event     = EventLari::find($validated['id_event']);
            $snapToken = $this->requestSnapToken($pembayaran, $kategori, $event, $user);

            return redirect()->route('runner.dashboard')
                ->with('success', 'Pendaftaran berhasil! BIB: ' . $pendaftaran->bib_number . '. Silakan selesaikan pembayaran.')
                ->with('snap_token', $snapToken);

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Buat/ambil ulang Snap Token untuk pendaftaran yang snap_token-nya masih kosong.
     * Dipanggil dari tombol "Lanjut ke Pembayaran" di dashboard ketika token null.
     */
    public function getPaymentToken(int $id)
    {
        $pendaftaran = PendaftaranLari::where('id_pendaftaran', $id)
            ->where('id_runner', auth()->user()->id_user)
            ->where('status_pembayaran', 'Pending')
            ->with(['pembayaran', 'kategori', 'event'])
            ->firstOrFail();

        $pembayaran = $pendaftaran->pembayaran;

        if (!$pembayaran->snap_token) {
            $snapToken = $this->requestSnapToken(
                $pembayaran,
                $pendaftaran->kategori,
                $pendaftaran->event,
                auth()->user()
            );
        } else {
            $snapToken = $pembayaran->snap_token;
        }

        return redirect()->route('runner.dashboard')
            ->with('snap_token', $snapToken)
            ->with('success', 'Silakan selesaikan pembayaran pada pop-up yang muncul.');
    }

    /**
     * Helper: request Snap Token ke API Midtrans.
     * Menyimpan token ke kolom snap_token di tabel pembayaran_lari.
     */
    private function requestSnapToken($pembayaran, $kategori, $event, $user): ?string
    {
        $serverKey = env('MIDTRANS_SERVER_KEY') ?: config('midtrans.server_key');
        $isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        $snapUrl = $isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        try {
            $response = Http::withBasicAuth($serverKey, '')
                ->withHeaders([
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post($snapUrl, [
                    'transaction_details' => [
                        'order_id'     => $pembayaran->kode_transaksi,
                        'gross_amount' => (int) $kategori->harga,
                    ],
                    'customer_details' => [
                        'first_name' => $user->nama,
                        'email'      => $user->email,
                        'phone'      => $user->no_hp ?? '081234567890',
                    ],
                    'item_details' => [
                        [
                            'id'       => (string) $kategori->id_kategori,
                            'price'    => (int) $kategori->harga,
                            'quantity' => 1,
                            'name'     => 'Tiket ' . substr($kategori->nama_kategori . ' ' . ($event->nama_event ?? ''), 0, 45),
                        ],
                    ],
                ]);

            if ($response->successful()) {
                $snapToken = $response->json('token');
                $pembayaran->update(['snap_token' => $snapToken]);
                return $snapToken;
            }

            logger('Midtrans Error: ' . $response->body());
        } catch (\Exception $e) {
            logger('Midtrans Connection Exception: ' . $e->getMessage());
        }

        return null;
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
                $pendaftaran->update(['status_pembayaran' => 'Gagal']);
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
