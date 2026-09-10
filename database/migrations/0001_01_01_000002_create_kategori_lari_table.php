<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel kategori_lari: menyimpan kategori lomba (5K, 10K, 21K, dll) per event.
     */
    public function up(): void
    {
        Schema::create('kategori_lari', function (Blueprint $table) {
            $table->bigIncrements('id_kategori');
            $table->unsignedBigInteger('id_event');
            $table->string('nama_kategori'); // Contoh: 5K Fun Run, 10K Competitive, Half Marathon
            $table->decimal('harga', 12, 2);
            $table->integer('kuota_peserta');
            $table->integer('terisi')->default(0);
            $table->timestamps();

            $table->foreign('id_event')
                  ->references('id_event')
                  ->on('event_lari')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_lari');
    }
};
