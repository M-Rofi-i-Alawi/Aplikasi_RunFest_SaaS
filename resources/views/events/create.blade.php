@extends('layouts.app')

@section('title', 'Buat Event Baru')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('organizer.events') }}" class="inline-flex items-center space-x-2 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-colors mb-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            <span>Kembali ke Daftar Event</span>
        </a>
        <h1 class="text-2xl font-extrabold text-slate-900">Buat Event Lari Baru</h1>
        <p class="text-sm text-slate-500">Lengkapi formulir detail event dan kategori perlombaan.</p>
    </div>

    <form method="POST" action="{{ route('organizer.events.store') }}" id="eventForm">
        @csrf

        {{-- Detail Event Card --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 sm:p-8 mb-6">
            <h2 class="text-base font-bold text-slate-900 uppercase tracking-wider mb-6 pb-2 border-b border-slate-200">
                1. Detail Informasi Event
            </h2>

            <div class="space-y-4">
                <div>
                    <label for="nama_event" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Event <span class="text-rose-500">*</span></label>
                    <input type="text" id="nama_event" name="nama_event" value="{{ old('nama_event') }}" required
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors"
                        placeholder="Contoh: Jakarta Night Run 2026">
                    @error('nama_event') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="tanggal_event" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Pelaksanaan <span class="text-rose-500">*</span></label>
                        <input type="date" id="tanggal_event" name="tanggal_event" value="{{ old('tanggal_event') }}" required
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors">
                        @error('tanggal_event') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="lokasi_venue" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Lokasi Venue <span class="text-rose-500">*</span></label>
                        <input type="text" id="lokasi_venue" name="lokasi_venue" value="{{ old('lokasi_venue') }}" required
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors"
                            placeholder="Contoh: GBK Senayan, Jakarta">
                        @error('lokasi_venue') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="tanggal_rpc_mulai" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal RPC Mulai <span class="text-rose-500">*</span></label>
                        <input type="date" id="tanggal_rpc_mulai" name="tanggal_rpc_mulai" value="{{ old('tanggal_rpc_mulai') }}" required
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors">
                        @error('tanggal_rpc_mulai') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="tanggal_rpc_selesai" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal RPC Selesai <span class="text-rose-500">*</span></label>
                        <input type="date" id="tanggal_rpc_selesai" name="tanggal_rpc_selesai" value="{{ old('tanggal_rpc_selesai') }}" required
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors">
                        @error('tanggal_rpc_selesai') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="deskripsi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Deskripsi Event</label>
                    <textarea id="deskripsi" name="deskripsi" rows="3"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors resize-none"
                        placeholder="Deskripsi singkat mengenai event ini...">{{ old('deskripsi') }}</textarea>
                </div>

                <div>
                    <label for="status_event" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Status Publikasi <span class="text-rose-500">*</span></label>
                    <select id="status_event" name="status_event" required
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors">
                        <option value="Draft" {{ old('status_event') == 'Draft' ? 'selected' : '' }}>Draft (Belum Tampil Publik)</option>
                        <option value="Publikasi" {{ old('status_event') == 'Publikasi' ? 'selected' : '' }}>Publikasi (Langsung Tampil di Katalog)</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Dynamic Categories Card --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 sm:p-8 mb-6">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3 mb-6">
                <h2 class="text-base font-bold text-slate-900 uppercase tracking-wider">
                    2. Kategori Perlombaan
                </h2>
                <button type="button" onclick="addKategori()"
                    class="px-3.5 py-1.5 rounded bg-blue-50 text-blue-700 font-bold text-xs hover:bg-blue-100 border border-blue-200 transition-colors">
                    + Tambah Kategori
                </button>
            </div>

            <div id="kategori-container" class="space-y-4">
                <div class="kategori-row grid grid-cols-1 sm:grid-cols-4 gap-3 p-4 rounded-lg bg-slate-50 border border-slate-200">
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Nama Kategori *</label>
                        <input type="text" name="kategori[0][nama_kategori]" required
                            class="w-full px-3 py-2 rounded border border-slate-300 bg-white text-slate-900 text-sm focus:outline-none focus:border-blue-600"
                            placeholder="Contoh: 10K Competitive">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Harga (Rp) *</label>
                        <input type="number" name="kategori[0][harga]" required min="0"
                            class="w-full px-3 py-2 rounded border border-slate-300 bg-white text-slate-900 text-sm focus:outline-none focus:border-blue-600"
                            placeholder="150000">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Kuota Peserta *</label>
                        <input type="number" name="kategori[0][kuota_peserta]" required min="1"
                            class="w-full px-3 py-2 rounded border border-slate-300 bg-white text-slate-900 text-sm focus:outline-none focus:border-blue-600"
                            placeholder="500">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('organizer.events') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-700 font-bold text-xs uppercase tracking-wider hover:bg-slate-50 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider shadow-sm transition-colors">
                Simpan Event
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let kategoriIndex = 1;
    function addKategori() {
        const container = document.getElementById('kategori-container');
        const row = document.createElement('div');
        row.className = 'kategori-row grid grid-cols-1 sm:grid-cols-4 gap-3 p-4 rounded-lg bg-slate-50 border border-slate-200 relative';
        row.innerHTML = `
            <div class="sm:col-span-2">
                <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Nama Kategori *</label>
                <input type="text" name="kategori[${kategoriIndex}][nama_kategori]" required
                    class="w-full px-3 py-2 rounded border border-slate-300 bg-white text-slate-900 text-sm focus:outline-none focus:border-blue-600"
                    placeholder="Contoh: 5K Fun Run">
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Harga (Rp) *</label>
                <input type="number" name="kategori[${kategoriIndex}][harga]" required min="0"
                    class="w-full px-3 py-2 rounded border border-slate-300 bg-white text-slate-900 text-sm focus:outline-none focus:border-blue-600"
                    placeholder="100000">
            </div>
            <div class="relative">
                <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Kuota Peserta *</label>
                <input type="number" name="kategori[${kategoriIndex}][kuota_peserta]" required min="1"
                    class="w-full px-3 py-2 rounded border border-slate-300 bg-white text-slate-900 text-sm focus:outline-none focus:border-blue-600"
                    placeholder="200">
                <button type="button" onclick="this.closest('.kategori-row').remove()"
                    class="absolute -top-2 -right-2 w-5 h-5 rounded-full bg-rose-600 text-white font-bold flex items-center justify-center text-xs shadow-sm hover:bg-rose-700">
                    ✕
                </button>
            </div>
        `;
        container.appendChild(row);
        kategoriIndex++;
    }
</script>
@endpush
