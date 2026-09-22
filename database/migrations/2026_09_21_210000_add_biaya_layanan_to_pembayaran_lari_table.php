<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran_lari', function (Blueprint $table) {
            $table->decimal('harga_tiket', 12, 2)->default(0)->after('snap_token');
            $table->decimal('biaya_layanan', 12, 2)->default(0)->after('harga_tiket');
        });

        // Sinkronkan data existing: harga_tiket = total_bayar, biaya_layanan = 0
        DB::table('pembayaran_lari')->update([
            'harga_tiket' => DB::raw('total_bayar'),
            'biaya_layanan' => 0,
        ]);
    }

    public function down(): void
    {
        Schema::table('pembayaran_lari', function (Blueprint $table) {
            $table->dropColumn(['harga_tiket', 'biaya_layanan']);
        });
    }
};
