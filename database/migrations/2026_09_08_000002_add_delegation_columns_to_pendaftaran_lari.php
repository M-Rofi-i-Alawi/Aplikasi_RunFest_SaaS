<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom surat kuasa / pengambilan diwakilkan ke pendaftaran_lari.
     * Mendukung alur: Marshal menginput data perwakilan saat RPC.
     */
    public function up(): void
    {
        Schema::table('pendaftaran_lari', function (Blueprint $table) {
            $table->boolean('is_diwakilkan')->default(false)->after('status_racepack');
            $table->string('nama_pengambil')->nullable()->after('is_diwakilkan');
            $table->string('nik_pengambil', 16)->nullable()->after('nama_pengambil');
            $table->text('catatan_rpc')->nullable()->after('nik_pengambil');
        });
    }

    public function down(): void
    {
        Schema::table('pendaftaran_lari', function (Blueprint $table) {
            $table->dropColumn(['is_diwakilkan', 'nama_pengambil', 'nik_pengambil', 'catatan_rpc']);
        });
    }
};
