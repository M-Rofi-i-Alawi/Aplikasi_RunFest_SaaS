<?php

namespace App\Http\Controllers;

use App\Models\KategoriLari;
use App\Models\PembayaranLari;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentCallbackController extends Controller
{
    /**
     * Midtrans Webhook Notification Handler.
     * Endpoint: POST /api/payment/webhook
     *
     * Menerima notifikasi asinkron dari Midtrans setelah pembayaran diproses.
     * Validasi keamanan via SHA-512 Signature Key.
     */
    public function handle(Request $request): JsonResponse
    {
        $serverKey = config('midtrans.server_key', env('MIDTRANS_SERVER_KEY'));
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        // Validasi Signature Key
        if ($hashed !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $transactionStatus = $request->transaction_status;
        $fraudStatus       = $request->fraud_status;
        $orderId           = $request->order_id;
        $paymentType       = $request->payment_type;

        $pembayaran = PembayaranLari::where('kode_transaksi', $orderId)->first();

        if (!$pembayaran) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $pendaftaran = $pembayaran->pendaftaran;

        if ($pendaftaran) {
            // Skenario A: Sukses Bayar (settlement / capture accept)
            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                if ($fraudStatus == 'accept' || empty($fraudStatus)) {
                    $pembayaran->update([
                        'status_transaksi'  => 'settlement',
                        'metode_pembayaran' => $paymentType ?? $pembayaran->metode_pembayaran ?? 'Midtrans Gateway',
                        'payment_type'      => $paymentType ?? $pembayaran->payment_type,
                        'waktu_bayar'       => now(),
                        'payload_response'  => $request->all(),
                    ]);

                    $pendaftaran->update([
                        'status_pembayaran' => 'Lunas',
                    ]);

                    // Opsi A: Terbitkan nomor BIB resmi saat pembayaran terkonfirmasi LUNAS
                    $pendaftaran->assignBibNumber();
                }
            }
            // Skenario B: Dibatalkan / Kedaluwarsa / Ditolak (cancel / deny / expire)
            else if (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                $pembayaran->update([
                    'status_transaksi' => $transactionStatus,
                    'payment_type'     => $paymentType ?? $pembayaran->payment_type,
                    'payload_response' => $request->all(),
                ]);

                // Hanya rollback jika status pendaftaran sebelumnya masih Pending
                if ($pendaftaran->status_pembayaran === 'Pending') {
                    $pendaftaran->update([
                        'status_pembayaran' => 'Gagal',
                        'bib_number'        => null,
                    ]);

                    // Rollback kuota kategori
                    if ($pendaftaran->kategori && $pendaftaran->kategori->terisi > 0) {
                        $pendaftaran->kategori->decrement('terisi');
                    }
                }
            }
            // Skenario C: Menunggu Pembayaran (pending)
            else if ($transactionStatus == 'pending') {
                $pembayaran->update([
                    'status_transaksi' => 'pending',
                    'payment_type'     => $paymentType ?? $pembayaran->payment_type,
                    'payload_response' => $request->all(),
                ]);
            }
        }

        return response()->json(['status' => 'success']);
    }
}
