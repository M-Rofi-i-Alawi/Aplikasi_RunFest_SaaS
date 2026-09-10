<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekeningOrganizer extends Model
{
    use HasFactory;

    protected $table = 'rekening_organizer';
    protected $primaryKey = 'id_rekening';

    protected $fillable = [
        'id_user',
        'tipe_rekening',
        'nama_bank',
        'nomor_rekening',
        'nama_pemilik',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    /* ================================================================
     * RELASI ELOQUENT
     * ================================================================ */

    /**
     * Rekening dimiliki oleh seorang User (Organizer).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
