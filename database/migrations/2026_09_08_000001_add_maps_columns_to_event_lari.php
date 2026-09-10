<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom GPS & Google Maps ke tabel event_lari.
     * Mendukung embed peta venue dan tautan navigasi instan.
     */
    public function up(): void
    {
        Schema::table('event_lari', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('lokasi_venue');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->text('google_maps_url')->nullable()->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('event_lari', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'google_maps_url']);
        });
    }
};
