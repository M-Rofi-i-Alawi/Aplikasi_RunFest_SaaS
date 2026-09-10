<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop unique_runner_per_event constraint agar runner bisa daftar ulang
     * setelah pendaftaran sebelumnya dibatalkan (status 'Gagal').
     * Pengecekan duplikat aktif tetap ditangani di level aplikasi (OrderController)
     * dengan filter whereNotIn('status_pembayaran', ['Gagal']).
     */
    public function up(): void
    {
        Schema::table('pendaftaran_lari', function (Blueprint $table) {
            $table->dropUnique('unique_runner_per_event');
        });
    }

    public function down(): void
    {
        Schema::table('pendaftaran_lari', function (Blueprint $table) {
            $table->unique(['id_event', 'id_runner'], 'unique_runner_per_event');
        });
    }
};
