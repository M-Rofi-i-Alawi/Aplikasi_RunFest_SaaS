<?php

namespace App\Http\Controllers;

use App\Models\KategoriLari;
use App\Models\PembayaranLari;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $hashed = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        // Validasi Signature Key — Tolak request tidak sah
        if ($hashed !== $request->signature_key) {
            Log::warning('Midtrans Webhook: Invalid signature.', ['order_id' => $request->order_id]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $transactionStatus = $request->transaction_status;
        $fraudStatus       = $request->fraud_status;
        $orderId           = $request->order_id;
        $paymentType       = $request->payment_type;

        $pembayaran = PembayaranLari::where('kode_transaksi', $orderId)->first();

        if (!$pembayaran) {
            Log::warning('Midtrans Webhook: Transaction not found.', ['order_id' => $orderId]);
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $pendaftaran = $pembayaran->pendaftaran;

        if (!$pendaftaran) {
            Log::warning('Midtrans Webhook: Pendaftaran not found for payment.', ['kode_transaksi' => $orderId]);
            return response()->json(['message' => 'Registration not found'], 404);
        }

        try {
            DB::transaction(function () use ($pembayaran, $pendaftaran, $transactionStatus, $fraudStatus, $paymentType, $request) {

                // ================================================================
                // SKENARIO A: Sukses Bayar (settlement / capture accept)
                // Auto-terbitkan BIB resmi peserta.
                // ================================================================
                if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
                    if ($fraudStatus === 'accept' || empty($fraudStatus)) {

                        // Jangan proses ulang jika sudah Lunas (idempotency)
                        if ($pendaftaran->status_pembayaran === 'Lunas') {
                            return;
                        }

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

                        // Terbitkan nomor BIB resmi dan aktifkan QR Code Token
                        $pendaftaran->assignBibNumber();

                        Log::info('Midtrans Webhook: Payment settled.', [
                            'order_id'      => $pembayaran->kode_transaksi,
                            'id_pendaftaran'=> $pendaftaran->id_pendaftaran,
                            'bib_number'    => $pendaftaran->fresh()->bib_number,
                        ]);
                    }
                }

                // ================================================================
                // SKENARIO B: Dibatalkan / Kedaluwarsa / Ditolak
                // Rollback kuota kategori peserta.
                // ================================================================
                elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {

                    $pembayaran->update([
                        'status_transaksi' => $transactionStatus,
                        'payment_type'     => $paymentType ?? $pembayaran->payment_type,
                        'payload_response' => $request->all(),
                    ]);

                    // Hanya rollback kuota jika status pendaftaran sebelumnya masih Pending
                    if ($pendaftaran->status_pembayaran === 'Pending') {
                        $pendaftaran->update([
                            'status_pembayaran' => 'Gagal',
                            'bib_number'        => null,
                        ]);

                        // Rollback kuota kategori dengan pessimistic locking
                        $kategori = KategoriLari::where('id_kategori', $pendaftaran->id_kategori)
                                                ->lockForUpdate()
                                                ->first();

                        if ($kategori && $kategori->terisi > 0) {
                            $kategori->decrement('terisi');
                        }

                        Log::info('Midtrans Webhook: Payment failed, quota restored.', [
                            'order_id'      => $pembayaran->kode_transaksi,
                            'status'        => $transactionStatus,
                            'id_pendaftaran'=> $pendaftaran->id_pendaftaran,
                        ]);
                    }
                }

                // ================================================================
                // SKENARIO C: Menunggu Pembayaran (pending)
                // ================================================================
                elseif ($transactionStatus === 'pending') {
                    $pembayaran->update([
                        'status_transaksi' => 'pending',
                        'payment_type'     => $paymentType ?? $pembayaran->payment_type,
                        'payload_response' => $request->all(),
                    ]);
                }
            });
        } catch (\Exception $e) {
            Log::error('Midtrans Webhook: Exception during processing.', [
                'order_id' => $orderId,
                'error'    => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Internal server error'], 500);
        }

        return response()->json(['status' => 'success']);
    }
}
