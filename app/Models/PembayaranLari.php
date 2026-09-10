<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranLari extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_lari';
    protected $primaryKey = 'id_pembayaran';

    protected $fillable = [
        'id_pendaftaran',
        'kode_transaksi',
        'snap_token',
        'metode_pembayaran',
        'payment_type',
        'total_bayar',
        'status_transaksi',
        'waktu_bayar',
        'payload_response',
    ];

    protected function casts(): array
    {
        return [
            'total_bayar' => 'decimal:2',
            'waktu_bayar' => 'datetime',
            'payload_response' => 'array',
        ];
    }

    /* ================================================================
     * RELASI ELOQUENT
     * ================================================================ */

    /**
     * Pembayaran milik satu pendaftaran.
     */
    public function pendaftaran()
    {
        return $this->belongsTo(PendaftaranLari::class, 'id_pendaftaran', 'id_pendaftaran');
    }
}
