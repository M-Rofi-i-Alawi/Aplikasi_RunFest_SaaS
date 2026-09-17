<!DOCTYPE html>
<html lang="id" class="scroll-smooth overflow-x-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="RunFest SaaS — Platform Pendaftaran Event Lari & Ticketing Race Pack terpercaya di Indonesia.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'RunFest SaaS') — Sports Event & Ticketing Platform</title>
    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-icon.png') }}">
    {{-- Anti-flash theme script --}}
    <script>
        (function() {
            const saved = localStorage.getItem('color-theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (saved === 'dark' || (!saved && prefersDark)) {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
            }
        })();
    </script>
    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,800;1,900&display=swap" rel="stylesheet">
    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            orange:        '#F05423',
                            'orange-dark': '#D4461A',
                            'orange-light':'#FF6A3D',
                            navy:          '#0F2137',
                            'navy-light':  '#1A3350',
                        }
                    },
                    fontFamily: { 'sans': ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        html, body { max-width:100vw !important; overflow-x:hidden !important; }
        body { font-family:'Plus Jakarta Sans',sans-serif; transition:background-color .3s ease,color .3s ease; }
        .heading-sport { font-weight:900; font-style:italic; text-transform:uppercase; letter-spacing:-.03em; }
        .glass-card {
            background:#fff; border:1px solid #e2e8f0;
            box-shadow:0 1px 4px 0 rgba(0,0,0,.06);
            transition:all .3s cubic-bezier(.4,0,.2,1); border-radius:1rem;
        }
        .dark .glass-card {
            background:rgba(21,43,68,.7); backdrop-filter:blur(16px);
            -webkit-backdrop-filter:blur(16px);
            border:1px solid rgba(255,255,255,.09);
            box-shadow:0 20px 40px -15px rgba(0,0,0,.5);
        }
        .glass-card:hover { transform:translateY(-2px); }
        .dark .glass-card:hover { border-color:rgba(240,84,35,.4); box-shadow:0 20px 45px -10px rgba(240,84,35,.22); }
        .btn-brand-orange {
            background:linear-gradient(135deg,#F05423 0%,#FF6A3D 100%);
            box-shadow:0 6px 18px -4px rgba(240,84,35,.38); transition:all .22s ease;
        }
        .btn-brand-orange:hover {
            background:linear-gradient(135deg,#D4461A 0%,#F05423 100%);
            box-shadow:0 10px 24px -4px rgba(240,84,35,.48); transform:translateY(-1px);
        }
        .rf-navbar {
            background:rgba(255,255,255,.96);
            backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px);
            border-bottom:1px solid rgba(226,232,240,.8);
        }
        .dark .rf-navbar { background:rgba(15,33,55,.95); border-bottom:1px solid rgba(255,255,255,.08); }
        .rf-search-input { background:#f1f5f9; border:1.5px solid #e2e8f0; transition:all .2s ease; }
        .rf-search-input:focus { background:#fff; border-color:#F05423; box-shadow:0 0 0 3px rgba(240,84,35,.12); outline:none; }
        .dark .rf-search-input { background:rgba(255,255,255,.07); border-color:rgba(255,255,255,.1); color:#f1f5f9; }
        .dark .rf-search-input:focus { background:rgba(255,255,255,.1); border-color:#F05423; }
        .badge-runner { background:#0ea5e9; }
        .badge-organizer { background:#7c3aed; }
        .badge-marshal { background:#16a34a; }
        .badge-superadmin { background:#F05423; }
        .nav-active { color:#F05423; background:rgba(240,84,35,.08); border:1px solid rgba(240,84,35,.25); }
        .rf-footer { background:#0F2137; }
        .dark .rf-footer { background:#0a1a2e; }
        .theme-pill {
            width:44px; height:24px; background:#e2e8f0;
            border-radius:999px; position:relative; cursor:pointer; transition:background .25s;
            border:none; padding:0;
        }
        .dark .theme-pill { background:#F05423; }
        .theme-pill::after {
            content:''; position:absolute; left:3px; top:3px;
            width:18px; height:18px; background:#fff; border-radius:50%;
            transition:transform .25s; box-shadow:0 1px 4px rgba(0,0,0,.18);
        }
        .dark .theme-pill::after { transform:translateX(20px); }
        .ambient-blob { pointer-events:none; position:fixed; border-radius:9999px; filter:blur(120px); z-index:-1; }
        @keyframes fadeSlideDown { from{opacity:0;transform:translateY(-8px)} to{opacity:1;transform:translateY(0)} }
        #profile-dropdown:not(.hidden) { animation:fadeSlideDown .18s ease forwards; }
        #search-results:not(.hidden) { animation:fadeSlideDown .15s ease forwards; }
        .flash-banner { animation:slideInTop .3s ease; }
        /* === GLOBAL DARK MODE CONTRAST ENGINE === */
        html.dark {
            color-scheme: dark;
        }

        /* Paksa teks heading dan konten utama agar terang & kontras di mode gelap */
        html.dark h1, html.dark h2, html.dark h3, html.dark h4, html.dark h5, html.dark h6 {
            color: #f8fafc !important; /* slate-50 */
        }

        /* Tangani hardcoded tailwind text classes */
        html.dark .text-slate-900, 
        html.dark .text-gray-900, 
        html.dark .text-black,
        html.dark .text-slate-800,
        html.dark .text-gray-800 {
            color: #f1f5f9 !important; /* slate-100 */
        }

        html.dark .text-slate-700, 
        html.dark .text-gray-700 {
            color: #cbd5e1 !important; /* slate-300 */
        }

        html.dark .text-slate-600, 
        html.dark .text-gray-600 {
            color: #94a3b8 !important; /* slate-400 */
        }

        html.dark .text-slate-500, 
        html.dark .text-gray-500 {
            color: #cbd5e1 !important;
        }

        /* Tangani surface kartu (card) putih agar berubah menjadi Tactical Deep Navy */
        html.dark .bg-white:not([data-preserve-white]):not(.preserve-white):not(.qr-box):not([data-qr-box]) {
            background-color: #0f2137 !important;
            border-color: #1e3a5a !important;
        }

        html.dark .bg-slate-50:not([data-preserve-bg]), 
        html.dark .bg-gray-50:not([data-preserve-bg]),
        html.dark .bg-slate-100:not([data-preserve-bg]),
        html.dark .bg-gray-100:not([data-preserve-bg]) {
            background-color: #0b1a2b !important;
            border-color: #1e3a5a !important;
        }

        /* Tangani garis border pemisah */
        html.dark .border-slate-200, 
        html.dark .border-gray-200,
        html.dark .border-slate-100,
        html.dark .border-gray-100,
        html.dark .border-slate-300,
        html.dark .border-gray-300 {
            border-color: #1e3a5a !important;
        }

        /* Form Controls: Input, Select, Textarea */
        html.dark input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]):not([type="file"]),
        html.dark select,
        html.dark textarea {
            background-color: #081624 !important;
            color: #f8fafc !important;
            border-color: #1e3a5a !important;
        }

        html.dark select option {
            background-color: #081624 !important;
            color: #f8fafc !important;
        }

        html.dark input::placeholder, 
        html.dark textarea::placeholder {
            color: #64748b !important;
        }

        /* Pengecualian elemen yang harus tetap mempertahankan warna brand & status */
        html.dark .text-[#F05423],
        html.dark .text-orange-600,
        html.dark .text-orange-500,
        html.dark .text-orange-400 {
            color: #F05423 !important;
        }

        html.dark .text-white {
            color: #ffffff !important;
        }

        html.dark .text-emerald-700, html.dark .text-emerald-600, html.dark .text-emerald-500, html.dark .text-emerald-400 {
            color: #34d399 !important;
        }
        html.dark .text-rose-700, html.dark .text-rose-600, html.dark .text-rose-500, html.dark .text-rose-400 {
            color: #fb7185 !important;
        }
        html.dark .text-amber-700, html.dark .text-amber-600, html.dark .text-amber-500, html.dark .text-amber-400 {
            color: #fbbf24 !important;
        }
        html.dark .text-blue-700, html.dark .text-blue-600, html.dark .text-blue-500, html.dark .text-blue-400 {
            color: #60a5fa !important;
        }

        /* QR Code Container harus selalu putih bersih untuk scan optik */
        [data-preserve-white], .preserve-white, .qr-box, [data-qr-box] {
            background-color: #ffffff !important;
            color: #000000 !important;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-900 dark:bg-[#07131f] dark:text-slate-100 min-h-screen flex flex-col relative transition-colors duration-200 antialiased selection:bg-[#F05423] selection:text-white overflow-x-hidden w-full max-w-full">

    {{-- Ambient Blobs --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10" aria-hidden="true">
        <div class="hidden dark:block ambient-blob" style="top:-80px;left:15%;width:520px;height:520px;background:rgba(240,84,35,.1)"></div>
        <div class="hidden dark:block ambient-blob" style="top:35%;right:-80px;width:440px;height:440px;background:rgba(30,58,138,.18)"></div>
    </div>

    {{-- ===== NAVBAR ===== --}}
    <nav class="rf-navbar sticky top-0 z-50 shadow-sm transition-colors duration-300 w-full" role="navigation" aria-label="Navigasi Utama">
        <div class="max-w-7xl mx-auto px-4 sm:px-5 lg:px-8">

            {{-- ROW 1: Logo | Search | Right Actions --}}
            <div class="flex items-center gap-3 h-[60px] sm:h-[68px]">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0 group" aria-label="RunFest SaaS">
                    <img src="{{ asset('images/logo-icon.png') }}" alt="RunFest" class="h-9 w-auto object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-sm">
                    <div class="hidden sm:flex flex-col leading-none">
                        <span class="text-[22px] font-black tracking-tight text-[#0F2137] dark:text-white">Run<span class="text-[#F05423]">Fest</span></span>
                        <span class="text-[9px] font-extrabold tracking-[0.22em] text-slate-400 uppercase">S A A S</span>
                    </div>
                </a>

                {{-- Desktop Search Bar --}}
                <div class="hidden md:flex flex-1 max-w-sm lg:max-w-md relative" id="navbar-search-wrapper">
                    <div class="relative w-full">
                        <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                        </span>
                        <input type="text" id="navbar-search" placeholder="Cari event lari..." autocomplete="off"
                               class="rf-search-input w-full pl-9 pr-4 py-2 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-100 placeholder-slate-400">
                        <div id="search-results" class="hidden absolute top-full left-0 right-0 mt-1.5 bg-white dark:bg-[#1A3350] rounded-2xl border border-slate-200 dark:border-white/10 shadow-xl overflow-hidden z-50 max-h-72 overflow-y-auto">
                            <div id="search-results-inner" class="py-1.5 px-1"></div>
                        </div>
                    </div>
                </div>

                {{-- Desktop Nav Links --}}
                <div class="hidden lg:flex items-center gap-0.5 flex-1 justify-center">
                    <a href="{{ route('events.index') }}"
                       class="px-3.5 py-2 rounded-xl text-[13px] font-bold transition-all whitespace-nowrap {{ request()->routeIs('events.*') || request()->routeIs('home') ? 'nav-active' : 'text-slate-600 dark:text-slate-300 hover:text-[#F05423] hover:bg-orange-50 dark:hover:bg-white/8' }}">
                        🏃 Event Lari
                    </a>
                    @auth
                        @if(auth()->user()->isRunner() || auth()->user()->isSuperAdmin())
                            <a href="{{ route('runner.dashboard') }}"
                               class="px-3.5 py-2 rounded-xl text-[13px] font-bold transition-all whitespace-nowrap {{ request()->routeIs('runner.*') ? 'nav-active' : 'text-slate-600 dark:text-slate-300 hover:text-[#F05423] hover:bg-orange-50 dark:hover:bg-white/8' }}">
                                🎫 Dashboard Tiket
                            </a>
                        @endif
                        @if(auth()->user()->isOrganizer() || auth()->user()->isSuperAdmin())
                            <a href="{{ route('organizer.events') }}"
                               class="px-3.5 py-2 rounded-xl text-[13px] font-bold transition-all whitespace-nowrap {{ request()->routeIs('organizer.*') ? 'nav-active' : 'text-slate-600 dark:text-slate-300 hover:text-[#F05423] hover:bg-orange-50 dark:hover:bg-white/8' }}">
                                🏢 Kelola Event
                            </a>
                        @endif
                        @if(auth()->user()->isMarshal() || auth()->user()->isSuperAdmin())
                            <a href="{{ route('marshal.scanner') }}"
                               class="px-3.5 py-2 rounded-xl text-[13px] font-bold transition-all whitespace-nowrap {{ request()->routeIs('marshal.*') ? 'nav-active' : 'text-slate-600 dark:text-slate-300 hover:text-[#F05423] hover:bg-orange-50 dark:hover:bg-white/8' }}">
                                📱 Scanner
                            </a>
                        @endif
                    @endauth
                </div>

                {{-- Right Actions --}}
                <div class="flex items-center gap-2 ml-auto">

                    {{-- Theme Pill Toggle (Hidden on mobile to keep top header clean, accessible in mobile menu) --}}
                    <div class="hidden sm:flex items-center gap-1.5 shrink-0" title="Beralih Mode Terang / Gelap">
                        <svg class="w-3.5 h-3.5 text-amber-500 hidden dark:block" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 100 2h1z"/></svg>
                        <svg class="w-3.5 h-3.5 text-slate-500 dark:hidden" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/></svg>
                        <button id="theme-toggle" type="button" aria-label="Toggle Dark/Light Mode" class="theme-pill shrink-0"></button>
                    </div>

                    @guest
                        <a href="{{ route('login') }}" class="hidden sm:inline-flex px-3 py-2 text-[13px] font-bold text-slate-700 dark:text-slate-300 hover:text-[#F05423] transition-colors">Masuk</a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 text-[13px] font-extrabold text-white btn-brand-orange rounded-xl">Daftar</a>
                    @else
                        {{-- Profile Dropdown --}}
                        <div class="relative" id="profile-wrapper">
                            <button id="profile-btn" type="button" aria-expanded="false" aria-controls="profile-dropdown"
                                    class="flex items-center gap-2 bg-slate-100 dark:bg-white/8 hover:bg-slate-200 dark:hover:bg-white/12 px-2.5 py-1.5 rounded-2xl border border-slate-200/80 dark:border-white/10 transition-all">
                                <div class="w-7 h-7 rounded-full btn-brand-orange text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-sm">
                                    {{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}
                                </div>
                                <div class="hidden sm:flex flex-col text-left leading-tight">
                                    <span class="text-xs font-bold text-slate-900 dark:text-white max-w-[100px] truncate">{{ auth()->user()->nama }}</span>
                                    @php
                                        $rc = ['Runner'=>'badge-runner','Organizer'=>'badge-organizer','Marshal'=>'badge-marshal','SuperAdmin'=>'badge-superadmin'][auth()->user()->role] ?? 'badge-superadmin';
                                    @endphp
                                    <span class="text-[9px] font-extrabold text-white px-1.5 py-0.5 rounded-md uppercase tracking-wide {{ $rc }}">{{ auth()->user()->role }}</span>
                                </div>
                                <svg class="w-3.5 h-3.5 text-slate-400 hidden sm:block shrink-0 transition-transform duration-200" id="profile-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <div id="profile-dropdown" class="hidden absolute right-0 top-full mt-2 w-52 bg-white dark:bg-[#1A3350] rounded-2xl border border-slate-200 dark:border-white/10 shadow-xl overflow-hidden z-50">
                                <div class="px-4 py-3 border-b border-slate-100 dark:border-white/8">
                                    <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ auth()->user()->nama }}</p>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <div class="py-1.5">
                                    <a href="{{ route('account.settings') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-white/8 hover:text-[#F05423] transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                        Pengaturan Akun
                                    </a>
                                    @if(auth()->user()->isRunner() || auth()->user()->isSuperAdmin())
                                        <a href="{{ route('runner.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-white/8 hover:text-[#F05423] transition-colors">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                            Tiket Saya
                                        </a>
                                    @endif
                                </div>
                                <div class="border-t border-slate-100 dark:border-white/8 py-1.5">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-2.5 w-full px-4 py-2.5 text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                                            Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endguest

                    {{-- Hamburger --}}
                    <button type="button" id="mobile-menu-toggle" aria-label="Buka menu"
                            class="lg:hidden p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/10 transition-all">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                    </button>
                </div>
            </div>

            {{-- Mobile Search --}}
            <div class="md:hidden pb-3">
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    </span>
                    <input type="text" id="mobile-search" placeholder="Cari event lari..." autocomplete="off"
                           class="rf-search-input w-full pl-9 pr-4 py-2.5 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-100 placeholder-slate-400">
                </div>
            </div>
        </div>

        {{-- Mobile Menu Dropdown --}}
        <div id="mobile-menu" class="hidden lg:hidden border-t border-slate-200/80 dark:border-white/8 bg-white dark:bg-[#0F2137]">
            <div class="max-w-7xl mx-auto px-4 py-3 space-y-1">
                <a href="{{ route('events.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-bold {{ request()->routeIs('events.*') || request()->routeIs('home') ? 'text-[#F05423] bg-orange-50 dark:bg-orange-500/10' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-white/8' }}">🏃 Event Lari</a>
                @auth
                    @if(auth()->user()->isRunner() || auth()->user()->isSuperAdmin())
                        <a href="{{ route('runner.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-bold {{ request()->routeIs('runner.*') ? 'text-[#F05423] bg-orange-50 dark:bg-orange-500/10' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-white/8' }}">🎫 Dashboard Tiket</a>
                    @endif
                    @if(auth()->user()->isOrganizer() || auth()->user()->isSuperAdmin())
                        <a href="{{ route('organizer.events') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-bold {{ request()->routeIs('organizer.*') ? 'text-[#F05423] bg-orange-50 dark:bg-orange-500/10' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-white/8' }}">🏢 Kelola Event</a>
                    @endif
                    @if(auth()->user()->isMarshal() || auth()->user()->isSuperAdmin())
                        <a href="{{ route('marshal.scanner') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-bold {{ request()->routeIs('marshal.*') ? 'text-[#F05423] bg-orange-50 dark:bg-orange-500/10' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-white/8' }}">📱 Scanner Racepack</a>
                    @endif
                    <div class="border-t border-slate-100 dark:border-white/8 pt-2 mt-2 space-y-1">
                        <a href="{{ route('account.settings') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-white/8">⚙️ Pengaturan Akun</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-2.5 w-full px-3 py-2.5 rounded-xl text-sm font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors">🚪 Keluar</button>
                        </form>
                    </div>
                @else
                    <div class="flex flex-col gap-2 pt-1">
                        <a href="{{ route('login') }}" class="block px-3 py-2.5 rounded-xl text-sm font-bold text-center text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-white/10 hover:bg-slate-50 dark:hover:bg-white/8">Masuk</a>
                        <a href="{{ route('register') }}" class="block px-3 py-2.5 rounded-xl text-sm font-extrabold text-center text-white btn-brand-orange">Daftar Sekarang</a>
                    </div>
                @endauth

                {{-- Mobile Theme Switcher --}}
                <div class="border-t border-slate-100 dark:border-white/8 pt-2.5 mt-2 flex items-center justify-between px-3 py-2">
                    <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Mode Tema</span>
                    <button type="button" class="theme-toggle-btn px-3 py-1.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-100 dark:bg-white/10 text-xs font-bold text-slate-700 dark:text-slate-200 flex items-center gap-1.5 hover:bg-slate-200 dark:hover:bg-white/15 transition-all">
                        <span class="dark:hidden flex items-center gap-1">🌙 Gelap</span>
                        <span class="hidden dark:flex items-center gap-1">☀️ Terang</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success') || session('error') || session('warning') || session('info'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 space-y-2" id="flash-message">
            @if(session('success'))
                <div class="flash-banner bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-200 rounded-2xl p-4 flex items-center justify-between text-sm font-semibold shadow-sm">
                    <div class="flex items-center gap-2"><svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg><span>{{ session('success') }}</span></div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-600 font-black ml-4 text-lg leading-none">✕</button>
                </div>
            @endif
            @if(session('error'))
                <div class="flash-banner bg-rose-50 dark:bg-rose-500/15 border border-rose-200 dark:border-rose-500/30 text-rose-800 dark:text-rose-200 rounded-2xl p-4 flex items-center justify-between text-sm font-semibold shadow-sm">
                    <div class="flex items-center gap-2"><svg class="w-5 h-5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg><span>{{ session('error') }}</span></div>
                    <button onclick="this.parentElement.remove()" class="text-rose-600 font-black ml-4 text-lg leading-none">✕</button>
                </div>
            @endif
            @if(session('warning'))
                <div class="flash-banner bg-amber-50 dark:bg-amber-500/15 border border-amber-200 dark:border-amber-500/30 text-amber-800 dark:text-amber-200 rounded-2xl p-4 flex items-center justify-between text-sm font-semibold shadow-sm">
                    <div class="flex items-center gap-2"><svg class="w-5 h-5 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg><span>{{ session('warning') }}</span></div>
                    <button onclick="this.parentElement.remove()" class="text-amber-600 font-black ml-4 text-lg leading-none">✕</button>
                </div>
            @endif
            @if(session('info'))
                <div class="flash-banner bg-blue-50 dark:bg-blue-500/15 border border-blue-200 dark:border-blue-500/30 text-blue-800 dark:text-blue-200 rounded-2xl p-4 flex items-center justify-between text-sm font-semibold shadow-sm">
                    <div class="flex items-center gap-2"><svg class="w-5 h-5 text-blue-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg><span>{{ session('info') }}</span></div>
                    <button onclick="this.parentElement.remove()" class="text-blue-600 font-black ml-4 text-lg leading-none">✕</button>
                </div>
            @endif
        </div>
    @endif

    {{-- Main Content --}}
    <main class="flex-1 w-full max-w-full overflow-x-hidden">
        @yield('content')
    </main>

    {{-- ===== FOOTER ===== --}}
    <footer class="rf-footer mt-20 w-full">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 pb-10 border-b border-white/10">
                {{-- Brand --}}
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logo-icon.png') }}" alt="RunFest Logo" class="h-10 w-auto object-contain">
                        <div class="flex flex-col leading-tight">
                            <span class="text-2xl font-black tracking-tight text-white">Run<span class="text-[#F05423]">Fest</span></span>
                            <span class="text-[9px] font-extrabold tracking-[0.22em] text-slate-400 uppercase">S A A S</span>
                        </div>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed max-w-xs">Platform digital terpercaya untuk pendaftaran, pembayaran tiket, dan manajemen event lari marathon di seluruh Indonesia.</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-2.5 py-1 bg-white/8 border border-white/10 rounded-full text-[10px] font-bold text-slate-300 uppercase tracking-wide">🏃 E-Ticket QR</span>
                        <span class="px-2.5 py-1 bg-white/8 border border-white/10 rounded-full text-[10px] font-bold text-slate-300 uppercase tracking-wide">💳 Midtrans</span>
                        <span class="px-2.5 py-1 bg-white/8 border border-white/10 rounded-full text-[10px] font-bold text-slate-300 uppercase tracking-wide">📅 Google Calendar</span>
                    </div>
                </div>
                {{-- Quick Links --}}
                <div>
                    <h4 class="text-xs font-extrabold text-white uppercase tracking-widest mb-4">Navigasi Cepat</h4>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('events.index') }}" class="text-sm text-slate-400 hover:text-[#F05423] transition-colors font-medium">Event Lari</a></li>
                        @auth
                            @if(auth()->user()->isRunner() || auth()->user()->isSuperAdmin())
                                <li><a href="{{ route('runner.dashboard') }}" class="text-sm text-slate-400 hover:text-[#F05423] transition-colors font-medium">Dashboard Tiket</a></li>
                            @endif
                            @if(auth()->user()->isOrganizer() || auth()->user()->isSuperAdmin())
                                <li><a href="{{ route('organizer.events') }}" class="text-sm text-slate-400 hover:text-[#F05423] transition-colors font-medium">Kelola Event</a></li>
                            @endif
                            @if(auth()->user()->isMarshal() || auth()->user()->isSuperAdmin())
                                <li><a href="{{ route('marshal.scanner') }}" class="text-sm text-slate-400 hover:text-[#F05423] transition-colors font-medium">Scanner Racepack</a></li>
                            @endif
                            <li><a href="{{ route('account.settings') }}" class="text-sm text-slate-400 hover:text-[#F05423] transition-colors font-medium">Pengaturan Akun</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="text-sm text-slate-400 hover:text-[#F05423] transition-colors font-medium">Masuk</a></li>
                            <li><a href="{{ route('register') }}" class="text-sm text-slate-400 hover:text-[#F05423] transition-colors font-medium">Daftar Akun</a></li>
                        @endauth
                    </ul>
                </div>
                {{-- Platform --}}
                <div>
                    <h4 class="text-xs font-extrabold text-white uppercase tracking-widest mb-4">Platform</h4>
                    <ul class="space-y-2.5">
                        <li><span class="text-sm text-slate-400 font-medium">Laravel 11 + Blade</span></li>
                        <li><span class="text-sm text-slate-400 font-medium">Midtrans Payment Gateway</span></li>
                        <li><span class="text-sm text-slate-400 font-medium">Google OAuth 2.0</span></li>
                        <li><span class="text-sm text-slate-400 font-medium">QR Code E-Ticket</span></li>
                        <li><span class="text-sm text-slate-400 font-medium">Google Calendar API</span></li>
                    </ul>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-6">
                <p class="text-xs text-slate-500 text-center sm:text-left">&copy; {{ date('Y') }} <strong class="text-slate-400">RunFest SaaS</strong> — Sports Event & Racepack Ticketing Platform. All rights reserved.</p>
                <div class="flex items-center gap-1.5">
                    <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                    <span class="text-[11px] text-slate-500 font-semibold">Sistem Aktif</span>
                </div>
            </div>
        </div>
    </footer>

    {{-- Scripts --}}
    <script>
    (function() {
        // Theme pill & buttons
        const themeToggles = document.querySelectorAll('#theme-toggle, .theme-toggle-btn');
        themeToggles.forEach(tt => {
            tt.addEventListener('click', () => {
                const isDark = document.documentElement.classList.toggle('dark');
                document.documentElement.classList.toggle('light', !isDark);
                localStorage.setItem('color-theme', isDark ? 'dark' : 'light');
            });
        });

        // Profile dropdown
        const pb = document.getElementById('profile-btn');
        const pd = document.getElementById('profile-dropdown');
        const pc = document.getElementById('profile-chevron');
        if (pb && pd) {
            pb.addEventListener('click', e => {
                e.stopPropagation();
                const open = pd.classList.toggle('hidden');
                pb.setAttribute('aria-expanded', !pd.classList.contains('hidden'));
                if (pc) pc.style.transform = pd.classList.contains('hidden') ? '' : 'rotate(180deg)';
            });
            document.addEventListener('click', e => {
                if (!pb.contains(e.target)) {
                    pd.classList.add('hidden');
                    if (pc) pc.style.transform = '';
                }
            });
        }

        // Mobile menu
        const mt = document.getElementById('mobile-menu-toggle');
        const mm = document.getElementById('mobile-menu');
        if (mt && mm) mt.addEventListener('click', () => mm.classList.toggle('hidden'));

        // Live search
        function setupSearch(inputId, boxId, innerId) {
            const inp = document.getElementById(inputId);
            const box = document.getElementById(boxId);
            const inn = document.getElementById(innerId);
            if (!inp) return;
            let timer;
            inp.addEventListener('input', () => {
                clearTimeout(timer);
                const q = inp.value.trim();
                if (q.length < 2) { if (box) box.classList.add('hidden'); return; }
                timer = setTimeout(() => {
                    fetch('/events?search=' + encodeURIComponent(q) + '&json=1', { headers: {'X-Requested-With':'XMLHttpRequest','Accept':'application/json'} })
                        .then(r => r.ok ? r.json() : null)
                        .then(data => {
                            if (!inn || !box) return;
                            if (!data || !data.length) {
                                inn.innerHTML = '<div class="px-4 py-3 text-xs text-slate-400 text-center">Tidak ada event ditemukan.</div>';
                            } else {
                                inn.innerHTML = data.map(ev => '<a href="/events/' + ev.slug + '" class="flex items-center gap-3 px-3 py-2.5 hover:bg-orange-50 dark:hover:bg-white/8 rounded-xl mx-1 transition-colors"><span class="w-8 h-8 flex items-center justify-center bg-orange-100 dark:bg-orange-500/20 rounded-lg text-base shrink-0">🏃</span><div class="overflow-hidden"><div class="text-xs font-bold text-slate-800 dark:text-white truncate hover:text-[#F05423]">' + ev.nama_event + '</div><div class="text-[10px] text-slate-400 truncate">' + (ev.lokasi_venue || '') + '</div></div></a>').join('');
                            }
                            box.classList.remove('hidden');
                        })
                        .catch(() => { if (box) box.classList.add('hidden'); });
                }, 280);
            });
            document.addEventListener('click', e => { if (box && !inp.contains(e.target) && !box.contains(e.target)) box.classList.add('hidden'); });
        }
        setupSearch('navbar-search', 'search-results', 'search-results-inner');

        // Mobile search → redirect on Enter
        ['mobile-search','navbar-search'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.addEventListener('keydown', e => {
                if (e.key === 'Enter' && el.value.trim()) window.location.href = '/events?search=' + encodeURIComponent(el.value.trim());
            });
        });

        // Auto-dismiss flash after 5s
        setTimeout(() => {
            const f = document.getElementById('flash-message');
            if (f) { f.style.transition = 'opacity .5s'; f.style.opacity = '0'; setTimeout(() => f.remove(), 500); }
        }, 5000);
    })();
    </script>

    @stack('scripts')
</body>
</html>