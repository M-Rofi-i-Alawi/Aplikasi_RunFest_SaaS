<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran_lari', function (Blueprint $table) {
            $table->string('snap_token')->nullable()->after('kode_transaksi');
            $table->string('payment_type')->nullable()->after('metode_pembayaran');
            $table->enum('status_transaksi', ['pending', 'settlement', 'expire', 'cancel', 'deny'])->default('pending')->after('total_bayar');
            $table->json('payload_response')->nullable()->after('waktu_bayar');
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran_lari', function (Blueprint $table) {
            $table->dropColumn(['snap_token', 'payment_type', 'status_transaksi', 'payload_response']);
        });
    }
};