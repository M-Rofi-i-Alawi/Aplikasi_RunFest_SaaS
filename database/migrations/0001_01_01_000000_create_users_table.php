<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel users: menyimpan data semua pengguna (SuperAdmin, Organizer, Runner, Marshal).
     * Mendukung dual auth: Google OAuth (google_id) dan Manual (password), keduanya nullable.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id_user');
            $table->string('google_id')->nullable()->unique();
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('password')->nullable(); // Nullable untuk Google OAuth users
            $table->string('no_hp', 20)->nullable();
            $table->string('golongan_darah', 5)->nullable();
            $table->string('kontak_darurat')->nullable();
            $table->enum('role', ['SuperAdmin', 'Organizer', 'Runner', 'Marshal'])->default('Runner');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
