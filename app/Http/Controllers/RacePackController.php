<?php

namespace App\Http\Controllers;

use App\Models\PendaftaranLari;
use App\Models\PembayaranLari;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RacePackController extends Controller
{
    /**
     * Tampilkan halaman scanner RPC untuk Marshal.
     */
    public function scanPage()
    {
        return view('marshal.scanner');
    }

    /**
     * API Endpoint: POST /api/racepack/scan
     *
     * Mencari data tiket berdasarkan QR Token, Nomor BIB, atau Kode Transaksi.
     * TIDAK langsung mengubah status — hanya mengembalikan data untuk preview di modal konfirmasi.
     *
     * Respons:
     * - HIJAU (200): Tiket valid & lunas, siap untuk konfirmasi serah terima
     * - KUNING (409): Race pack sudah pernah diambil sebelumnya
     * - MERAH (422): Input tidak ditemukan atau pembayaran belum lunas
     */
    public function scan(Request $request): JsonResponse
    {
        $request->validate([
            'qr_code_token' => ['required', 'string', 'min:3', 'max:100'],
        ]);

        $input = trim($request->input('qr_code_token'));

        // ============================================================
        // STRATEGI 1: Cari berdasarkan QR Token (SHA-256, 64 char)
        // ============================================================
        $pendaftaran = PendaftaranLari::where('qr_code_token', $input)
            ->with(['runner', 'event', 'kategori', 'pembayaran'])
            ->first();

        // ============================================================
        // STRATEGI 2: Cari berdasarkan Nomor BIB (mis: 5K-0001)
        // ============================================================
        if (!$pendaftaran) {
            $pendaftaran = PendaftaranLari::where('bib_number', strtoupper($input))
                ->with(['runner', 'event', 'kategori', 'pembayaran'])
                ->first();
        }

        // ============================================================
        // STRATEGI 3: Cari berdasarkan Kode Transaksi via relasi pembayaran
        // ============================================================
        if (!$pendaftaran) {
            $pembayaran = PembayaranLari::where('kode_transaksi', strtoupper($input))
                ->with(['pendaftaran.runner', 'pendaftaran.event', 'pendaftaran.kategori'])
                ->first();

            if ($pembayaran) {
                $pendaftaran = $pembayaran->pendaftaran;
            }
        }

        // ============================================================
        // INPUT TIDAK DITEMUKAN → MERAH
        // ============================================================
        if (!$pendaftaran) {
            return response()->json([
                'status'    => 'error',
                'indicator' => 'merah',
                'message'   => 'Data tidak ditemukan. Coba masukkan QR Token, Nomor BIB (mis: 5K-0001), atau Kode TRX yang tercetak di tiket.',
            ], 422);
        }

        // Siapkan data respons lengkap
        $responseData = [
            'id_pendaftaran'      => $pendaftaran->id_pendaftaran,
            'bib_number'          => $pendaftaran->bib_number,
            'nama_runner'         => $pendaftaran->runner->nama,
            'nik'                 => $pendaftaran->runner->nik,
            'foto_identitas_url'  => $pendaftaran->runner->foto_identitas ? Storage::url($pendaftaran->runner->foto_identitas) : null,
            'ukuran_jersey'       => $pendaftaran->ukuran_jersey,
            'nama_event'          => $pendaftaran->event->nama_event,
            'kategori'            => $pendaftaran->kategori->nama_kategori,
            'status_pembayaran'   => $pendaftaran->status_pembayaran,
            'status_racepack'     => $pendaftaran->status_racepack,
        ];

        // PEMBAYARAN BELUM LUNAS → MERAH
        if ($pendaftaran->status_pembayaran !== 'Lunas') {
            return response()->json([
                'status'    => 'error',
                'indicator' => 'merah',
                'message'   => 'Pembayaran belum lunas. Status saat ini: ' . $pendaftaran->status_pembayaran,
                'data'      => $responseData,
            ], 422);
        }

        // RACE PACK SUDAH DIAMBIL → KUNING
        if ($pendaftaran->status_racepack === 'Sudah Diambil') {
            $responseData['waktu_pengambilan'] = $pendaftaran->waktu_pengambilan_racepack?->format('d M Y, H:i');
            $responseData['is_diwakilkan'] = $pendaftaran->is_diwakilkan;
            $responseData['nama_pengambil'] = $pendaftaran->nama_pengambil;
            $responseData['nik_pengambil'] = $pendaftaran->nik_pengambil;

            return response()->json([
                'status'    => 'warning',
                'indicator' => 'kuning',
                'message'   => 'Race pack sudah pernah diambil pada ' . ($pendaftaran->waktu_pengambilan_racepack?->format('d M Y, H:i') ?? '-') . '.',
                'data'      => $responseData,
            ], 409);
        }

        // ============================================================
        // TIKET VALID & LUNAS → HIJAU (preview, belum confirm)
        // Return data untuk ditampilkan di modal konfirmasi Marshal.
        // ============================================================
        return response()->json([
            'status'    => 'success',
            'indicator' => 'hijau',
            'message'   => 'Tiket VALID & LUNAS! Cocokkan identitas fisik peserta, lalu konfirmasi serah terima racepack.',
            'data'      => $responseData,
        ], 200);
    }

    /**
     * API Endpoint: POST /api/racepack/confirm
     *
     * Konfirmasi serah terima racepack oleh Marshal.
     * Mendukung pengambilan diwakilkan (surat kuasa) dengan input nama & NIK pengambil.
     */
    public function confirmHandover(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_pendaftaran' => ['required', 'integer', 'exists:pendaftaran_lari,id_pendaftaran'],
            'is_diwakilkan'  => ['required', 'boolean'],
            'nama_pengambil' => ['required_if:is_diwakilkan,true', 'nullable', 'string', 'max:255'],
            'nik_pengambil'  => ['required_if:is_diwakilkan,true', 'nullable', 'string', 'size:16'],
            'catatan_rpc'    => ['nullable', 'string', 'max:500'],
        ], [
            'nama_pengambil.required_if' => 'Nama pengambil wajib diisi jika pengambilan diwakilkan.',
            'nik_pengambil.required_if'  => 'NIK pengambil wajib diisi jika pengambilan diwakilkan.',
            'nik_pengambil.size'         => 'NIK pengambil harus tepat 16 digit.',
        ]);

        $pendaftaran = PendaftaranLari::with(['runner', 'event', 'kategori'])
            ->findOrFail($validated['id_pendaftaran']);

        // Validasi: Tiket harus Lunas dan racepack belum diambil
        if ($pendaftaran->status_pembayaran !== 'Lunas') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Pembayaran belum lunas. Tidak dapat menyerahkan racepack.',
            ], 422);
        }

        if ($pendaftaran->status_racepack === 'Sudah Diambil') {
            return response()->json([
                'status'  => 'warning',
                'message' => 'Race pack sudah pernah diambil pada ' . ($pendaftaran->waktu_pengambilan_racepack?->format('d M Y, H:i') ?? '-'),
            ], 409);
        }

        // ============================================================
        // KONFIRMASI SERAH TERIMA
        // ============================================================
        try {
            DB::transaction(function () use ($pendaftaran, $validated) {
                $pendaftaran->update([
                    'status_racepack'            => 'Sudah Diambil',
                    'waktu_pengambilan_racepack'  => now(),
                    'is_diwakilkan'              => $validated['is_diwakilkan'],
                    'nama_pengambil'             => $validated['is_diwakilkan'] ? $validated['nama_pengambil'] : null,
                    'nik_pengambil'              => $validated['is_diwakilkan'] ? $validated['nik_pengambil'] : null,
                    'catatan_rpc'                => $validated['catatan_rpc'] ?? null,
                ]);
            });

            return response()->json([
                'status'    => 'success',
                'indicator' => 'hijau',
                'message'   => 'Racepack berhasil diserahkan!' . ($validated['is_diwakilkan'] ? ' (Diwakilkan oleh: ' . $validated['nama_pengambil'] . ')' : ''),
                'data'      => [
                    'id_pendaftaran'    => $pendaftaran->id_pendaftaran,
                    'bib_number'        => $pendaftaran->bib_number,
                    'nama_runner'       => $pendaftaran->runner->nama,
                    'ukuran_jersey'     => $pendaftaran->ukuran_jersey,
                    'nama_event'        => $pendaftaran->event->nama_event,
                    'kategori'          => $pendaftaran->kategori->nama_kategori,
                    'status_pembayaran' => $pendaftaran->status_pembayaran,
                    'status_racepack'   => 'Sudah Diambil',
                    'waktu_pengambilan' => now()->format('d M Y, H:i'),
                    'is_diwakilkan'     => $validated['is_diwakilkan'],
                    'nama_pengambil'    => $validated['is_diwakilkan'] ? $validated['nama_pengambil'] : null,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal konfirmasi: ' . $e->getMessage(),
            ], 500);
        }
    }
}
