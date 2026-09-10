<?php

namespace Database\Seeders;

use App\Models\EventLari;
use App\Models\KategoriLari;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Membuat data awal: SuperAdmin, Organizer, sample Event & Kategori, Runner, Marshal.
     */
    public function run(): void
    {
        // ============================================================
        // SUPER ADMIN
        // ============================================================
        User::create([
            'nama' => 'Super Admin',
            'email' => 'admin@runfest.test',
            'password' => Hash::make('password'),
            'role' => 'SuperAdmin',
            'no_hp' => '081200000001',
        ]);

        // ============================================================
        // ORGANIZER
        // ============================================================
        $organizer = User::create([
            'nama' => 'Budi Santoso',
            'email' => 'organizer@runfest.test',
            'password' => Hash::make('password'),
            'role' => 'Organizer',
            'no_hp' => '081200000002',
        ]);

        // ============================================================
        // RUNNER (Sample)
        // ============================================================
        User::create([
            'nama' => 'Andi Pratama',
            'email' => 'runner@runfest.test',
            'password' => Hash::make('password'),
            'role' => 'Runner',
            'no_hp' => '081200000003',
            'golongan_darah' => 'O',
            'kontak_darurat' => 'Ibu Sari - 081200000099',
        ]);

        // ============================================================
        // MARSHAL
        // ============================================================
        User::create([
            'nama' => 'Dimas Marshal',
            'email' => 'marshal@runfest.test',
            'password' => Hash::make('password'),
            'role' => 'Marshal',
            'no_hp' => '081200000004',
        ]);

        // ============================================================
        // SAMPLE EVENT 1: Jakarta Night Run
        // ============================================================
        $event1 = EventLari::create([
            'id_organizer' => $organizer->id_user,
            'nama_event' => 'Jakarta Night Run 2026',
            'slug' => 'jakarta-night-run-2026',
            'tanggal_event' => '2026-10-15',
            'lokasi_venue' => 'GBK, Senayan, Jakarta Pusat',
            'tanggal_rpc_mulai' => '2026-10-12',
            'tanggal_rpc_selesai' => '2026-10-14',
            'status_event' => 'Publikasi',
            'deskripsi' => 'Lari malam hari di jantung kota Jakarta! Nikmati pemandangan ikonik GBK yang diterangi lampu-lampu megah sambil berlari bersama ribuan pelari lainnya. Event ini cocok untuk pelari pemula maupun profesional.',
            'banner_url' => '/images/banners/jakarta-night-run.png',
        ]);

        KategoriLari::create([
            'id_event' => $event1->id_event,
            'nama_kategori' => '5K Fun Run',
            'harga' => 150000,
            'kuota_peserta' => 1000,
            'terisi' => 0,
        ]);

        KategoriLari::create([
            'id_event' => $event1->id_event,
            'nama_kategori' => '10K Competitive',
            'harga' => 250000,
            'kuota_peserta' => 500,
            'terisi' => 0,
        ]);

        KategoriLari::create([
            'id_event' => $event1->id_event,
            'nama_kategori' => 'Half Marathon 21K',
            'harga' => 450000,
            'kuota_peserta' => 200,
            'terisi' => 0,
        ]);

        // ============================================================
        // SAMPLE EVENT 2: Bali Beach Run
        // ============================================================
        $event2 = EventLari::create([
            'id_organizer' => $organizer->id_user,
            'nama_event' => 'Bali Beach Run Festival 2026',
            'slug' => 'bali-beach-run-2026',
            'tanggal_event' => '2026-11-22',
            'lokasi_venue' => 'Pantai Sanur, Denpasar, Bali',
            'tanggal_rpc_mulai' => '2026-11-19',
            'tanggal_rpc_selesai' => '2026-11-21',
            'status_event' => 'Publikasi',
            'deskripsi' => 'Berlari di sepanjang garis pantai Sanur yang indah dengan sunrise yang memukau. Race pack eksklusif dengan jersey premium dan finisher medal terbatas.',
            'banner_url' => '/images/banners/bali-beach-run.png',
        ]);

        KategoriLari::create([
            'id_event' => $event2->id_event,
            'nama_kategori' => '5K Sunrise Run',
            'harga' => 175000,
            'kuota_peserta' => 800,
            'terisi' => 0,
        ]);

        KategoriLari::create([
            'id_event' => $event2->id_event,
            'nama_kategori' => '10K Beach Challenge',
            'harga' => 300000,
            'kuota_peserta' => 400,
            'terisi' => 0,
        ]);

        // ============================================================
        // SAMPLE EVENT 3: Bandung Trail
        // ============================================================
        $event3 = EventLari::create([
            'id_organizer' => $organizer->id_user,
            'nama_event' => 'Bandung Highland Trail 2026',
            'slug' => 'bandung-highland-trail-2026',
            'tanggal_event' => '2026-12-08',
            'lokasi_venue' => 'Tahura Djuanda, Bandung',
            'tanggal_rpc_mulai' => '2026-12-05',
            'tanggal_rpc_selesai' => '2026-12-07',
            'status_event' => 'Publikasi',
            'deskripsi' => 'Trail running di dataran tinggi Bandung. Tantang dirimu dengan medan pegunungan yang menantang sambil menikmati udara sejuk dan pemandangan alam yang luar biasa.',
            'banner_url' => '/images/banners/bandung-trail-run.png',
        ]);

        KategoriLari::create([
            'id_event' => $event3->id_event,
            'nama_kategori' => '10K Trail Run',
            'harga' => 275000,
            'kuota_peserta' => 300,
            'terisi' => 0,
        ]);

        KategoriLari::create([
            'id_event' => $event3->id_event,
            'nama_kategori' => '21K Ultra Trail',
            'harga' => 500000,
            'kuota_peserta' => 150,
            'terisi' => 0,
        ]);

        KategoriLari::create([
            'id_event' => $event3->id_event,
            'nama_kategori' => '42K Full Marathon',
            'harga' => 750000,
            'kuota_peserta' => 75,
            'terisi' => 0,
        ]);

        // ============================================================
        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('');
        $this->command->info('📌 Akun Login:');
        $this->command->info('   SuperAdmin : admin@runfest.test / password');
        $this->command->info('   Organizer  : organizer@runfest.test / password');
        $this->command->info('   Runner     : runner@runfest.test / password');
        $this->command->info('   Marshal    : marshal@runfest.test / password');
    }
}
