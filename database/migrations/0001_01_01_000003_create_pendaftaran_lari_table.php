<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel pendaftaran_lari: mencatat registrasi peserta ke event.
     * Menyimpan bib_number unik, qr_code_token untuk scan RPC, dan status pembayaran/racepack.
     */
    public function up(): void
    {
        Schema::create('pendaftaran_lari', function (Blueprint $table) {
            $table->bigIncrements('id_pendaftaran');
            $table->unsignedBigInteger('id_event');
            $table->unsignedBigInteger('id_runner');
            $table->unsignedBigInteger('id_kategori');
            $table->string('bib_number', 20)->unique();
            $table->enum('ukuran_jersey', ['S', 'M', 'L', 'XL', 'XXL']);
            $table->string('qr_code_token', 64)->unique()->comment('SHA-256 hash token untuk scan RPC');
            $table->enum('status_pembayaran', ['Pending', 'Lunas', 'Gagal'])->default('Pending');
            $table->enum('status_racepack', ['Belum Diambil', 'Sudah Diambil'])->default('Belum Diambil');
            $table->timestamp('waktu_pengambilan_racepack')->nullable();
            $table->timestamps();

            $table->foreign('id_event')
                  ->references('id_event')
                  ->on('event_lari')
                  ->onDelete('cascade');

            $table->foreign('id_runner')
                  ->references('id_user')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('id_kategori')
                  ->references('id_kategori')
                  ->on('kategori_lari')
                  ->onDelete('cascade');

            // Satu runner hanya bisa mendaftar 1x per event
            $table->unique(['id_event', 'id_runner'], 'unique_runner_per_event');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_lari');
    }
};
