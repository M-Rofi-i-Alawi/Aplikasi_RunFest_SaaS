<?php

namespace App\Http\Middleware;

use App\Models\EventMarshal;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureMarshalAssignedToEvent
{
    /**
     * Verifikasi bahwa Marshal yang login ditugaskan pada event yang sedang diakses.
     *
     * SuperAdmin selalu diperbolehkan bypass.
     * Marshal yang tidak ditugaskan mendapat 403 Forbidden.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // SuperAdmin bypass semua pembatasan
        if ($user && $user->isSuperAdmin()) {
            return $next($request);
        }

        // Ambil id_event dari route parameter
        $eventId = $request->route('id_event') ?? $request->route('id') ?? $request->input('id_event');

        if (!$eventId) {
            abort(403, 'Parameter event tidak ditemukan.');
        }

        // Cek apakah user terdaftar sebagai Marshal pada event ini
        $assigned = EventMarshal::where('id_event', $eventId)
                                ->where('id_user_marshal', $user->id_user)
                                ->exists();

        if (!$assigned) {
            abort(403, 'Anda tidak ditugaskan pada event ini! Hubungi SuperAdmin untuk penugasan.');
        }

        return $next($request);
    }
}
