<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fix: Ubah constraint bib_number dari UNIQUE global menjadi UNIQUE per event.
     * BIB seperti '5K-0001' wajar muncul di beberapa event berbeda,
     * tapi harus tetap unik dalam satu event yang sama.
     */
    public function up(): void
    {
        Schema::table('pendaftaran_lari', function (Blueprint $table) {
            // Hapus unique global pada bib_number
            $table->dropUnique(['bib_number']);

            // Tambahkan composite unique: BIB unik PER EVENT
            $table->unique(['id_event', 'bib_number'], 'unique_bib_per_event');
        });
    }

    /**
     * Reverse: Kembalikan ke unique global bib_number.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_lari', function (Blueprint $table) {
            $table->dropUnique('unique_bib_per_event');
            $table->unique('bib_number');
        });
    }
};
