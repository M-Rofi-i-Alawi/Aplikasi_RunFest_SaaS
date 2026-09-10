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
}
