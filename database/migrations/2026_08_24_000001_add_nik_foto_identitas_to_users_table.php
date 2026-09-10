<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom NIK dan foto_identitas ke tabel users
     * untuk fitur validasi identitas anti-joki.
     *
     * - nik: Nomor Induk Kependudukan (16 digit) atau NISN pelajar. Unik & nullable.
     * - foto_identitas: Path file foto KTP/KIA/Kartu Pelajar di local storage.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nik', 20)->nullable()->unique()->after('password');
            $table->string('foto_identitas')->nullable()->after('nik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['nik']);
            $table->dropColumn(['nik', 'foto_identitas']);
        });
    }
};
