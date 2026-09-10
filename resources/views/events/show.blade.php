@extends('layouts.app')

@section('title', $event->nama_event . ' — Detail Event')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    {{-- Back Link --}}
    <a href="{{ route('events.index') }}" class="inline-flex items-center space-x-2 text-sm font-bold text-slate-600 hover:text-[#ff5500] transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
        <span>Kembali ke Katalog Event</span>
    </a>

    {{-- Main Detail Card (Light Mode) --}}
    <div class="bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-sm">
        
        {{-- Banner Image & Gradient Overlay --}}
        <div class="h-64 sm:h-80 relative overflow-hidden bg-slate-900">
            <img src="{{ $event->banner_url ?: 'https://images.unsplash.com/photo-1530541930197-ff16ac917b0e?auto=format&fit=crop&w=1200&q=80' }}"
                 alt="{{ $event->nama_event }}"
                 class="w-full h-full object-cover object-center">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent"></div>
            
            <div class="absolute bottom-6 left-6 right-6 z-10 space-y-2">
                <div class="flex items-center justify-between gap-4">
                    <span class="px-3 py-1 rounded-xl text-xs font-black uppercase tracking-wider bg-[#ff5500] text-white shadow-md">
                        {{ $event->status_event }}
                    </span>
                    <span class="px-3 py-1 rounded-xl text-xs font-bold bg-white/20 text-white border border-white/20 backdrop-blur-md">
                        Organizer: {{ $event->organizer ? $event->organizer->nama : '#' . $event->id_organizer }}
                    </span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight drop-shadow-md">{{ $event->nama_event }}</h1>
                <p class="text-slate-200 text-sm flex items-center space-x-2">
                    <svg class="w-4 h-4 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                    <span>{{ $event->lokasi_venue }}</span>
                </p>
            </div>
        </div>

        <div class="p-6 sm:p-8 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-5 rounded-2xl bg-slate-50 border border-slate-200 text-sm">
                <div>
                    <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block mb-0.5">Tanggal Event</span>
                    <span class="font-extrabold text-slate-900 text-base">{{ $event->tanggal_event->format('d F Y') }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block mb-0.5">Lokasi Venue</span>
                    <span class="font-extrabold text-slate-900 text-base">{{ $event->lokasi_venue }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block mb-0.5">Periode Racepack (RPC)</span>
                    <span class="font-extrabold text-slate-900 text-base">{{ $event->tanggal_rpc_mulai->format('d M') }} — {{ $event->tanggal_rpc_selesai->format('d M Y') }}</span>
                </div>
            </div>

            @if($event->deskripsi)
                <div class="text-slate-700 text-sm leading-relaxed space-y-2">
                    <h3 class="font-bold text-slate-900 uppercase tracking-wider text-xs">Deskripsi Event</h3>
                    <p class="bg-slate-50 p-5 rounded-2xl border border-slate-200">{{ $event->deskripsi }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Kategori Cards --}}
    <h2 class="text-2xl font-extrabold text-slate-900 pt-4">Pilihan Kategori Lari</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($event->kategori as $kat)
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between space-y-4 hover:border-orange-300 transition-all">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">{{ $kat->nama_kategori }}</h3>
                    <p class="text-2xl font-black text-[#ff5500] mb-4">
                        Rp {{ number_format($kat->harga, 0, ',', '.') }}
                    </p>

                    <div class="mb-5 space-y-1.5">
                        <div class="flex justify-between text-xs font-semibold text-slate-600">
                            <span>Ketersediaan Kuota</span>
                            <span>{{ $kat->terisi }} / {{ $kat->kuota_peserta }} Peserta</span>
                        </div>
                        <div class="w-full h-2.5 rounded-full bg-slate-100 overflow-hidden border border-slate-200">
                            @php $persen = $kat->kuota_peserta > 0 ? ($kat->terisi / $kat->kuota_peserta) * 100 : 0; @endphp
                            <div class="h-full bg-gradient-to-r from-[#ff5500] to-[#ff7700] rounded-full" style="width: {{ min($persen, 100) }}%"></div>
                        </div>
                        <p class="text-xs text-slate-500 font-medium">Sisa {{ $kat->sisa_kuota }} slot kuota lagi.</p>
                    </div>
                </div>

                <div>
                    @auth
                        @if(!$kat->is_tersedia)
                            <button disabled class="w-full py-3 rounded-2xl bg-slate-200 text-slate-500 font-bold text-xs uppercase cursor-not-allowed">
                                Kuota Penuh
                            </button>
                        @elseif(auth()->user()->isRunner() || auth()->user()->isSuperAdmin())
                            <a href="{{ route('runner.register-event', $event->slug) }}?kategori={{ $kat->id_kategori }}"
                               class="block w-full text-center py-3 rounded-2xl btn-brand-orange text-white font-extrabold text-xs uppercase tracking-wider transition-all">
                                Daftar Sekarang
                            </a>
                        @else
                            <button disabled class="w-full py-3 rounded-2xl bg-slate-100 border border-slate-200 text-slate-400 font-bold text-xs uppercase cursor-not-allowed">
                                Khusus Akun Runner
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                           class="block w-full text-center py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-800 font-bold text-xs uppercase tracking-wider transition-all">
                            Login untuk Mendaftar
                        </a>
                    @endauth
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
