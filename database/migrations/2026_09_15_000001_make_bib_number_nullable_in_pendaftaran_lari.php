<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Mengubah bib_number menjadi nullable agar tiket Pending/Gagal
     * tidak membakar nomor BIB resmi sebelum pembayaran Lunas.
     */
    public function up(): void
    {
        Schema::table('pendaftaran_lari', function (Blueprint $table) {
            $table->string('bib_number', 20)->nullable()->change();
        });

        // Bersihkan bib_number dari transaksi yang sudah Gagal di masa lalu
        DB::table('pendaftaran_lari')
            ->where('status_pembayaran', 'Gagal')
            ->update(['bib_number' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_lari', function (Blueprint $table) {
            $table->string('bib_number', 20)->nullable(false)->change();
        });
    }
};
