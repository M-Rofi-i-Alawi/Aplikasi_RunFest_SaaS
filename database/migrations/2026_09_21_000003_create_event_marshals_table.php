<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_marshals', function (Blueprint $table) {
            $table->id('id_marshal_assignment');
            $table->unsignedBigInteger('id_event');
            $table->unsignedBigInteger('id_user_marshal');
            $table->string('pin_akses_scanner')->nullable()->comment('PIN verifikasi scanner lapangan');
            $table->timestamps();

            // Foreign Keys
            $table->foreign('id_event')
                  ->references('id_event')
                  ->on('event_lari')
                  ->cascadeOnDelete();

            $table->foreign('id_user_marshal')
                  ->references('id_user')
                  ->on('users')
                  ->cascadeOnDelete();

            // Satu marshal hanya dapat ditugaskan sekali per event
            $table->unique(['id_event', 'id_user_marshal'], 'unique_marshal_per_event');

            // Index untuk query performa
            $table->index('id_event');
            $table->index('id_user_marshal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_marshals');
    }
};
