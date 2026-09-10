<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendaftaranLari extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran_lari';
    protected $primaryKey = 'id_pendaftaran';

    protected $fillable = [
        'id_event',
        'id_runner',
        'id_kategori',
        'bib_number',
        'ukuran_jersey',
        'qr_code_token',
        'status_pembayaran',
        'status_racepack',
        'is_diwakilkan',
        'nama_pengambil',
        'nik_pengambil',
        'catatan_rpc',
        'waktu_pengambilan_racepack',
    ];

    protected function casts(): array
    {
        return [
            'waktu_pengambilan_racepack' => 'datetime',
            'is_diwakilkan' => 'boolean',
        ];
    }

    /* ================================================================
     * RELASI ELOQUENT
     * ================================================================ */

    /**
     * Pendaftaran milik satu event.
     */
    public function event()
    {
        return $this->belongsTo(EventLari::class, 'id_event', 'id_event');
    }

    /**
     * Pendaftaran milik satu runner (User).
     */
    public function runner()
    {
        return $this->belongsTo(User::class, 'id_runner', 'id_user');
    }

    /**
     * Pendaftaran termasuk satu kategori lari.
     */
    public function kategori()
    {
        return $this->belongsTo(KategoriLari::class, 'id_kategori', 'id_kategori');
    }

    /**
     * Pendaftaran memiliki satu data pembayaran.
     */
    public function pembayaran()
    {
        return $this->hasOne(PembayaranLari::class, 'id_pendaftaran', 'id_pendaftaran');
    }

    /* ================================================================
     * HELPER METHODS
     * ================================================================ */

    public function isLunas(): bool
    {
        return $this->status_pembayaran === 'Lunas';
    }

    public function isSudahDiambil(): bool
    {
        return $this->status_racepack === 'Sudah Diambil';
    }
}
