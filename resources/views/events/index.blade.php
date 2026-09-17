@extends('layouts.app')

@section('title', 'Katalog Event Lari — RunFest SaaS')

@push('styles')
<style>
    .pill-filter-btn { transition: all .2s ease; }
    .pill-filter-btn.active {
        background: #F05423; color: #fff;
        box-shadow: 0 4px 14px -4px rgba(240,84,35,.5);
    }
    .pill-filter-btn:not(.active):hover {
        background: #fff7f5; color: #F05423; border-color: #F05423;
    }
    .dark .pill-filter-btn:not(.active):hover { background: rgba(240,84,35,.12); color: #FF8060; }
    .event-card { transition: all .3s cubic-bezier(.4,0,.2,1); }
    .event-card.hidden-by-filter { display: none !important; }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- ======================================================
         HERO CAROUSEL BANNER (Modern Split Showcase)
         ====================================================== --}}
    @if($events->count() > 0)
        @php $featuredEvents = $events->take(3); @endphp
        <div class="relative rounded-3xl overflow-hidden bg-white dark:bg-[#0f2137] border border-slate-200 dark:border-white/10 shadow-xl mb-10 transition-colors">
            <div id="hero-slider" class="relative">
                @foreach($featuredEvents as $index => $event)
                    <div class="slide-item transition-all duration-500 {{ $index === 0 ? 'block' : 'hidden' }}" data-slide="{{ $index }}">
                        <div class="grid grid-cols-1 lg:grid-cols-12 items-stretch">
                            
                            {{-- 1. Image Viewport (Natural 16:9 Aspect Ratio on Mobile) --}}
                            <div class="lg:col-span-7 relative overflow-hidden bg-slate-950 aspect-video lg:aspect-auto lg:min-h-[420px] group/img">
                                <img src="{{ $event->banner_image_url ?? 'https://images.unsplash.com/photo-1530541930197-ff16ac917b0e?auto=format&fit=crop&w=1600&q=80' }}"
                                     alt="{{ $event->nama_event }}"
                                     class="w-full h-full object-cover object-center group-hover/img:scale-105 transition-transform duration-700">
                                
                                {{-- Subtle vignette on mobile --}}
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20 lg:hidden"></div>

                                {{-- Floating Badges on Image --}}
                                <div class="absolute top-3.5 left-3.5 z-10 flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-[#F05423] text-white shadow-md">
                                        <span>🔥</span> Event Pilihan
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-black/55 text-white backdrop-blur-md border border-white/20">
                                        👥 {{ $event->pendaftaran_count }} Peserta
                                    </span>
                                </div>

                                {{-- Mobile Slide Indicator Pill --}}
                                <div class="absolute top-3.5 right-3.5 z-10 lg:hidden bg-black/60 backdrop-blur-md px-2.5 py-1 rounded-full text-[11px] font-extrabold text-white/90 border border-white/20">
                                    {{ $index + 1 }} / {{ $featuredEvents->count() }}
                                </div>
                            </div>

                            {{-- 2. Detail Body (Seamless Connected Card, Spans 5 Cols on Desktop) --}}
                            <div class="lg:col-span-5 p-5 sm:p-7 lg:p-8 flex flex-col justify-between gap-4 bg-white dark:bg-[#0f2137]">
                                <div class="space-y-3">
                                    {{-- Organizer & Status --}}
                                    <div class="flex items-center justify-between gap-2 text-xs">
                                        @if($event->organizer)
                                            <div class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400 font-medium">
                                                <div class="w-5 h-5 rounded-full bg-[#F05423] text-white font-extrabold text-[10px] flex items-center justify-center shrink-0">
                                                    {{ strtoupper(substr($event->organizer->nama, 0, 1)) }}
                                                </div>
                                                <span class="truncate max-w-[180px]">Diselenggarakan oleh <strong class="text-slate-800 dark:text-slate-200">{{ $event->organizer->nama }}</strong></span>
                                            </div>
                                        @endif
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 shrink-0">
                                            Pendaftaran Buka
                                        </span>
                                    </div>

                                    {{-- Title --}}
                                    <h2 class="text-lg sm:text-2xl lg:text-3xl font-black italic uppercase tracking-tight text-slate-900 dark:text-white leading-tight hover:text-[#F05423] transition-colors">
                                        <a href="{{ route('events.show', $event->slug) }}">
                                            {{ $event->nama_event }}
                                        </a>
                                    </h2>

                                    {{-- Date & Location with Modern Icons --}}
                                    <div class="space-y-2 pt-1 text-xs sm:text-sm font-medium text-slate-600 dark:text-slate-300">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-xl bg-orange-50 dark:bg-orange-950/40 text-[#F05423] flex items-center justify-center shrink-0">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                                            </div>
                                            <div>
                                                <span class="text-[10px] uppercase font-bold text-slate-400 block leading-none">Jadwal Acara</span>
                                                <span class="font-bold text-slate-900 dark:text-white">{{ $event->tanggal_event->translatedFormat('l, d F Y') }}</span>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-white/10 text-slate-700 dark:text-slate-200 flex items-center justify-center shrink-0">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                                            </div>
                                            <div class="overflow-hidden">
                                                <span class="text-[10px] uppercase font-bold text-slate-400 block leading-none">Lokasi Venue</span>
                                                <span class="font-semibold text-slate-800 dark:text-slate-200 truncate block">{{ $event->lokasi_venue }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Categories Tags --}}
                                    @if($event->kategori->count() > 0)
                                        <div class="flex flex-wrap items-center gap-1.5 pt-1">
                                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mr-1">Kategori:</span>
                                            @foreach($event->kategori->take(4) as $kat)
                                                <span class="px-2.5 py-0.5 rounded-lg text-[11px] font-bold bg-slate-100 dark:bg-white/8 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-white/10">
                                                    {{ $kat->nama_kategori }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                {{-- Action Footer: Price + Button --}}
                                <div class="pt-4 border-t border-slate-100 dark:border-white/10 flex items-center justify-between gap-3">
                                    <div>
                                        @php $minHarga = $event->kategori->min('harga'); @endphp
                                        <span class="text-[10px] uppercase font-extrabold tracking-wider text-slate-400 block leading-none mb-1">Mulai Dari</span>
                                        <span class="text-xl sm:text-2xl font-black italic text-[#F05423] leading-none">
                                            @if($minHarga) Rp {{ number_format($minHarga, 0, ',', '.') }} @else Gratis @endif
                                        </span>
                                    </div>
                                    <a href="{{ route('events.show', $event->slug) }}"
                                       class="inline-flex items-center gap-2 px-5 sm:px-6 py-2.5 sm:py-3 rounded-xl bg-[#F05423] hover:bg-[#d94416] text-white font-black italic uppercase text-xs sm:text-sm tracking-wider shadow-lg shadow-orange-500/25 hover:shadow-orange-500/40 hover:scale-[1.02] active:scale-100 transition-all shrink-0">
                                        <span>Lihat Detail</span>
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Carousel Navigation Dots --}}
            <div class="flex items-center justify-center gap-2 py-3 bg-slate-50 dark:bg-white/5 border-t border-slate-100 dark:border-white/8">
                <button onclick="prevSlide();resetAutoSlide();" class="p-1.5 rounded-lg text-slate-400 hover:text-[#F05423] transition-colors" aria-label="Sebelumnya">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                </button>
                @foreach($featuredEvents as $index => $event)
                    <button onclick="goToSlide({{ $index }})" aria-label="Slide {{ $index + 1 }}"
                            class="dot-indicator h-2.5 rounded-full transition-all duration-300 {{ $index === 0 ? 'bg-[#F05423] w-7' : 'bg-slate-300 dark:bg-white/30 hover:bg-slate-400 w-2.5' }}"
                            data-indicator="{{ $index }}"></button>
                @endforeach
                <button onclick="nextSlide();resetAutoSlide();" class="p-1.5 rounded-lg text-slate-400 hover:text-[#F05423] transition-colors" aria-label="Berikutnya">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </button>
            </div>
        </div>
    @endif

    {{-- ======================================================
         SECTION HEADER & PILL FILTER
         ====================================================== --}}
    <div class="space-y-5">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <span class="inline-block px-3.5 py-1 text-xs font-black uppercase tracking-wider text-[#F05423] bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/30 rounded-xl mb-2">
                    Katalog Lomba & Marathon
                </span>
                <h2 class="text-3xl font-black italic uppercase tracking-tight text-slate-900 dark:text-white">
                    Jelajahi Event Lari
                </h2>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1 font-medium">
                    Temukan lomba terbaik di Indonesia. Daftar & dapatkan E-Ticket + Racepack eksklusif.
                </p>
            </div>
            <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                <span class="font-semibold">{{ $events->total() }} Event Aktif</span>
            </div>
        </div>

        {{-- Pill Filter Tabs (ala Loket.com) --}}
        <div class="overflow-x-auto pb-1 -mx-1 px-1">
            <div class="flex items-center gap-2 min-w-max" id="category-filters">
                @php
                    $filterPills = ['Semua','5K','10K','Half Marathon','Full Marathon','Trail Run','Fun Run'];
                @endphp
                @foreach($filterPills as $i => $pill)
                    <button type="button"
                            class="pill-filter-btn px-4 py-2 rounded-full text-[12px] font-bold border whitespace-nowrap
                                   {{ $i === 0
                                      ? 'active bg-[#F05423] text-white border-[#F05423]'
                                      : 'bg-white dark:bg-white/5 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-white/15' }}"
                            data-filter="{{ $pill === 'Semua' ? 'all' : strtolower(str_replace([' ','K'], ['-','k'], $pill)) }}"
                            onclick="filterEvents(this, '{{ $pill === 'Semua' ? 'all' : $pill }}')">
                        {{ $pill }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ======================================================
         EVENT CARDS GRID
         ====================================================== --}}
    @if($events->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="events-grid">
            @foreach($events as $event)
                @php
                    $katNames = $event->kategori->pluck('nama_kategori')->join(' ');
                    $minHarga = $event->kategori->min('harga');
                @endphp
                <div class="event-card glass-card rounded-3xl overflow-hidden flex flex-col group"
                     data-categories="{{ strtolower($katNames) }}">

                    {{-- Banner 16:9 --}}
                    <div class="aspect-video relative overflow-hidden bg-slate-900 shrink-0">
                        @if($event->banner_image_url)
                            <img src="{{ $event->banner_image_url }}" alt="{{ $event->nama_event }}"
                                 class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
                        @else
                            <img src="https://images.unsplash.com/photo-1530541930197-ff16ac917b0e?auto=format&fit=crop&w=800&q=80"
                                 alt="{{ $event->nama_event }}"
                                 class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>

                        {{-- Floating badges --}}
                        <div class="absolute top-3 left-3 right-3 flex items-start justify-between z-10">
                            <div class="flex flex-col gap-1.5">
                                <span class="px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-[#F05423] text-white shadow-md flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                    {{ $event->status_event === 'Publikasi' ? 'Buka Pendaftaran' : $event->status_event }}
                                </span>
                                {{-- Category badges --}}
                                @foreach($event->kategori->take(2) as $kat)
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-extrabold bg-black/60 text-orange-300 border border-orange-400/30 backdrop-blur-md">
                                        {{ $kat->nama_kategori }}
                                    </span>
                                @endforeach
                                @if($event->kategori->count() > 2)
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-black/50 text-slate-300">
                                        +{{ $event->kategori->count() - 2 }} lagi
                                    </span>
                                @endif
                            </div>
                            <span class="px-2.5 py-1 rounded-xl text-[10px] font-bold bg-black/60 text-white border border-white/20 backdrop-blur-md shrink-0">
                                {{ $event->pendaftaran_count }} Peserta
                            </span>
                        </div>

                        {{-- Date overlay bottom --}}
                        <div class="absolute bottom-3 left-3 z-10">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-orange-400 block">Tanggal Lomba</span>
                            <span class="text-base font-black text-white drop-shadow">{{ $event->tanggal_event->format('d F Y') }}</span>
                        </div>
                    </div>

                    {{-- Card Body --}}
                    <div class="p-5 flex flex-col flex-1 justify-between gap-4">
                        <div class="space-y-2">
                            <h3 class="text-lg font-black text-slate-900 dark:text-white group-hover:text-[#F05423] transition-colors leading-snug">
                                <a href="{{ route('events.show', $event->slug) }}">{{ $event->nama_event }}</a>
                            </h3>
                            @if($event->organizer)
                                <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-medium">
                                    <svg class="w-3.5 h-3.5 text-[#F05423] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 1 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                                    <span>by <strong class="text-slate-800 dark:text-slate-200">{{ $event->organizer->nama }}</strong></span>
                                </div>
                            @endif
                            <div class="flex items-start gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                                <svg class="w-3.5 h-3.5 text-slate-400 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                                <span class="font-medium">{{ $event->lokasi_venue }}</span>
                            </div>
                        </div>

                        {{-- Card Footer --}}
                        <div class="border-t border-slate-100 dark:border-white/10 pt-4 flex items-center justify-between gap-2">
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wide block">Mulai dari</span>
                                <span class="text-base font-black text-[#F05423]">
                                    @if($minHarga)
                                        Rp {{ number_format($minHarga, 0, ',', '.') }}
                                    @else
                                        —
                                    @endif
                                </span>
                            </div>
                            <a href="{{ route('events.show', $event->slug) }}"
                               class="shrink-0 px-4 py-2 rounded-xl btn-brand-orange text-white text-[11px] font-extrabold uppercase tracking-wider transition-all">
                                Beli Tiket →
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- No results from filter --}}
        <div id="no-filter-results" class="hidden text-center py-12">
            <div class="text-4xl mb-3">🏃</div>
            <h3 class="text-lg font-bold text-slate-700 dark:text-slate-300">Tidak ada event untuk kategori ini</h3>
            <p class="text-sm text-slate-500 mt-1">Coba pilih kategori lain atau lihat semua event.</p>
        </div>

        <div class="mt-8 flex justify-center">
            {{ $events->links() }}
        </div>
    @else
        <div class="glass-card rounded-3xl p-14 text-center shadow-sm">
            <div class="text-5xl mb-4">🏃</div>
            <h3 class="text-xl font-bold text-slate-800 dark:text-white">Belum Ada Event Lari</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Event lari terbaru akan segera hadir.</p>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    // ---- CAROUSEL ----
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slide-item');
    const indicators = document.querySelectorAll('.dot-indicator');
    const totalSlides = slides.length;
    let autoSlideInterval;

    function showSlide(index) {
        if (!totalSlides) return;
        slides.forEach((s,i) => {
            if (i === index) {
                s.classList.remove('hidden');
                s.classList.add('block');
            } else {
                s.classList.remove('block');
                s.classList.add('hidden');
            }
        });
        indicators.forEach((d,i) => {
            if (i === index) {
                d.classList.remove('bg-slate-300','dark:bg-white/30','w-2.5');
                d.classList.add('bg-[#F05423]','w-7');
            } else {
                d.classList.remove('bg-[#F05423]','w-7');
                d.classList.add('bg-slate-300','dark:bg-white/30','w-2.5');
            }
        });
        currentSlide = index;
    }
    function nextSlide() { showSlide((currentSlide+1) % totalSlides); }
    function prevSlide() { showSlide((currentSlide-1+totalSlides) % totalSlides); }
    function goToSlide(i) { showSlide(i); resetAutoSlide(); }
    function startAutoSlide() { autoSlideInterval = setInterval(nextSlide, 5000); }
    function resetAutoSlide() { clearInterval(autoSlideInterval); startAutoSlide(); }

    if (totalSlides > 0) {
        startAutoSlide();
        const sliderEl = document.getElementById('hero-slider');
        if (sliderEl) {
            sliderEl.addEventListener('mouseenter', () => clearInterval(autoSlideInterval));
            sliderEl.addEventListener('mouseleave', startAutoSlide);
            let tx = 0;
            sliderEl.addEventListener('touchstart', e => { tx = e.changedTouches[0].screenX; }, { passive: true });
            sliderEl.addEventListener('touchend', e => {
                const diff = e.changedTouches[0].screenX - tx;
                if (Math.abs(diff) > 40) { diff < 0 ? nextSlide() : prevSlide(); resetAutoSlide(); }
            }, { passive: true });
        }
    }

    // ---- PILL FILTER ----
    function filterEvents(btn, category) {
        // Update active pill
        document.querySelectorAll('.pill-filter-btn').forEach(b => {
            b.classList.remove('active','bg-[#F05423]','text-white','border-[#F05423]');
            b.classList.add('bg-white','dark:bg-white/5','text-slate-600','dark:text-slate-300','border-slate-200','dark:border-white/15');
        });
        btn.classList.add('active','bg-[#F05423]','text-white','border-[#F05423]');
        btn.classList.remove('bg-white','dark:bg-white/5','text-slate-600','dark:text-slate-300','border-slate-200','dark:border-white/15');

        // Filter cards
        const cards = document.querySelectorAll('.event-card');
        let visible = 0;
        cards.forEach(card => {
            const cats = card.dataset.categories || '';
            const match = category === 'Semua' || cats.includes(category.toLowerCase());
            card.classList.toggle('hidden-by-filter', !match);
            if (match) visible++;
        });
        const noRes = document.getElementById('no-filter-results');
        if (noRes) noRes.classList.toggle('hidden', visible > 0);
    }
</script>
@endpush