<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriLari extends Model
{
    use HasFactory;

    protected $table = 'kategori_lari';
    protected $primaryKey = 'id_kategori';

    protected $fillable = [
        'id_event',
        'nama_kategori',
        'harga',
        'kuota_peserta',
        'terisi',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
            'kuota_peserta' => 'integer',
            'terisi' => 'integer',
        ];
    }

    /* ================================================================
     * RELASI ELOQUENT
     * ================================================================ */

    /**
     * Kategori milik satu event.
     */
    public function event()
    {
        return $this->belongsTo(EventLari::class, 'id_event', 'id_event');
    }

    /**
     * Kategori memiliki banyak pendaftaran.
     */
    public function pendaftaran()
    {
        return $this->hasMany(PendaftaranLari::class, 'id_kategori', 'id_kategori');
    }

    /* ================================================================
     * ACCESSORS
     * ================================================================ */

    /**
     * Hitung sisa kuota yang tersedia.
     */
    public function getSisaKuotaAttribute(): int
    {
        return $this->kuota_peserta - $this->terisi;
    }

    /**
     * Cek apakah kuota masih tersedia.
     */
    public function getIsTersediaAttribute(): bool
    {
        return $this->terisi < $this->kuota_peserta;
    }
}
