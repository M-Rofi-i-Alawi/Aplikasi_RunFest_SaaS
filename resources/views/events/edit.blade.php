@extends('layouts.app')

@section('title', 'Edit Event - ' . $event->nama_event)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('organizer.events') }}" class="inline-flex items-center space-x-2 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-colors mb-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            <span>Kembali ke Daftar Event</span>
        </a>
        <h1 class="text-2xl font-extrabold text-slate-900">Edit Event</h1>
        <p class="text-sm text-slate-500">{{ $event->nama_event }}</p>
    </div>

    {{-- Edit Form --}}
    <form method="POST" action="{{ route('organizer.events.update', $event->id_event) }}">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 sm:p-8 mb-6">
            <h2 class="text-base font-bold text-slate-900 uppercase tracking-wider mb-6 pb-2 border-b border-slate-200">
                Detail Event
            </h2>

            <div class="space-y-4">
                <div>
                    <label for="nama_event" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Event <span class="text-rose-500">*</span></label>
                    <input type="text" id="nama_event" name="nama_event" value="{{ old('nama_event', $event->nama_event) }}" required
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors">
                    @error('nama_event') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="tanggal_event" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Pelaksanaan <span class="text-rose-500">*</span></label>
                        <input type="date" id="tanggal_event" name="tanggal_event" value="{{ old('tanggal_event', $event->tanggal_event->format('Y-m-d')) }}" required
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors">
                    </div>
                    <div>
                        <label for="lokasi_venue" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Lokasi Venue <span class="text-rose-500">*</span></label>
                        <input type="text" id="lokasi_venue" name="lokasi_venue" value="{{ old('lokasi_venue', $event->lokasi_venue) }}" required
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="tanggal_rpc_mulai" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal RPC Mulai <span class="text-rose-500">*</span></label>
                        <input type="date" id="tanggal_rpc_mulai" name="tanggal_rpc_mulai" value="{{ old('tanggal_rpc_mulai', $event->tanggal_rpc_mulai->format('Y-m-d')) }}" required
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors">
                    </div>
                    <div>
                        <label for="tanggal_rpc_selesai" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal RPC Selesai <span class="text-rose-500">*</span></label>
                        <input type="date" id="tanggal_rpc_selesai" name="tanggal_rpc_selesai" value="{{ old('tanggal_rpc_selesai', $event->tanggal_rpc_selesai->format('Y-m-d')) }}" required
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors">
                    </div>
                </div>

                <div>
                    <label for="deskripsi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Deskripsi Event</label>
                    <textarea id="deskripsi" name="deskripsi" rows="3"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors resize-none">{{ old('deskripsi', $event->deskripsi) }}</textarea>
                </div>

                <div>
                    <label for="status_event" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Status Publikasi <span class="text-rose-500">*</span></label>
                    <select id="status_event" name="status_event" required
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors">
                        @foreach(['Draft', 'Publikasi', 'Selesai', 'Dibatalkan'] as $status)
                            <option value="{{ $status }}" {{ old('status_event', $event->status_event) == $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3 mb-8">
            <a href="{{ route('organizer.events') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-700 font-bold text-xs uppercase tracking-wider hover:bg-slate-50 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider shadow-sm transition-colors">
                Simpan Perubahan
            </button>
        </div>
    </form>

    {{-- Kategori Section --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <h2 class="text-base font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-200">
            Daftar Kategori Lari
        </h2>

        <div class="space-y-2 mb-6">
            @foreach($event->kategori as $kat)
                <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-between text-sm">
                    <div>
                        <strong class="text-slate-900 font-bold">{{ $kat->nama_kategori }}</strong>
                        <span class="text-blue-600 font-extrabold ml-3">Rp {{ number_format($kat->harga, 0, ',', '.') }}</span>
                        <span class="text-slate-500 text-xs ml-3">({{ $kat->terisi }}/{{ $kat->kuota_peserta }} peserta)</span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Add Kategori Form --}}
        <form method="POST" action="{{ route('organizer.events.kategori.store', $event->id_event) }}" class="p-4 rounded-lg bg-slate-50 border border-slate-200">
            @csrf
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">Tambah Kategori Baru</h3>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div class="sm:col-span-2">
                    <input type="text" name="nama_kategori" required placeholder="Nama Kategori (contoh: 10K)"
                        class="w-full px-3 py-2 rounded border border-slate-300 bg-white text-slate-900 text-sm focus:outline-none focus:border-blue-600">
                </div>
                <div>
                    <input type="number" name="harga" required min="0" placeholder="Harga (Rp)"
                        class="w-full px-3 py-2 rounded border border-slate-300 bg-white text-slate-900 text-sm focus:outline-none focus:border-blue-600">
                </div>
                <div class="flex space-x-2">
                    <input type="number" name="kuota_peserta" required min="1" placeholder="Kuota"
                        class="w-full px-3 py-2 rounded border border-slate-300 bg-white text-slate-900 text-sm focus:outline-none focus:border-blue-600">
                    <button type="submit"
                        class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider flex-shrink-0 transition-colors">
                        + Tambah
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
