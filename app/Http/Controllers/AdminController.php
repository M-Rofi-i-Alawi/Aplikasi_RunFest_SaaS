<?php

namespace App\Http\Controllers;

use App\Models\EventLari;
use App\Models\EventMarshal;
use App\Models\PendaftaranLari;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /* ================================================================
     * DASHBOARD — Ringkasan Statistik Sistem
     * ================================================================ */

    public function dashboard()
    {
        $stats = [
            'total_events'       => EventLari::count(),
            'events_moderasi'    => EventLari::where('status_event', 'Moderasi')->count(),
            'events_publikasi'   => EventLari::where('status_event', 'Publikasi')->count(),
            'events_selesai'     => EventLari::where('status_event', 'Selesai')->count(),
            'total_organizers'   => User::where('role', 'Organizer')->count(),
            'organizers_pending' => User::where('role', 'Organizer')->where('status_akun', 'Pending')->count(),
            'organizers_aktif'   => User::where('role', 'Organizer')->where('status_akun', 'Aktif')->count(),
            'total_marshals'     => User::where('role', 'Marshal')->count(),
            'total_runners'      => User::where('role', 'Runner')->count(),
            'total_tiket'        => PendaftaranLari::count(),
            'tiket_lunas'        => PendaftaranLari::where('status_pembayaran', 'Lunas')->count(),
            'tiket_pending'      => PendaftaranLari::where('status_pembayaran', 'Pending')->count(),
        ];

        // Event terbaru yang butuh moderasi
        $pendingEvents = EventLari::with('organizer')
            ->where('status_event', 'Moderasi')
            ->latest()
            ->take(5)
            ->get();

        // EO terbaru yang butuh verifikasi
        $pendingOrganizers = User::where('role', 'Organizer')
            ->where('status_akun', 'Pending')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'pendingEvents', 'pendingOrganizers'));
    }

    /* ================================================================
     * MANAJEMEN ORGANIZER — Verifikasi & Aktivasi EO
     * ================================================================ */

    public function organizers(Request $request)
    {
        $query = User::where('role', 'Organizer');

        // Filter berdasarkan status akun
        if ($request->filled('status')) {
            $query->where('status_akun', $request->status);
        }

        // Pencarian berdasarkan nama / email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $organizers = $query->withCount('eventLari')->latest()->paginate(20);

        return view('admin.organizers', compact('organizers'));
    }

    public function updateOrganizerStatus(Request $request, $id)
    {
        $request->validate([
            'status_akun' => ['required', 'in:Pending,Aktif,Diblokir'],
        ]);

        $organizer = User::where('role', 'Organizer')->findOrFail($id);
        $organizer->update(['status_akun' => $request->status_akun]);

        $statusLabel = match($request->status_akun) {
            'Aktif'    => 'diaktifkan',
            'Diblokir' => 'diblokir',
            'Pending'  => 'dikembalikan ke Pending',
        };

        return back()->with('success', "Akun organizer {$organizer->nama} berhasil {$statusLabel}.");
    }

    /* ================================================================
     * MODERASI EVENT — Review & Publikasi
     * ================================================================ */

    public function events(Request $request)
    {
        $query = EventLari::with('organizer');

        // Filter berdasarkan status event
        if ($request->filled('status')) {
            $query->where('status_event', $request->status);
        } else {
            // Default: tampilkan event yang butuh moderasi
            $query->whereIn('status_event', ['Draft', 'Moderasi']);
        }

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_event', 'like', "%{$search}%");
        }

        $events = $query->latest()->paginate(20);

        return view('admin.events', compact('events'));
    }

    public function updateEventStatus(Request $request, $id)
    {
        $request->validate([
            'status_event' => ['required', 'in:Draft,Moderasi,Publikasi,Selesai,Dibatalkan'],
        ]);

        $event = EventLari::findOrFail($id);
        $oldStatus = $event->status_event;
        $event->update(['status_event' => $request->status_event]);

        $statusLabel = match($request->status_event) {
            'Publikasi'   => 'dipublikasikan ke katalog',
            'Selesai'     => 'ditandai selesai',
            'Dibatalkan'  => 'dibatalkan',
            'Moderasi'    => 'dikembalikan ke moderasi',
            'Draft'       => 'dikembalikan ke draft',
        };

        return back()->with('success', "Event \"{$event->nama_event}\" berhasil {$statusLabel}.");
    }

    /* ================================================================
     * MANAJEMEN MARSHAL — Penugasan & PIN Scanner
     * ================================================================ */

    public function marshals(Request $request)
    {
        // Daftar event yang memiliki penugasan marshal
        $events = EventLari::with(['marshals.marshal'])
            ->whereIn('status_event', ['Publikasi', 'Selesai'])
            ->latest()
            ->get();

        // Daftar semua user Marshal (untuk form assignment)
        $availableMarshals = User::where('role', 'Marshal')
            ->where('status_akun', 'Aktif')
            ->orderBy('nama')
            ->get();

        // Daftar semua event untuk dropdown
        $availableEvents = EventLari::whereIn('status_event', ['Publikasi', 'Moderasi'])
            ->orderBy('tanggal_event')
            ->get();

        return view('admin.marshals', compact('events', 'availableMarshals', 'availableEvents'));
    }

    public function assignMarshal(Request $request)
    {
        $request->validate([
            'id_event'       => ['required', 'exists:event_lari,id_event'],
            'id_user_marshal'=> ['required', 'exists:users,id_user'],
            'pin_akses_scanner' => ['nullable', 'string', 'max:20'],
        ]);

        // Cek apakah user yang dipilih memang berperan Marshal
        $marshal = User::findOrFail($request->id_user_marshal);
        if ($marshal->role !== 'Marshal' && !$marshal->isSuperAdmin()) {
            return back()->with('error', "User {$marshal->nama} bukan seorang Marshal.");
        }

        // Cek duplikasi penugasan
        $exists = EventMarshal::where('id_event', $request->id_event)
                               ->where('id_user_marshal', $request->id_user_marshal)
                               ->exists();

        if ($exists) {
            return back()->with('error', "Marshal {$marshal->nama} sudah ditugaskan pada event ini.");
        }

        EventMarshal::create([
            'id_event'          => $request->id_event,
            'id_user_marshal'   => $request->id_user_marshal,
            'pin_akses_scanner' => $request->pin_akses_scanner ?? Str::random(6),
        ]);

        return back()->with('success', "Marshal {$marshal->nama} berhasil ditugaskan.");
    }

    public function removeMarshal($id)
    {
        $assignment = EventMarshal::findOrFail($id);
        $marshalName = $assignment->marshal->nama ?? 'Marshal';
        $assignment->delete();

        return back()->with('success', "Penugasan {$marshalName} berhasil dihapus.");
    }

    public function updateMarshalPin(Request $request, $id)
    {
        $request->validate([
            'pin_akses_scanner' => ['required', 'string', 'min:4', 'max:20'],
        ]);

        $assignment = EventMarshal::findOrFail($id);
        $assignment->update(['pin_akses_scanner' => $request->pin_akses_scanner]);

        return back()->with('success', 'PIN scanner berhasil diperbarui.');
    }
}
