<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel event_lari: menyimpan data event lomba lari yang dibuat oleh Organizer.
     */
    public function up(): void
    {
        Schema::create('event_lari', function (Blueprint $table) {
            $table->bigIncrements('id_event');
            $table->unsignedBigInteger('id_organizer');
            $table->string('nama_event');
            $table->string('slug')->unique();
            $table->date('tanggal_event');
            $table->string('lokasi_venue');
            $table->date('tanggal_rpc_mulai')->comment('Tanggal Race Pack Collection dimulai');
            $table->date('tanggal_rpc_selesai')->comment('Tanggal Race Pack Collection berakhir');
            $table->enum('status_event', ['Draft', 'Publikasi', 'Selesai', 'Dibatalkan'])->default('Draft');
            $table->text('deskripsi')->nullable();
            $table->string('banner_url')->nullable();
            $table->timestamps();

            $table->foreign('id_organizer')
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
        Schema::dropIfExists('event_lari');
    }
};
