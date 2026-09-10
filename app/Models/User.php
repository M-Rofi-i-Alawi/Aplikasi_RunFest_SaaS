<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Primary key menggunakan id_user sesuai spesifikasi.
     */
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'google_id',
        'nama',
        'email',
        'password',
        'nik',
        'foto_identitas',
        'no_hp',
        'golongan_darah',
        'kontak_darurat',
        'ukuran_jersey_default',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /* ================================================================
     * RELASI ELOQUENT
     * ================================================================ */

    /**
     * User sebagai Organizer memiliki banyak event lari.
     */
    public function eventLari()
    {
        return $this->hasMany(EventLari::class, 'id_organizer', 'id_user');
    }

    /**
     * User sebagai Runner memiliki banyak pendaftaran.
     */
    public function pendaftaran()
    {
        return $this->hasMany(PendaftaranLari::class, 'id_runner', 'id_user');
    }

    /**
     * User sebagai Organizer memiliki banyak rekening payout.
     */
    public function rekening()
    {
        return $this->hasMany(RekeningOrganizer::class, 'id_user', 'id_user');
    }

    /**
     * Rekening utama organizer.
     */
    public function rekeningUtama()
    {
        return $this->hasOne(RekeningOrganizer::class, 'id_user', 'id_user')->where('is_primary', true);
    }

    /* ================================================================
     * HELPER METHODS
     * ================================================================ */

    public function isSuperAdmin(): bool
    {
        return $this->role === 'SuperAdmin';
    }

    public function isOrganizer(): bool
    {
        return $this->role === 'Organizer';
    }

    public function isRunner(): bool
    {
        return $this->role === 'Runner';
    }

    public function isMarshal(): bool
    {
        return $this->role === 'Marshal';
    }

    /**
     * Cek apakah user login via Google OAuth (tidak punya password manual).
     */
    public function isOAuthUser(): bool
    {
        return !empty($this->google_id) && is_null($this->getAttributes()['password'] ?? null);
    }
}
