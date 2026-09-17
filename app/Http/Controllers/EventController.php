<?php

namespace App\Http\Controllers;

use App\Models\EventLari;
use App\Models\KategoriLari;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventController extends Controller
{
    /**
     * Katalog event publik — menampilkan semua event yang berstatus "Publikasi".
     * Mendukung filter pencarian (?search=...) dan live search JSON (?json=1).
     */
    public function index(Request $request)
    {
        $query = EventLari::where('status_event', 'Publikasi')
            ->withCount('pendaftaran')
            ->with(['kategori', 'organizer'])
            ->orderBy('tanggal_event', 'asc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_event', 'like', "%{$search}%")
                  ->orWhere('lokasi_venue', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if ($request->wantsJson() || $request->has('json')) {
            $results = $query->limit(8)->get();
            return response()->json($results->map(function ($ev) {
                return [
                    'id_event' => $ev->id_event,
                    'nama_event' => $ev->nama_event,
                    'slug' => $ev->slug,
                    'lokasi_venue' => $ev->lokasi_venue,
                    'tanggal_event' => $ev->tanggal_event ? $ev->tanggal_event->format('d M Y') : '',
                    'banner_image_url' => $ev->banner_image_url,
                ];
            }));
        }

        $events = $query->paginate(9)->withQueryString();

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
     * Daftar event — Multi-Role Support:
     *   - SuperAdmin: melihat SEMUA event di platform.
     *   - Organizer:  hanya melihat event milik sendiri.
     */
    public function myEvents()
    {
        $user = auth()->user();

        $baseQuery = EventLari::query();
        if (!$user->isSuperAdmin()) {
            $baseQuery->where('id_organizer', $user->id_user);
        }

        // Hitung metrik ringkasan untuk stats card
        $allEvents = (clone $baseQuery)->with(['kategori'])->withCount('pendaftaran')->get();
        $totalEvents = $allEvents->count();
        $totalPublikasi = $allEvents->where('status_event', 'Publikasi')->count();
        $totalPeserta = $allEvents->sum('pendaftaran_count');

        $eventIds = $allEvents->pluck('id_event');
        $totalPendapatan = \App\Models\PembayaranLari::where('status_transaksi', 'Lunas')
            ->whereHas('pendaftaran', function ($q) use ($eventIds) {
                $q->whereIn('id_event', $eventIds);
            })
            ->sum('total_bayar');

        $events = (clone $baseQuery)
            ->withCount('pendaftaran')
            ->with(['kategori', 'organizer'])
            ->latest()
            ->paginate(10);

        return view('events.my-events', compact('events', 'totalEvents', 'totalPublikasi', 'totalPeserta', 'totalPendapatan'));
    }

    /**
     * Form buat event baru (Organizer / SuperAdmin).
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
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
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

        $bannerUrl = null;
        if ($request->hasFile('banner')) {
            $bannerUrl = $request->file('banner')->store('events', 'public');
        }

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
            'banner_url' => $bannerUrl,
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
     * Form edit event — Multi-Role Support:
     *   - SuperAdmin: bisa edit event siapapun.
     *   - Organizer:  hanya event milik sendiri.
     */
    public function edit(int $id)
    {
        $event = $this->resolveEventForManagement($id);

        return view('events.edit', compact('event'));
    }

    /**
     * Update event di database — Multi-Role Support.
     */
    public function update(Request $request, int $id)
    {
        $event = $this->resolveEventForManagement($id);

        $validated = $request->validate([
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
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

        if ($request->hasFile('banner')) {
            $oldBanner = $event->banner_url;
            $newBanner = $request->file('banner')->store('events', 'public');
            $validated['banner_url'] = $newBanner;

            // Hapus foto lama jika memang file tersimpan di storage public
            if ($oldBanner && Storage::disk('public')->exists($oldBanner)) {
                Storage::disk('public')->delete($oldBanner);
            }
        }
        unset($validated['banner']);

        $event->update($validated);

        return redirect()->route('organizer.events')
            ->with('success', 'Event berhasil diperbarui!');
    }

    /**
     * Hapus event beserta seluruh data terkait (kategori, pendaftaran, pembayaran).
     * 
     * Safety Rules:
     *   - Event yang memiliki pendaftaran berstatus "Lunas" TIDAK BISA dihapus.
     *     Organizer harus mengubah status event ke "Dibatalkan" terlebih dahulu.
     *   - Event yang hanya memiliki pendaftaran "Pending"/"Gagal" atau tanpa
     *     pendaftaran bisa dihapus langsung (CASCADE di DB akan membersihkan child records).
     */
    public function destroy(int $id)
    {
        $event = $this->resolveEventForManagement($id);

        // Safety check: tolak penghapusan jika ada peserta yang sudah bayar (Lunas)
        $lunasCount = $event->pendaftaran()->where('status_pembayaran', 'Lunas')->count();

        if ($lunasCount > 0) {
            return back()->with('error',
                'Event "' . $event->nama_event . '" tidak bisa dihapus karena masih memiliki '
                . $lunasCount . ' peserta berstatus Lunas. Ubah status event ke "Dibatalkan" sebagai gantinya.'
            );
        }

        // Hapus banner dari storage jika ada
        if ($event->banner_url && Storage::disk('public')->exists($event->banner_url)) {
            Storage::disk('public')->delete($event->banner_url);
        }

        $namaEvent = $event->nama_event;

        // Delete event — child records (kategori_lari, pendaftaran_lari, pembayaran_lari)
        // dihapus otomatis oleh database CASCADE constraint.
        $event->delete();

        return redirect()->route('organizer.events')
            ->with('success', 'Event "' . $namaEvent . '" beserta seluruh datanya berhasil dihapus.');
    }

    /**
     * Tambah kategori baru ke event yang sudah ada — Multi-Role Support.
     */
    public function storeKategori(Request $request, int $eventId)
    {
        $event = $this->resolveEventForManagement($eventId);

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

    /* ================================================================
     * PRIVATE HELPERS
     * ================================================================ */

    /**
     * Resolve event berdasarkan ID, memeriksa hak akses berdasarkan role.
     *   - SuperAdmin: bisa mengakses event siapapun.
     *   - Organizer:  hanya event milik sendiri.
     */
    private function resolveEventForManagement(int $id): EventLari
    {
        $user = auth()->user();

        $query = EventLari::where('id_event', $id)->with('kategori');

        if (!$user->isSuperAdmin()) {
            $query->where('id_organizer', $user->id_user);
        }

        return $query->firstOrFail();
    }
}

