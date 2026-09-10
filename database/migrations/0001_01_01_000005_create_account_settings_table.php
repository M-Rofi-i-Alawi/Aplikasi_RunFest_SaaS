<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom ukuran_jersey_default di tabel users
     * dan membuat tabel rekening_organizer untuk payout organizer.
     */
    public function up(): void
    {
        // Tambah kolom ukuran jersey default di users
        Schema::table('users', function (Blueprint $table) {
            $table->enum('ukuran_jersey_default', ['S', 'M', 'L', 'XL', 'XXL'])
                ->nullable()
                ->after('kontak_darurat');
        });

        // Tabel rekening organizer untuk info payout
        Schema::create('rekening_organizer', function (Blueprint $table) {
            $table->bigIncrements('id_rekening');
            $table->unsignedBigInteger('id_user');
            $table->enum('tipe_rekening', ['Bank Transfer', 'E-Wallet'])->default('Bank Transfer');
            $table->string('nama_bank', 100); // Nama bank atau nama e-wallet
            $table->string('nomor_rekening', 50);
            $table->string('nama_pemilik'); // Nama pemilik rekening
            $table->boolean('is_primary')->default(false); // Rekening utama
            $table->timestamps();

            $table->foreign('id_user')
                ->references('id_user')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekening_organizer');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ukuran_jersey_default');
        });
    }
};
