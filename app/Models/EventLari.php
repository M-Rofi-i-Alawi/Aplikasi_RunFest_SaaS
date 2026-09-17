<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EventLari extends Model
{
    use HasFactory;

    protected $table = 'event_lari';
    protected $primaryKey = 'id_event';

    protected $fillable = [
        'id_organizer',
        'nama_event',
        'slug',
        'tanggal_event',
        'lokasi_venue',
        'latitude',
        'longitude',
        'google_maps_url',
        'tanggal_rpc_mulai',
        'tanggal_rpc_selesai',
        'status_event',
        'deskripsi',
        'banner_url',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_event' => 'date',
            'tanggal_rpc_mulai' => 'date',
            'tanggal_rpc_selesai' => 'date',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    /* ================================================================
     * BOOT — Auto-generate slug dari nama_event
     * ================================================================ */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->nama_event) . '-' . Str::random(5);
            }
        });
    }

    /* ================================================================
     * RELASI ELOQUENT
     * ================================================================ */

    /**
     * Event dimiliki oleh satu Organizer (User).
     */
    public function organizer()
    {
        return $this->belongsTo(User::class, 'id_organizer', 'id_user');
    }

    /**
     * Event memiliki banyak kategori lari.
     */
    public function kategori()
    {
        return $this->hasMany(KategoriLari::class, 'id_event', 'id_event');
    }

    /**
     * Event memiliki banyak pendaftaran.
     */
    public function pendaftaran()
    {
        return $this->hasMany(PendaftaranLari::class, 'id_event', 'id_event');
    }

    /* ================================================================
     * ACCESSORS
     * ================================================================ */

    /**
     * Cek apakah event sedang dalam periode RPC.
     */
    public function getIsRpcActiveAttribute(): bool
    {
        $today = now()->toDateString();
        return $today >= $this->tanggal_rpc_mulai && $today <= $this->tanggal_rpc_selesai;
    }

    /**
     * URL banner siap pakai untuk tag <img>.
     * Mendukung URL eksternal, aset statis public, maupun path Laravel Storage.
     */
    public function getBannerImageUrlAttribute(): ?string
    {
        if (empty($this->banner_url)) {
            return null;
        }

        if (str_starts_with($this->banner_url, 'http://') || str_starts_with($this->banner_url, 'https://')) {
            return $this->banner_url;
        }

        if (str_starts_with($this->banner_url, '/')) {
            return asset(ltrim($this->banner_url, '/'));
        }

        return asset('storage/' . $this->banner_url);
    }

    /**
     * URL Google Maps venue dengan fallback otomatis ke Google Maps Search query.
     */
    public function getMapsUrlAttribute(): string
    {
        if (!empty($this->google_maps_url)) {
            return $this->google_maps_url;
        }

        $query = trim(($this->lokasi_venue ?? '') . ' ' . ($this->nama_event ?? ''));
        return 'https://www.google.com/maps/search/?api=1&query=' . urlencode($query);
    }
}

