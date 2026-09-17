@extends('layouts.app')

@section('title', 'Edit Event - ' . $event->nama_event)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Top Navigation & Header --}}
    <div class="mb-6">
        <a href="{{ route('organizer.events') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 hover:text-[#F05423] dark:hover:text-[#F05423] transition-colors mb-2 group">
            <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            <span>Kembali ke Daftar Event</span>
        </a>
        <h1 class="text-2xl sm:text-3xl font-black italic uppercase tracking-tight text-slate-900 dark:text-white">
            Edit Event: <span class="text-[#F05423]">{{ $event->nama_event }}</span>
        </h1>
        <p class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400 mt-1">
            Perbarui data detail event, venue, dan status publikasi.
        </p>
    </div>

    {{-- Edit Form --}}
    <form method="POST" action="{{ route('organizer.events.update', $event->id_event) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="bg-white dark:bg-[#0f2137] rounded-2xl border border-slate-200 dark:border-white/10 shadow-sm p-6 sm:p-8 mb-6 transition-colors">
            <h2 class="text-base font-black italic uppercase tracking-wider text-slate-900 dark:text-white mb-6 pb-3 border-b border-slate-200 dark:border-white/10 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#F05423]"></span>
                Detail Informasi Event
            </h2>

            <div class="space-y-5">
                {{-- Foto / Banner Event --}}
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Foto / Banner Event Saat Ini
                    </label>

                    @if($event->banner_image_url)
                        <div class="mb-3">
                            <div class="w-full max-h-60 rounded-xl overflow-hidden border border-slate-200 dark:border-white/10 bg-slate-100 dark:bg-[#081624] relative shadow-inner">
                                <img src="{{ $event->banner_image_url }}" alt="{{ $event->nama_event }}" class="w-full h-48 sm:h-60 object-cover object-center">
                            </div>
                        </div>
                    @else
                        <div class="mb-3 p-4 rounded-xl border border-dashed border-slate-300 dark:border-white/15 bg-slate-50 dark:bg-[#081624] text-center">
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400">Belum ada foto event. Sistem menggunakan placeholder default.</p>
                        </div>
                    @endif

                    <label for="banner" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Ganti Foto / Banner <span class="text-slate-400 font-normal text-[11px] lowercase">(opsional)</span>
                    </label>
                    <div class="space-y-3">
                        <input type="file" id="banner" name="banner" accept="image/jpeg,image/png,image/jpg,image/webp"
                            onchange="previewBanner(this)"
                            class="block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:italic file:uppercase file:tracking-wider file:bg-orange-50 dark:file:bg-orange-950/40 file:text-[#F05423] hover:file:bg-orange-100 dark:hover:file:bg-orange-900/40 border border-slate-300 dark:border-white/15 rounded-xl cursor-pointer bg-slate-50 dark:bg-[#081624] transition-colors">
                        
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Biarkan kosong jika tidak ingin mengubah foto. Format: JPG, JPEG, PNG, WEBP (Maks. 2MB).
                        </p>
                        @error('banner') 
                            <p class="text-xs text-rose-600 dark:text-rose-400 font-bold mt-1">{{ $message }}</p> 
                        @enderror

                        {{-- Preview Box Foto Baru --}}
                        <div id="banner-preview-wrapper" class="hidden">
                            <span class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1.5">Preview Foto Baru:</span>
                            <div class="w-full max-h-60 rounded-xl overflow-hidden border border-orange-200 dark:border-orange-500/30 bg-orange-50/50 dark:bg-orange-950/20 relative shadow-inner">
                                <img id="banner-preview" src="#" alt="Preview Banner Baru" class="w-full h-48 sm:h-60 object-cover object-center">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Nama Event --}}
                <div>
                    <label for="nama_event" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Nama Event <span class="text-[#F05423]">*</span>
                    </label>
                    <input type="text" id="nama_event" name="nama_event" value="{{ old('nama_event', $event->nama_event) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all">
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
                        <input type="date" id="tanggal_event" name="tanggal_event" value="{{ old('tanggal_event', $event->tanggal_event->format('Y-m-d')) }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all">
                    </div>
                    <div>
                        <label for="lokasi_venue" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Lokasi Venue <span class="text-[#F05423]">*</span>
                        </label>
                        <input type="text" id="lokasi_venue" name="lokasi_venue" value="{{ old('lokasi_venue', $event->lokasi_venue) }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all">
                    </div>
                </div>

                {{-- Google Maps URL --}}
                <div>
                    <label for="google_maps_url" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Lokasi Google Maps <span class="text-slate-400 font-normal text-[11px] lowercase">(opsional)</span>
                    </label>
                    <input type="url" id="google_maps_url" name="google_maps_url" value="{{ old('google_maps_url', $event->google_maps_url) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all"
                        placeholder="https://maps.google.com/?q=... atau https://maps.app.goo.gl/...">
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Link maps venue untuk navigasi peserta.
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
                        <input type="number" step="any" id="latitude" name="latitude" value="{{ old('latitude', $event->latitude) }}"
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
                        <input type="number" step="any" id="longitude" name="longitude" value="{{ old('longitude', $event->longitude) }}"
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
                        <input type="date" id="tanggal_rpc_mulai" name="tanggal_rpc_mulai" value="{{ old('tanggal_rpc_mulai', $event->tanggal_rpc_mulai->format('Y-m-d')) }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all">
                    </div>
                    <div>
                        <label for="tanggal_rpc_selesai" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Tanggal RPC Selesai <span class="text-[#F05423]">*</span>
                        </label>
                        <input type="date" id="tanggal_rpc_selesai" name="tanggal_rpc_selesai" value="{{ old('tanggal_rpc_selesai', $event->tanggal_rpc_selesai->format('Y-m-d')) }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all">
                    </div>
                </div>

                {{-- Deskripsi Event --}}
                <div>
                    <label for="deskripsi" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Deskripsi Event
                    </label>
                    <textarea id="deskripsi" name="deskripsi" rows="3"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all resize-none">{{ old('deskripsi', $event->deskripsi) }}</textarea>
                </div>

                {{-- Status Event --}}
                <div>
                    <label for="status_event" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Status Publikasi <span class="text-[#F05423]">*</span>
                    </label>
                    <select id="status_event" name="status_event" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all">
                        @foreach(['Draft', 'Publikasi', 'Selesai', 'Dibatalkan'] as $status)
                            <option value="{{ $status }}" {{ old('status_event', $event->status_event) == $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center justify-end gap-3 mb-8">
            <a href="{{ route('organizer.events') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-700 dark:text-slate-300 font-bold text-xs uppercase tracking-wider hover:bg-slate-50 dark:hover:bg-[#122438] transition-colors">
                Batal
            </a>
            <button type="submit" class="px-7 py-2.5 rounded-xl bg-[#F05423] hover:bg-[#d94416] text-white font-black italic uppercase tracking-wider text-xs shadow-lg shadow-orange-500/25 hover:shadow-orange-500/40 transition-all">
                Simpan Perubahan
            </button>
        </div>
    </form>

    {{-- Kategori Section --}}
    <div class="bg-white dark:bg-[#0f2137] rounded-2xl border border-slate-200 dark:border-white/10 shadow-sm p-6 sm:p-8 transition-colors">
        <h2 class="text-base font-black italic uppercase tracking-wider text-slate-900 dark:text-white mb-4 pb-3 border-b border-slate-200 dark:border-white/10 flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
            Daftar Kategori Lari
        </h2>

        <div class="space-y-2.5 mb-6">
            @foreach($event->kategori as $kat)
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#081624]/60 border border-slate-200 dark:border-white/10 flex flex-wrap items-center justify-between gap-3 text-sm">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-[#F05423]"></span>
                        <strong class="text-slate-900 dark:text-white font-black italic uppercase">{{ $kat->nama_kategori }}</strong>
                        <span class="text-[#F05423] font-black text-sm">Rp {{ number_format($kat->harga, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $kat->terisi >= $kat->kuota_peserta ? 'bg-rose-100 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400' : 'bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400' }}">
                            {{ $kat->terisi }} / {{ $kat->kuota_peserta }} Peserta
                        </span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Add Kategori Form --}}
        <form method="POST" action="{{ route('organizer.events.kategori.store', $event->id_event) }}" class="p-5 rounded-xl bg-slate-50 dark:bg-[#081624]/60 border border-slate-200 dark:border-white/10">
            @csrf
            <h3 class="text-xs font-black italic uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-3 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[#F05423]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Kategori Baru
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div class="sm:col-span-2">
                    <input type="text" name="nama_kategori" required placeholder="Nama Kategori (contoh: 10K Open)"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white text-sm focus:outline-none focus:border-[#F05423]">
                </div>
                <div>
                    <input type="number" name="harga" required min="0" placeholder="Harga (Rp)"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white text-sm focus:outline-none focus:border-[#F05423]">
                </div>
                <div class="flex space-x-2">
                    <input type="number" name="kuota_peserta" required min="1" placeholder="Kuota"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white text-sm focus:outline-none focus:border-[#F05423]">
                    <button type="submit"
                        class="px-4 py-2.5 rounded-lg bg-[#F05423] hover:bg-[#d94416] text-white font-black italic uppercase tracking-wider text-xs flex-shrink-0 transition-colors shadow-md shadow-orange-500/20">
                        + Tambah
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
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