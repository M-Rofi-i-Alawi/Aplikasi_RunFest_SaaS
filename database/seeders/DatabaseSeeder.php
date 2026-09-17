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

        // Catatan: Sample event codingan telah dihapus agar event murni hasil inputan user/organizer.
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
