<?php

namespace App\Http\Controllers;

use App\Models\EventLari;
use App\Models\KategoriLari;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventController extends Controller
{
    /**
     * Katalog event publik — menampilkan semua event yang berstatus "Publikasi".
     */
    public function index()
    {
        $events = EventLari::where('status_event', 'Publikasi')
            ->withCount('pendaftaran')
            ->with(['kategori', 'organizer'])
            ->orderBy('tanggal_event', 'asc')
            ->paginate(9);

        return view('events.index', compact('events'));
    }

    /**
     * Detail event publik berdasarkan slug.
     */
    public function show(string $slug)
    {
        $event = EventLari::where('slug', $slug)
            ->with(['kategori', 'organizer'])
            ->withCount('pendaftaran')
            ->firstOrFail();

        return view('events.show', compact('event'));
    }

    /**
     * Daftar event milik Organizer yang sedang login.
     */
    public function myEvents()
    {
        $events = EventLari::where('id_organizer', auth()->user()->id_user)
            ->withCount('pendaftaran')
            ->with('kategori')
            ->latest()
            ->paginate(10);

        return view('events.my-events', compact('events'));
    }

    /**
     * Form buat event baru (Organizer).
     */
    public function create()
    {
        return view('events.create');
    }

    /**
     * Simpan event baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_event' => ['required', 'string', 'max:255'],
            'tanggal_event' => ['required', 'date', 'after:today'],
            'lokasi_venue' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'google_maps_url' => ['nullable', 'url', 'max:2000'],
            'tanggal_rpc_mulai' => ['required', 'date', 'before:tanggal_event'],
            'tanggal_rpc_selesai' => ['required', 'date', 'after_or_equal:tanggal_rpc_mulai', 'before_or_equal:tanggal_event'],
            'deskripsi' => ['nullable', 'string'],
            'status_event' => ['required', 'in:Draft,Publikasi'],
            // Kategori lari (dinamis, bisa lebih dari 1)
            'kategori' => ['required', 'array', 'min:1'],
            'kategori.*.nama_kategori' => ['required', 'string', 'max:100'],
            'kategori.*.harga' => ['required', 'numeric', 'min:0'],
            'kategori.*.kuota_peserta' => ['required', 'integer', 'min:1'],
        ]);

        $event = EventLari::create([
            'id_organizer' => auth()->user()->id_user,
            'nama_event' => $validated['nama_event'],
            'slug' => Str::slug($validated['nama_event']) . '-' . Str::random(5),
            'tanggal_event' => $validated['tanggal_event'],
            'lokasi_venue' => $validated['lokasi_venue'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'google_maps_url' => $validated['google_maps_url'] ?? null,
            'tanggal_rpc_mulai' => $validated['tanggal_rpc_mulai'],
            'tanggal_rpc_selesai' => $validated['tanggal_rpc_selesai'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'status_event' => $validated['status_event'],
        ]);

        // Simpan kategori lari
        foreach ($validated['kategori'] as $kat) {
            KategoriLari::create([
                'id_event' => $event->id_event,
                'nama_kategori' => $kat['nama_kategori'],
                'harga' => $kat['harga'],
                'kuota_peserta' => $kat['kuota_peserta'],
                'terisi' => 0,
            ]);
        }

        return redirect()->route('organizer.events')
            ->with('success', 'Event "' . $event->nama_event . '" berhasil dibuat!');
    }

    /**
     * Form edit event (Organizer).
     */
    public function edit(int $id)
    {
        $event = EventLari::where('id_event', $id)
            ->where('id_organizer', auth()->user()->id_user)
            ->with('kategori')
            ->firstOrFail();

        return view('events.edit', compact('event'));
    }

    /**
     * Update event di database.
     */
    public function update(Request $request, int $id)
    {
        $event = EventLari::where('id_event', $id)
            ->where('id_organizer', auth()->user()->id_user)
            ->firstOrFail();

        $validated = $request->validate([
            'nama_event' => ['required', 'string', 'max:255'],
            'tanggal_event' => ['required', 'date'],
            'lokasi_venue' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'google_maps_url' => ['nullable', 'url', 'max:2000'],
            'tanggal_rpc_mulai' => ['required', 'date'],
            'tanggal_rpc_selesai' => ['required', 'date', 'after_or_equal:tanggal_rpc_mulai'],
            'deskripsi' => ['nullable', 'string'],
            'status_event' => ['required', 'in:Draft,Publikasi,Selesai,Dibatalkan'],
        ]);

        $event->update($validated);

        return redirect()->route('organizer.events')
            ->with('success', 'Event berhasil diperbarui!');
    }

    /**
     * Tambah kategori baru ke event yang sudah ada.
     */
    public function storeKategori(Request $request, int $eventId)
    {
        $event = EventLari::where('id_event', $eventId)
            ->where('id_organizer', auth()->user()->id_user)
            ->firstOrFail();

        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:100'],
            'harga' => ['required', 'numeric', 'min:0'],
            'kuota_peserta' => ['required', 'integer', 'min:1'],
        ]);

        KategoriLari::create([
            'id_event' => $event->id_event,
            'nama_kategori' => $validated['nama_kategori'],
            'harga' => $validated['harga'],
            'kuota_peserta' => $validated['kuota_peserta'],
            'terisi' => 0,
        ]);

        return back()->with('success', 'Kategori "' . $validated['nama_kategori'] . '" berhasil ditambahkan!');
    }
}
