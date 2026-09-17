@extends('layouts.app')

@section('title', $event->nama_event . ' — Detail Event Lari')

@push('styles')
<style>
    .facility-chip { transition: all .2s ease; }
    .facility-chip:hover { transform: translateY(-1px); }
    .quota-bar { background: linear-gradient(90deg, #F05423, #FF7A4D); border-radius: 999px; }
    .sticky-ticket-box { max-height: calc(100vh - 8rem); overflow-y: auto; }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm font-medium text-slate-500 dark:text-slate-400 mb-6">
        <a href="{{ route('events.index') }}" class="hover:text-[#F05423] transition-colors">Katalog Event</a>
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        <span class="text-slate-900 dark:text-white font-bold truncate max-w-[200px] sm:max-w-none">{{ $event->nama_event }}</span>
    </div>

    {{-- ======================================================
         2-COLUMN LAYOUT: Left 65% Content | Right 35% Sticky Ticket
         ====================================================== --}}
    <div class="flex flex-col lg:flex-row gap-8 items-start">

        {{-- ===================== LEFT COLUMN ===================== --}}
        <div class="flex-1 min-w-0 space-y-6">

            {{-- Hero Banner --}}
            <div class="relative rounded-3xl overflow-hidden bg-slate-900 shadow-xl border border-slate-200 dark:border-white/10">
                <div class="aspect-video sm:aspect-[21/9] w-full relative">
                    @if($event->banner_image_url)
                        <img src="{{ $event->banner_image_url }}" alt="{{ $event->nama_event }}"
                             class="w-full h-full object-cover object-center">
                    @else
                        <img src="https://images.unsplash.com/photo-1530541930197-ff16ac917b0e?auto=format&fit=crop&w=1600&q=80"
                             alt="{{ $event->nama_event }}"
                             class="w-full h-full object-cover object-center">
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-[#0a1825] via-slate-950/50 to-transparent"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-[#0a1825]/70 via-transparent to-transparent"></div>
                </div>

                {{-- Overlay info --}}
                <div class="absolute bottom-0 left-0 right-0 p-5 sm:p-8 space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-3 py-1 rounded-xl text-xs font-black uppercase tracking-wider bg-[#F05423] text-white shadow-md">{{ $event->status_event }}</span>
                        @if($event->organizer)
                            <span class="px-3 py-1 rounded-xl text-xs font-bold bg-white/15 text-white border border-white/20 backdrop-blur-md">by {{ $event->organizer->nama }}</span>
                        @endif
                        <span class="px-3 py-1 rounded-xl text-xs font-bold bg-black/50 text-white border border-white/20 backdrop-blur-md">{{ $event->pendaftaran_count }} Peserta</span>
                    </div>
                    <h1 class="text-2xl sm:text-4xl font-black italic uppercase tracking-tight text-white leading-tight drop-shadow-md">{{ $event->nama_event }}</h1>
                    <div class="flex items-center gap-1.5 text-sm text-slate-200">
                        <svg class="w-4 h-4 text-orange-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                        <span class="font-medium">{{ $event->lokasi_venue }}</span>
                    </div>
                </div>
            </div>

            {{-- Info Stats Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @php
                    $infoItems = [
                        ['icon'=>'📅','label'=>'Tanggal Lomba','value'=> $event->tanggal_event->format('d F Y')],
                        ['icon'=>'📦','label'=>'Periode RPC','value'=> $event->tanggal_rpc_mulai->format('d M').' — '.$event->tanggal_rpc_selesai->format('d M Y')],
                        ['icon'=>'👥','label'=>'Total Peserta','value'=> $event->pendaftaran_count.' Runner'],
                    ];
                @endphp
                @foreach($infoItems as $item)
                    <div class="bg-white dark:bg-white/5 rounded-2xl border border-slate-200 dark:border-white/10 p-4 flex flex-col gap-1">
                        <span class="text-lg">{{ $item['icon'] }}</span>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wide">{{ $item['label'] }}</span>
                        <span class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $item['value'] }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Deskripsi Event --}}
            @if($event->deskripsi)
                <div class="bg-white dark:bg-white/5 rounded-2xl border border-slate-200 dark:border-white/10 p-6 space-y-3">
                    <h2 class="text-sm font-black uppercase tracking-wider text-[#F05423]">📋 Deskripsi Event</h2>
                    <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">{{ $event->deskripsi }}</p>
                </div>
            @endif

            {{-- Fasilitas Pelari --}}
            <div class="bg-white dark:bg-white/5 rounded-2xl border border-slate-200 dark:border-white/10 p-6 space-y-4">
                <h2 class="text-sm font-black uppercase tracking-wider text-[#F05423]">🎁 Fasilitas Pelari</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @php
                        $facilities = [
                            ['emoji'=>'👕','name'=>'Jersey Resmi'],
                            ['emoji'=>'🏅','name'=>'Medali Finisher'],
                            ['emoji'=>'🛡️','name'=>'Asuransi Peserta'],
                            ['emoji'=>'💧','name'=>'Water Station'],
                            ['emoji'=>'🎒','name'=>'Goodie Bag'],
                            ['emoji'=>'📸','name'=>'Official Foto'],
                            ['emoji'=>'🩹','name'=>'Tim Medis P3K'],
                            ['emoji'=>'🏆','name'=>'Hadiah Juara'],
                            ['emoji'=>'📱','name'=>'E-Certificate'],
                        ];
                    @endphp
                    @foreach($facilities as $fac)
                        <div class="facility-chip flex items-center gap-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl p-3">
                            <span class="text-xl shrink-0">{{ $fac['emoji'] }}</span>
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-200">{{ $fac['name'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Lokasi & Peta --}}
            <div class="bg-white dark:bg-white/5 rounded-2xl border border-slate-200 dark:border-white/10 p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-black uppercase tracking-wider text-[#F05423]">📍 Lokasi Venue</h2>
                    <a href="{{ $event->maps_url }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-orange-50 dark:bg-orange-500/10 text-[#F05423] hover:bg-[#F05423] hover:text-white font-bold text-xs border border-orange-200 dark:border-orange-500/30 transition-all">
                        🗺️ Buka Google Maps
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                    </a>
                </div>
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">📌 {{ $event->lokasi_venue }}</p>
                {{-- Embedded Map --}}
                <div class="w-full h-52 rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-white/10 flex items-center justify-center">
                    @php
                        $q = urlencode($event->lokasi_venue);
                        $embedUrl = "https://maps.google.com/maps?q={$q}&output=embed&z=15";
                    @endphp
                    <iframe src="{{ $embedUrl }}"
                            width="100%" height="100%"
                            style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            class="w-full h-full">
                    </iframe>
                </div>
                {{-- RPC Period --}}
                <div class="flex items-center gap-3 p-4 bg-amber-50 dark:bg-amber-500/10 rounded-xl border border-amber-200 dark:border-amber-500/30">
                    <span class="text-2xl">📦</span>
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider text-amber-700 dark:text-amber-400 block">Pengambilan Racepack (RPC)</span>
                        <span class="text-sm font-extrabold text-amber-900 dark:text-amber-200">{{ $event->tanggal_rpc_mulai->format('d M Y') }} — {{ $event->tanggal_rpc_selesai->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- Mobile: Kategori (visible only on small screens) --}}
            <div class="lg:hidden space-y-4">
                <h2 class="text-xl font-black italic uppercase tracking-tight text-slate-900 dark:text-white">🎫 Pilihan Kategori Lari</h2>
                @foreach($event->kategori as $kat)
                    @php
                        $persen = $kat->kuota_peserta > 0 ? ($kat->terisi / $kat->kuota_peserta) * 100 : 0;
                        $isAlmostFull = $persen >= 80 && $persen < 100;
                        $isFull = !$kat->is_tersedia;
                    @endphp
                    <div class="bg-white dark:bg-white/5 rounded-2xl border {{ $isFull ? 'border-slate-200 dark:border-white/8 opacity-60' : 'border-slate-200 dark:border-white/10 hover:border-[#F05423]/60' }} p-5 transition-all space-y-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-base font-black text-slate-900 dark:text-white">{{ $kat->nama_kategori }}</h3>
                                <p class="text-2xl font-black text-[#F05423] mt-1">Rp {{ number_format($kat->harga, 0, ',', '.') }}</p>
                            </div>
                            @if($isFull)
                                <span class="shrink-0 px-2.5 py-1 rounded-xl text-[10px] font-black bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400 uppercase">Penuh</span>
                            @elseif($isAlmostFull)
                                <span class="shrink-0 px-2.5 py-1 rounded-xl text-[10px] font-black bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 uppercase">Hampir Habis</span>
                            @else
                                <span class="shrink-0 px-2.5 py-1 rounded-xl text-[10px] font-black bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 uppercase">Tersedia</span>
                            @endif
                        </div>
                        <div class="space-y-1.5">
                            <div class="flex justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                                <span>Kuota</span><span>{{ $kat->terisi }} / {{ $kat->kuota_peserta }}</span>
                            </div>
                            <div class="w-full h-2 rounded-full bg-slate-100 dark:bg-white/10 overflow-hidden">
                                <div class="quota-bar h-full" style="width: {{ min($persen, 100) }}%"></div>
                            </div>
                            <p class="text-xs text-slate-400">Sisa {{ $kat->sisa_kuota }} slot</p>
                        </div>
                        @auth
                            @if($isFull)
                                <button disabled class="w-full py-3 rounded-2xl bg-slate-100 dark:bg-white/5 text-slate-400 font-bold text-xs uppercase cursor-not-allowed">Kuota Penuh</button>
                            @elseif(auth()->user()->isRunner() || auth()->user()->isSuperAdmin())
                                <a href="{{ route('runner.register-event', $event->slug) }}?kategori={{ $kat->id_kategori }}"
                                   class="block w-full text-center py-3 rounded-2xl btn-brand-orange text-white font-extrabold text-xs uppercase tracking-wider">
                                    Daftar Sekarang
                                </a>
                            @else
                                <button disabled class="w-full py-3 rounded-2xl bg-slate-100 dark:bg-white/5 text-slate-400 font-bold text-xs uppercase cursor-not-allowed">Khusus Akun Runner</button>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="block w-full text-center py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 dark:bg-white/5 dark:hover:bg-white/10 border border-slate-300 dark:border-white/15 text-slate-700 dark:text-slate-200 font-bold text-xs uppercase tracking-wider transition-all">
                                Login untuk Mendaftar
                            </a>
                        @endauth
                    </div>
                @endforeach
            </div>

        </div>{{-- /left column --}}

        {{-- ===================== RIGHT COLUMN — STICKY TICKET BOX ===================== --}}
        <div class="hidden lg:block w-[360px] xl:w-[380px] shrink-0">
            <div class="sticky top-24 sticky-ticket-box">

                {{-- Ticket Selector Card --}}
                <div class="bg-white dark:bg-[#1A3350] rounded-3xl border border-slate-200 dark:border-white/10 shadow-xl overflow-hidden">

                    {{-- Card Header --}}
                    <div class="bg-[#0F2137] px-6 py-5">
                        <div class="flex items-center gap-3 mb-2">
                            <img src="{{ asset('images/logo-icon.png') }}" alt="RunFest" class="h-7 w-auto object-contain brightness-[5] grayscale">
                            <span class="text-xs font-black uppercase tracking-widest text-slate-400">RunFest SaaS</span>
                        </div>
                        <h2 class="text-white font-black text-lg leading-tight italic uppercase tracking-tight">Pilih Kategori & Daftar</h2>
                        <p class="text-slate-400 text-xs mt-1">{{ $event->nama_event }}</p>
                    </div>

                    {{-- Event Quick Info --}}
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-white/8 bg-slate-50/50 dark:bg-white/3 flex items-center gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 font-semibold block">Tanggal</span>
                            <span class="font-extrabold text-slate-900 dark:text-white">{{ $event->tanggal_event->format('d M Y') }}</span>
                        </div>
                        <div class="w-px h-8 bg-slate-200 dark:bg-white/10"></div>
                        <div>
                            <span class="text-slate-400 font-semibold block">Lokasi</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 max-w-[150px] truncate block">{{ $event->lokasi_venue }}</span>
                        </div>
                    </div>

                    {{-- Kategori List --}}
                    <div class="px-5 py-4 space-y-3">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Kategori Tersedia</p>

                        @foreach($event->kategori as $kat)
                            @php
                                $persen = $kat->kuota_peserta > 0 ? ($kat->terisi / $kat->kuota_peserta) * 100 : 0;
                                $isAlmostFull = $persen >= 80 && $persen < 100;
                                $isFull = !$kat->is_tersedia;
                            @endphp
                            <div class="rounded-2xl border {{ $isFull ? 'border-slate-200 dark:border-white/8 opacity-55' : 'border-slate-200 dark:border-white/10 hover:border-[#F05423]/60 hover:shadow-md hover:shadow-orange-500/10' }} bg-white dark:bg-white/4 p-4 transition-all space-y-3 cursor-pointer group"
                                 onclick="{{ !$isFull && auth()->check() && (auth()->user()->isRunner() || auth()->user()->isSuperAdmin()) ? "window.location='" . route('runner.register-event', $event->slug) . "?kategori=" . $kat->id_kategori . "'" : '' }}">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <h3 class="text-sm font-black text-slate-900 dark:text-white group-hover:text-[#F05423] transition-colors">{{ $kat->nama_kategori }}</h3>
                                        <p class="text-xl font-black text-[#F05423] leading-none mt-1">Rp {{ number_format($kat->harga, 0, ',', '.') }}</p>
                                    </div>
                                    @if($isFull)
                                        <span class="shrink-0 px-2 py-0.5 rounded-lg text-[10px] font-black bg-slate-200 dark:bg-slate-700 text-slate-400 uppercase mt-0.5">Penuh</span>
                                    @elseif($isAlmostFull)
                                        <span class="shrink-0 px-2 py-0.5 rounded-lg text-[10px] font-black bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 uppercase mt-0.5 animate-pulse">Hampir Habis</span>
                                    @else
                                        <span class="shrink-0 px-2 py-0.5 rounded-lg text-[10px] font-black bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 uppercase mt-0.5">Tersedia</span>
                                    @endif
                                </div>

                                {{-- Progress Bar --}}
                                <div class="space-y-1">
                                    <div class="w-full h-1.5 rounded-full bg-slate-100 dark:bg-white/10 overflow-hidden">
                                        <div class="quota-bar h-full transition-all" style="width: {{ min($persen, 100) }}%"></div>
                                    </div>
                                    <p class="text-[10px] text-slate-400 font-medium">{{ $kat->terisi }}/{{ $kat->kuota_peserta }} peserta · Sisa {{ $kat->sisa_kuota }} slot</p>
                                </div>

                                {{-- CTA Button --}}
                                @auth
                                    @if($isFull)
                                        <button disabled class="w-full py-2.5 rounded-xl bg-slate-100 dark:bg-white/5 text-slate-400 font-bold text-xs uppercase cursor-not-allowed">Kuota Penuh</button>
                                    @elseif(auth()->user()->isRunner() || auth()->user()->isSuperAdmin())
                                        <a href="{{ route('runner.register-event', $event->slug) }}?kategori={{ $kat->id_kategori }}"
                                           class="block w-full text-center py-2.5 rounded-xl btn-brand-orange text-white font-extrabold text-xs uppercase tracking-wider"
                                           onclick="event.stopPropagation()">
                                            Pilih & Daftar →
                                        </a>
                                    @else
                                        <button disabled class="w-full py-2.5 rounded-xl bg-slate-100 dark:bg-white/5 text-slate-400 font-bold text-xs uppercase cursor-not-allowed">Khusus Runner</button>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}"
                                       class="block w-full text-center py-2.5 rounded-xl bg-[#0F2137] hover:bg-[#1A3350] text-white font-extrabold text-xs uppercase tracking-wider transition-all"
                                       onclick="event.stopPropagation()">
                                        Login untuk Daftar
                                    </a>
                                @endauth
                            </div>
                        @endforeach
                    </div>

                    {{-- Footer guarantee --}}
                    <div class="px-6 pb-5 pt-1">
                        <div class="flex items-center gap-2 text-[11px] text-slate-400 font-medium">
                            <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Pembayaran aman via Midtrans · E-Ticket langsung diterbitkan
                        </div>
                    </div>
                </div>

                {{-- Organizer Info --}}
                @if($event->organizer)
                    <div class="mt-4 bg-white dark:bg-white/5 rounded-2xl border border-slate-200 dark:border-white/10 p-4 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-[#F05423] text-white font-extrabold text-sm flex items-center justify-center shrink-0 shadow-sm">
                            {{ strtoupper(substr($event->organizer->nama, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wide">Diselenggarakan oleh</p>
                            <p class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $event->organizer->nama }}</p>
                        </div>
                    </div>
                @endif

            </div>
        </div>{{-- /right column --}}

    </div>{{-- /2-col --}}
</div>
@endsection