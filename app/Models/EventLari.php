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
        // Contact Person fields
        'nama_cp',
        'no_wa_cp',
        'email_cp',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_event'      => 'date',
            'tanggal_rpc_mulai'  => 'date',
            'tanggal_rpc_selesai'=> 'date',
            'latitude'           => 'decimal:8',
            'longitude'          => 'decimal:8',
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

    /**
     * Penugasan Marshal pada event ini.
     */
    public function marshals()
    {
        return $this->hasMany(EventMarshal::class, 'id_event', 'id_event');
    }

    /**
     * Users yang berperan sebagai Marshal di event ini.
     */
    public function assignedMarshals()
    {
        return $this->hasManyThrough(
            User::class,
            EventMarshal::class,
            'id_event',        // FK di event_marshals -> event_lari
            'id_user',         // PK di users
            'id_event',        // PK di event_lari
            'id_user_marshal'  // FK di event_marshals -> users
        );
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

    /**
     * URL WhatsApp langsung ke narahubung event.
     */
    public function getWaUrlAttribute(): ?string
    {
        if (empty($this->no_wa_cp)) {
            return null;
        }

        // Normalisasi nomor: hapus karakter non-digit, ganti prefix 0 -> 62
        $no = preg_replace('/\D/', '', $this->no_wa_cp);
        if (str_starts_with($no, '0')) {
            $no = '62' . substr($no, 1);
        }

        $text = urlencode(
            "Halo {$this->nama_cp}, saya ingin bertanya seputar event {$this->nama_event}."
        );

        return "https://wa.me/{$no}?text={$text}";
    }

    /**
     * Alias accessor & mutator untuk no_wa agar kompatibel dengan no_wa_cp.
     */
    public function getNoWaAttribute(): ?string
    {
        return $this->attributes['no_wa_cp'] ?? null;
    }

    public function setNoWaAttribute($value): void
    {
        $this->attributes['no_wa_cp'] = $value;
    }

    /**
     * Apakah event ini sudah selesai / lewat.
     */
    public function getIsFinishedAttribute(): bool
    {
        return $this->status_event === 'Selesai'
            || ($this->tanggal_event && $this->tanggal_event->isPast());
    }
}
