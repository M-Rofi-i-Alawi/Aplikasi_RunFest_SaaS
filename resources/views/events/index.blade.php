@extends('layouts.app')

@section('title', 'Katalog Event Lari — RunFest SaaS')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-10">

    {{-- ================================================================
         HERO CAROUSEL ("Gambar Berjalan" — Adaptif Terang & Gelap)
         ================================================================ --}}
    @if($events->count() > 0)
        @php $featuredEvents = $events->take(3); @endphp
        <div class="relative group rounded-3xl overflow-hidden bg-slate-900 border border-slate-200 dark:border-white/15 shadow-xl dark:shadow-2xl">
            
            {{-- Carousel Slides Wrapper --}}
            <div id="hero-slider" class="relative min-h-[380px] sm:min-h-[440px] md:min-h-[480px]">
                @foreach($featuredEvents as $index => $event)
                    <div class="slide-item absolute inset-0 transition-all duration-700 ease-in-out opacity-0 pointer-events-none {{ $index === 0 ? 'opacity-100 pointer-events-auto z-10' : 'z-0' }}"
                         data-slide="{{ $index }}">
                        
                        {{-- Background Banner Image with Gradient Overlay --}}
                        <div class="absolute inset-0 bg-slate-900">
                            <img src="{{ $event->banner_url ?: 'https://images.unsplash.com/photo-1530541930197-ff16ac917b0e?auto=format&fit=crop&w=1600&q=80' }}"
                                 alt="{{ $event->nama_event }}"
                                 class="w-full h-full object-cover object-center transform scale-105 transition-transform duration-1000">
                            {{-- Gradient Overlay for Text Readability --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent"></div>
                            <div class="absolute inset-0 bg-gradient-to-r from-slate-950/90 via-slate-950/40 to-transparent"></div>
                        </div>

                        {{-- Slide Content Overlay --}}
                        <div class="absolute inset-0 p-6 sm:p-10 md:p-14 flex flex-col justify-end max-w-3xl z-20 space-y-3.5">
                            
                            {{-- Top Badges --}}
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-3 py-1 rounded-xl text-xs font-black uppercase tracking-wider bg-[#ff5500] text-white shadow-lg shadow-orange-600/30">
                                    ★ FEATURED EVENT — {{ $event->status_event }}
                                </span>
                                <span class="px-3 py-1 rounded-xl text-xs font-bold bg-white/20 text-white backdrop-blur-md border border-white/20">
                                    {{ $event->pendaftaran_count }} Peserta Terdaftar
                                </span>
                            </div>

                            {{-- Event Title --}}
                            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-white tracking-tight leading-tight drop-shadow-md">
                                {{ $event->nama_event }}
                            </h1>

                            {{-- Organizer & Location Details --}}
                            <div class="flex flex-wrap items-center gap-3 text-sm text-slate-200 font-medium">
                                @if($event->organizer)
                                    <div class="flex items-center space-x-2 bg-white/15 px-3 py-1.5 rounded-xl backdrop-blur-md border border-white/20">
                                        <div class="w-5 h-5 rounded-full bg-[#ff5500] text-white font-extrabold text-[10px] flex items-center justify-center">
                                            {{ strtoupper(substr($event->organizer->nama, 0, 1)) }}
                                        </div>
                                        <span>Organizer: <strong class="text-white">{{ $event->organizer->nama }}</strong></span>
                                    </div>
                                @endif

                                <div class="flex items-center space-x-1.5 bg-white/15 px-3 py-1.5 rounded-xl backdrop-blur-md border border-white/20">
                                    <svg class="w-4 h-4 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                                    </svg>
                                    <span>{{ $event->lokasi_venue }}</span>
                                </div>

                                <div class="flex items-center space-x-1.5 bg-white/15 px-3 py-1.5 rounded-xl backdrop-blur-md border border-white/20">
                                    <svg class="w-4 h-4 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                                    </svg>
                                    <span>{{ $event->tanggal_event->format('d F Y') }}</span>
                                </div>
                            </div>

                            {{-- Category Pills --}}
                            <div class="flex flex-wrap gap-2 pt-1">
                                @foreach($event->kategori as $kat)
                                    <span class="px-3 py-1 rounded-xl text-xs font-extrabold bg-orange-500/30 text-orange-200 border border-orange-400/40 backdrop-blur-md">
                                        {{ $kat->nama_kategori }} — Rp {{ number_format($kat->harga, 0, ',', '.') }}
                                    </span>
                                @endforeach
                            </div>

                            {{-- CTA Button --}}
                            <div class="pt-2 flex items-center space-x-4">
                                <a href="{{ route('events.show', $event->slug) }}"
                                   class="inline-flex items-center space-x-2 px-6 py-3 rounded-2xl btn-brand-orange text-white font-extrabold text-sm uppercase tracking-wider hover:scale-105 transition-all">
                                    <span>Lihat Detail & Daftar</span>
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                                    </svg>
                                </a>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Slider Controls (Arrows) --}}
            <button onclick="prevSlide()" class="absolute left-4 top-1/2 -translate-y-1/2 z-30 w-11 h-11 rounded-2xl bg-white/20 hover:bg-[#ff5500] backdrop-blur-md border border-white/30 text-white flex items-center justify-center transition-all hover:scale-110">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </button>
            <button onclick="nextSlide()" class="absolute right-4 top-1/2 -translate-y-1/2 z-30 w-11 h-11 rounded-2xl bg-white/20 hover:bg-[#ff5500] backdrop-blur-md border border-white/30 text-white flex items-center justify-center transition-all hover:scale-110">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            </button>

            {{-- Slide Progress Indicators (Dots) --}}
            <div class="absolute bottom-5 right-6 z-30 flex items-center space-x-2 bg-black/40 px-3.5 py-1.5 rounded-2xl backdrop-blur-md border border-white/20">
                @foreach($featuredEvents as $index => $event)
                    <button onclick="goToSlide({{ $index }})"
                            class="dot-indicator w-3 h-3 rounded-full transition-all duration-300 {{ $index === 0 ? 'bg-[#ff5500] w-8' : 'bg-white/50 hover:bg-white' }}"
                            data-indicator="{{ $index }}"></button>
                @endforeach
            </div>

        </div>
    @endif

    {{-- ================================================================
         SECTION HEADER & CATALOG TITLE
         ================================================================ --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-200 dark:border-white/10 pb-5">
        <div>
            <span class="inline-block px-3.5 py-1 text-xs font-black uppercase tracking-wider text-[#ff5500] bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/30 rounded-xl mb-2">
                Katalog Lomba & Marathon
            </span>
            <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Jelajahi Event Lari Terbaru
            </h2>
            <p class="text-slate-600 dark:text-slate-400 text-sm mt-1">
                Temukan lomba lari terbaik di seluruh Indonesia. Daftarkan diri Anda dan dapatkan E-Ticket & Racepack eksklusif.
            </p>
        </div>
    </div>

    {{-- ================================================================
         EVENT CARDS GRID (Adaptif Dual Mode Terang & Gelap)
         ================================================================ --}}
    @if($events->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($events as $event)
                <div class="glass-card rounded-3xl overflow-hidden flex flex-col justify-between group">
                    
                    <div>
                        {{-- Card Image Banner Header --}}
                        <div class="h-48 relative overflow-hidden bg-slate-900">
                            <img src="{{ $event->banner_url ?: 'https://images.unsplash.com/photo-1530541930197-ff16ac917b0e?auto=format&fit=crop&w=800&q=80' }}"
                                 alt="{{ $event->nama_event }}"
                                 class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
                            
                            {{-- Floating Badges --}}
                            <div class="absolute top-4 left-4 right-4 flex items-center justify-between z-10">
                                <span class="px-2.5 py-1 rounded-xl text-xs font-extrabold uppercase tracking-wider bg-[#ff5500] text-white shadow-md">
                                    {{ $event->status_event }}
                                </span>
                                <span class="px-2.5 py-1 rounded-xl text-xs font-bold bg-black/60 text-white border border-white/20 backdrop-blur-md">
                                    {{ $event->pendaftaran_count }} Peserta
                                </span>
                            </div>

                            <div class="absolute bottom-3 left-4 right-4 z-10">
                                <span class="text-[11px] font-extrabold uppercase tracking-wider text-orange-400 block">Tanggal Lomba</span>
                                <span class="text-lg font-black text-white drop-shadow">{{ $event->tanggal_event->format('d F Y') }}</span>
                            </div>
                        </div>

                        {{-- Card Body Content --}}
                        <div class="p-6 space-y-3">
                            <h3 class="text-xl font-extrabold text-slate-900 dark:text-white group-hover:text-[#ff5500] dark:group-hover:text-[#ff5500] transition-colors leading-snug">
                                <a href="{{ route('events.show', $event->slug) }}">{{ $event->nama_event }}</a>
                            </h3>

                            @if($event->organizer)
                                <div class="flex items-center space-x-1.5 text-xs text-slate-500 dark:text-slate-300 font-medium">
                                    <svg class="w-3.5 h-3.5 text-[#ff5500]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 1 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                                    </svg>
                                    <span>Organizer: <strong class="text-slate-800 dark:text-white font-semibold">{{ $event->organizer->nama }}</strong></span>
                                </div>
                            @endif

                            <div class="flex items-start space-x-2 text-slate-600 dark:text-slate-300 text-sm">
                                <svg class="w-4 h-4 text-slate-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                                </svg>
                                <span>{{ $event->lokasi_venue }}</span>
                            </div>

                            {{-- Categories Badges --}}
                            <div class="flex flex-wrap gap-1.5 pt-1">
                                @foreach($event->kategori as $kat)
                                    <span class="px-2.5 py-1 rounded-xl text-xs font-bold bg-slate-100 dark:bg-white/10 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-white/15">
                                        {{ $kat->nama_kategori }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Card Footer --}}
                    <div class="p-6 pt-0">
                        <div class="pt-4 border-t border-slate-100 dark:border-white/10 flex items-center justify-between">
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">RPC: {{ $event->tanggal_rpc_mulai->format('d M') }} - {{ $event->tanggal_rpc_selesai->format('d M Y') }}</span>
                            <a href="{{ route('events.show', $event->slug) }}" 
                               class="px-4 py-2 rounded-xl btn-brand-orange text-white text-xs font-extrabold uppercase tracking-wider transition-all">
                                Lihat Detail
                            </a>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

        <div class="mt-8 flex justify-center">
            {{ $events->links() }}
        </div>
    @else
        <div class="glass-card rounded-3xl p-12 text-center shadow-sm">
            <svg class="w-12 h-12 text-slate-400 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
            </svg>
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Belum Ada Event Lari</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Event lari terbaru akan segera hadir.</p>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slide-item');
    const indicators = document.querySelectorAll('.dot-indicator');
    const totalSlides = slides.length;
    let autoSlideInterval;

    function showSlide(index) {
        if (totalSlides === 0) return;
        
        slides.forEach((slide, i) => {
            if (i === index) {
                slide.classList.remove('opacity-0', 'pointer-events-none', 'z-0');
                slide.classList.add('opacity-100', 'pointer-events-auto', 'z-10');
            } else {
                slide.classList.remove('opacity-100', 'pointer-events-auto', 'z-10');
                slide.classList.add('opacity-0', 'pointer-events-none', 'z-0');
            }
        });

        indicators.forEach((dot, i) => {
            if (i === index) {
                dot.classList.remove('bg-white/50', 'w-3');
                dot.classList.add('bg-[#ff5500]', 'w-8');
            } else {
                dot.classList.remove('bg-[#ff5500]', 'w-8');
                dot.classList.add('bg-white/50', 'w-3');
            }
        });

        currentSlide = index;
    }

    function nextSlide() {
        let next = (currentSlide + 1) % totalSlides;
        showSlide(next);
    }

    function prevSlide() {
        let prev = (currentSlide - 1 + totalSlides) % totalSlides;
        showSlide(prev);
    }

    function goToSlide(index) {
        showSlide(index);
        resetAutoSlide();
    }

    function startAutoSlide() {
        autoSlideInterval = setInterval(nextSlide, 5000);
    }

    function resetAutoSlide() {
        clearInterval(autoSlideInterval);
        startAutoSlide();
    }

    if (totalSlides > 0) {
        startAutoSlide();
        const sliderEl = document.getElementById('hero-slider');
        if (sliderEl) {
            sliderEl.addEventListener('mouseenter', () => clearInterval(autoSlideInterval));
            sliderEl.addEventListener('mouseleave', startAutoSlide);
        }
    }
</script>
@endpush
