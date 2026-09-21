<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_lari', function (Blueprint $table) {
            // Contact Person (Narahubung) fields
            $table->string('nama_cp')->nullable()->after('deskripsi');
            $table->string('no_wa_cp')->nullable()->after('nama_cp');
            $table->string('email_cp')->nullable()->after('no_wa_cp');

            // Update enum status_event to include 'Moderasi' and 'Dibatalkan'
            // MySQL: drop and re-add with new values
        });

        // Update status_event enum via raw SQL for MySQL compatibility
        DB::statement("ALTER TABLE event_lari MODIFY COLUMN status_event ENUM('Draft','Moderasi','Publikasi','Selesai','Dibatalkan') NOT NULL DEFAULT 'Draft'");
    }

    public function down(): void
    {
        Schema::table('event_lari', function (Blueprint $table) {
            $table->dropColumn(['nama_cp', 'no_wa_cp', 'email_cp']);
        });

        DB::statement("ALTER TABLE event_lari MODIFY COLUMN status_event ENUM('Draft','Publikasi','Selesai') NOT NULL DEFAULT 'Draft'");
    }
};
