<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventMarshal extends Model
{
    use HasFactory;

    protected $table = 'event_marshals';
    protected $primaryKey = 'id_marshal_assignment';

    protected $fillable = [
        'id_event',
        'id_user_marshal',
        'pin_akses_scanner',
    ];

    /* ================================================================
     * RELASI ELOQUENT
     * ================================================================ */

    /**
     * Penugasan marshal ini terkait dengan satu event.
     */
    public function event()
    {
        return $this->belongsTo(EventLari::class, 'id_event', 'id_event');
    }

    /**
     * Penugasan marshal ini terkait dengan satu user (Marshal).
     */
    public function marshal()
    {
        return $this->belongsTo(User::class, 'id_user_marshal', 'id_user');
    }
}
