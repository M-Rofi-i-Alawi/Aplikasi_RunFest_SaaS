@extends('layouts.app')

@section('title', 'Buat Event Baru - RunFest SaaS')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Top Navigation & Header --}}
    <div class="mb-6">
        <a href="{{ route('organizer.events') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 hover:text-[#F05423] dark:hover:text-[#F05423] transition-colors mb-2 group">
            <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            <span>Kembali ke Daftar Event</span>
        </a>
        <h1 class="text-2xl sm:text-3xl font-black italic uppercase tracking-tight text-slate-900 dark:text-white">
            Buat Event Lari <span class="text-[#F05423]">Baru</span>
        </h1>
        <p class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400 mt-1">
            Lengkapi formulir detail event, lokasi venue, dan kategori perlombaan.
        </p>
    </div>

    <form method="POST" action="{{ route('organizer.events.store') }}" id="eventForm" enctype="multipart/form-data">
        @csrf

        {{-- Detail Event Card --}}
        <div class="bg-white dark:bg-[#0f2137] rounded-2xl border border-slate-200 dark:border-white/10 shadow-sm p-6 sm:p-8 mb-6 transition-colors">
            <h2 class="text-base font-black italic uppercase tracking-wider text-slate-900 dark:text-white mb-6 pb-3 border-b border-slate-200 dark:border-white/10 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#F05423]"></span>
                1. Detail Informasi Event
            </h2>

            <div class="space-y-5">
                {{-- Foto / Banner Event --}}
                <div>
                    <label for="banner" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Foto / Banner Event <span class="text-slate-400 font-normal text-[11px] lowercase">(opsional)</span>
                    </label>
                    <div class="space-y-3">
                        <input type="file" id="banner" name="banner" accept="image/jpeg,image/png,image/jpg,image/webp"
                            onchange="previewBanner(this)"
                            class="block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:italic file:uppercase file:tracking-wider file:bg-orange-50 dark:file:bg-orange-950/40 file:text-[#F05423] hover:file:bg-orange-100 dark:hover:file:bg-orange-900/40 border border-slate-300 dark:border-white/15 rounded-xl cursor-pointer bg-slate-50 dark:bg-[#081624] transition-colors">
                        
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Upload foto atau banner resmi event agar peserta dapat mengenali event dengan mudah. (Format: JPG, JPEG, PNG, WEBP, Maks. 2MB)
                        </p>
                        @error('banner') 
                            <p class="text-xs text-rose-600 dark:text-rose-400 font-bold mt-1">{{ $message }}</p> 
                        @enderror

                        {{-- Preview Box --}}
                        <div id="banner-preview-wrapper" class="hidden">
                            <span class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1.5">Preview Banner Terpilih:</span>
                            <div class="w-full max-h-60 rounded-xl overflow-hidden border border-slate-200 dark:border-white/10 bg-slate-100 dark:bg-[#081624] relative shadow-inner">
                                <img id="banner-preview" src="#" alt="Preview Banner" class="w-full h-48 sm:h-60 object-cover object-center">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Nama Event --}}
                <div>
                    <label for="nama_event" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Nama Event <span class="text-[#F05423]">*</span>
                    </label>
                    <input type="text" id="nama_event" name="nama_event" value="{{ old('nama_event') }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all"
                        placeholder="Contoh: Jakarta Night Run 2026">
                    @error('nama_event') 
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-bold">{{ $message }}</p> 
                    @enderror
                </div>

                {{-- Tanggal & Venue --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="tanggal_event" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Tanggal Pelaksanaan <span class="text-[#F05423]">*</span>
                        </label>
                        <input type="date" id="tanggal_event" name="tanggal_event" value="{{ old('tanggal_event') }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all">
                        @error('tanggal_event') 
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-bold">{{ $message }}</p> 
                        @enderror
                    </div>
                    <div>
                        <label for="lokasi_venue" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Lokasi Venue <span class="text-[#F05423]">*</span>
                        </label>
                        <input type="text" id="lokasi_venue" name="lokasi_venue" value="{{ old('lokasi_venue') }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all"
                            placeholder="Contoh: GBK Senayan, Jakarta">
                        @error('lokasi_venue') 
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-bold">{{ $message }}</p> 
                        @enderror
                    </div>
                </div>

                {{-- Google Maps URL --}}
                <div>
                    <label for="google_maps_url" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Lokasi Google Maps <span class="text-slate-400 font-normal text-[11px] lowercase">(opsional)</span>
                    </label>
                    <input type="url" id="google_maps_url" name="google_maps_url" value="{{ old('google_maps_url') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all"
                        placeholder="https://maps.google.com/?q=... atau https://maps.app.goo.gl/...">
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Link maps ini memudahkan peserta membuka rute navigasi di ponsel mereka.
                    </p>
                    @error('google_maps_url') 
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-bold">{{ $message }}</p> 
                    @enderror
                </div>

                {{-- Koordinat Opsional --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="latitude" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Latitude <span class="text-slate-400 font-normal text-[11px] lowercase">(opsional)</span>
                        </label>
                        <input type="number" step="any" id="latitude" name="latitude" value="{{ old('latitude') }}"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all"
                            placeholder="-6.218335">
                        @error('latitude') 
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-bold">{{ $message }}</p> 
                        @enderror
                    </div>
                    <div>
                        <label for="longitude" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Longitude <span class="text-slate-400 font-normal text-[11px] lowercase">(opsional)</span>
                        </label>
                        <input type="number" step="any" id="longitude" name="longitude" value="{{ old('longitude') }}"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all"
                            placeholder="106.802216">
                        @error('longitude') 
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-bold">{{ $message }}</p> 
                        @enderror
                    </div>
                </div>

                {{-- Periode Pengambilan Racepack (RPC) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="tanggal_rpc_mulai" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Tanggal RPC Mulai <span class="text-[#F05423]">*</span>
                        </label>
                        <input type="date" id="tanggal_rpc_mulai" name="tanggal_rpc_mulai" value="{{ old('tanggal_rpc_mulai') }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all">
                        @error('tanggal_rpc_mulai') 
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-bold">{{ $message }}</p> 
                        @enderror
                    </div>
                    <div>
                        <label for="tanggal_rpc_selesai" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Tanggal RPC Selesai <span class="text-[#F05423]">*</span>
                        </label>
                        <input type="date" id="tanggal_rpc_selesai" name="tanggal_rpc_selesai" value="{{ old('tanggal_rpc_selesai') }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all">
                        @error('tanggal_rpc_selesai') 
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-bold">{{ $message }}</p> 
                        @enderror
                    </div>
                </div>

                {{-- Deskripsi Event --}}
                <div>
                    <label for="deskripsi" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Deskripsi Event
                    </label>
                    <textarea id="deskripsi" name="deskripsi" rows="3"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all resize-none"
                        placeholder="Deskripsi singkat mengenai rute lomba, fasilitas pelari, medali, dan jersey...">{{ old('deskripsi') }}</textarea>
                </div>

                {{-- Status Event --}}
                <div>
                    <label for="status_event" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Status Publikasi <span class="text-[#F05423]">*</span>
                    </label>
                    <select id="status_event" name="status_event" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all">
                        <option value="Draft" {{ old('status_event') == 'Draft' ? 'selected' : '' }}>Draft (Belum Tampil Publik)</option>
                        <option value="Publikasi" {{ old('status_event') == 'Publikasi' ? 'selected' : '' }}>Publikasi (Langsung Tampil di Katalog)</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Dynamic Categories Card --}}
        <div class="bg-white dark:bg-[#0f2137] rounded-2xl border border-slate-200 dark:border-white/10 shadow-sm p-6 sm:p-8 mb-6 transition-colors">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-white/10 pb-3 mb-6">
                <h2 class="text-base font-black italic uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    2. Kategori Perlombaan
                </h2>
                <button type="button" onclick="addKategori()"
                    class="px-4 py-2 rounded-xl bg-orange-50 dark:bg-orange-950/40 text-[#F05423] hover:bg-orange-100 dark:hover:bg-orange-900/40 border border-orange-200 dark:border-orange-500/30 font-black italic uppercase tracking-wider text-xs transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Tambah Kategori
                </button>
            </div>

            <div id="kategori-container" class="space-y-4">
                <div class="kategori-row grid grid-cols-1 sm:grid-cols-4 gap-3.5 p-4 rounded-xl bg-slate-50 dark:bg-[#081624]/60 border border-slate-200 dark:border-white/10 relative">
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1">Nama Kategori *</label>
                        <input type="text" name="kategori[0][nama_kategori]" required
                            class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white text-sm focus:outline-none focus:border-[#F05423]"
                            placeholder="Contoh: 10K Competitive">
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1">Harga (Rp) *</label>
                        <input type="number" name="kategori[0][harga]" required min="0"
                            class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white text-sm focus:outline-none focus:border-[#F05423]"
                            placeholder="150000">
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1">Kuota Peserta *</label>
                        <input type="number" name="kategori[0][kuota_peserta]" required min="1"
                            class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white text-sm focus:outline-none focus:border-[#F05423]"
                            placeholder="500">
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('organizer.events') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-700 dark:text-slate-300 font-bold text-xs uppercase tracking-wider hover:bg-slate-50 dark:hover:bg-[#122438] transition-colors">
                Batal
            </a>
            <button type="submit" class="px-7 py-2.5 rounded-xl bg-[#F05423] hover:bg-[#d94416] text-white font-black italic uppercase tracking-wider text-xs shadow-lg shadow-orange-500/25 hover:shadow-orange-500/40 transition-all">
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
        row.className = 'kategori-row grid grid-cols-1 sm:grid-cols-4 gap-3.5 p-4 rounded-xl bg-slate-50 dark:bg-[#081624]/60 border border-slate-200 dark:border-white/10 relative';
        row.innerHTML = `
            <div class="sm:col-span-2">
                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1">Nama Kategori *</label>
                <input type="text" name="kategori[${kategoriIndex}][nama_kategori]" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white text-sm focus:outline-none focus:border-[#F05423]"
                    placeholder="Contoh: 5K Fun Run">
            </div>
            <div>
                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1">Harga (Rp) *</label>
                <input type="number" name="kategori[${kategoriIndex}][harga]" required min="0"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white text-sm focus:outline-none focus:border-[#F05423]"
                    placeholder="100000">
            </div>
            <div class="relative">
                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1">Kuota Peserta *</label>
                <input type="number" name="kategori[${kategoriIndex}][kuota_peserta]" required min="1"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white text-sm focus:outline-none focus:border-[#F05423]"
                    placeholder="200">
                <button type="button" onclick="this.closest('.kategori-row').remove()"
                    class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-rose-600 text-white font-bold flex items-center justify-center text-xs shadow-md hover:bg-rose-700 transition-colors" title="Hapus Kategori">
                    ✕
                </button>
            </div>
        `;
        container.appendChild(row);
        kategoriIndex++;
    }

    function previewBanner(input) {
        const previewWrapper = document.getElementById('banner-preview-wrapper');
        const previewImg = document.getElementById('banner-preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewWrapper.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            previewWrapper.classList.add('hidden');
        }
    }
</script>
@endpush