<!DOCTYPE html>
<html lang="id" class="scroll-smooth overflow-x-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="RunFest SaaS — Platform Pendaftaran Event Lari & Ticketing Race Pack terpercaya di Indonesia.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'RunFest SaaS') — Sports Event & Ticketing Platform</title>

    {{-- Script Pencegah Flash Warna --}}
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    {{-- Google Fonts: Plus Jakarta Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- Tailwind CSS CDN dengan Dark Mode Class --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            orange: '#ff5500',
                            'orange-hover': '#e64d00',
                            navy: '#0b1329',
                        }
                    },
                    fontFamily: {
                        'sans': ['"Plus Jakarta Sans"', 'Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        html, body {
            max-width: 100vw !important;
            overflow-x: hidden !important;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Glassmorphism Styles (Adaptif Terang & Gelap) */
        .glass-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Light Mode Card */
        .glass-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }

        /* Dark Mode Glass Card */
        .dark .glass-card {
            background: rgba(11, 19, 41, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5);
        }

        .glass-card:hover {
            transform: translateY(-2px);
        }

        .dark .glass-card:hover {
            border-color: rgba(255, 85, 0, 0.4);
            box-shadow: 0 20px 45px -10px rgba(255, 85, 0, 0.25);
        }

        .btn-brand-orange {
            background: linear-gradient(135deg, #ff5500 0%, #ff7700 100%);
            box-shadow: 0 8px 20px -4px rgba(255, 85, 0, 0.35);
            transition: all 0.25s ease;
        }

        .btn-brand-orange:hover {
            background: linear-gradient(135deg, #e64d00 0%, #ff5500 100%);
            box-shadow: 0 12px 25px -4px rgba(255, 85, 0, 0.45);
            transform: translateY(-1px);
        }
    </style>

    @stack('styles')
</head>
<body class="bg-slate-50 dark:bg-[#070c18] text-slate-900 dark:text-slate-100 antialiased min-h-screen flex flex-col relative transition-colors duration-300 overflow-x-hidden w-full max-w-full">

    {{-- Dark Mode Ambient Glow Blobs Wrapper (Terisolasi dari overflow layout) --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="hidden dark:block absolute top-0 left-1/4 w-[550px] h-[550px] bg-[#ff5500]/12 rounded-full blur-[150px]"></div>
        <div class="hidden dark:block absolute top-1/3 -right-20 w-[500px] h-[500px] bg-blue-900/20 rounded-full blur-[160px]"></div>
    </div>

    {{-- ========== NAVBAR (Dual Theme Switcher Adaptif) ========== --}}
    <nav class="bg-white/90 dark:bg-[#070c18]/85 backdrop-blur-md dark:backdrop-blur-xl border-b border-slate-200/80 dark:border-white/10 sticky top-0 z-50 shadow-sm transition-colors duration-300 w-full max-w-full">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                
                {{-- Left: Brand Logo & Title --}}
                <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                    <img src="{{ asset('images/logo-icon.png') }}" alt="RunFest Logo Icon" class="h-9 sm:h-10 w-auto object-contain group-hover:scale-105 transition-transform duration-300">
                    <div class="flex flex-col">
                        <span class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 dark:text-white leading-tight">
                            Run<span class="text-[#ff5500]">Fest</span>
                        </span>
                        <span class="text-[9px] font-extrabold tracking-[0.25em] text-slate-400 dark:text-slate-400 uppercase -mt-0.5">S A A S</span>
                    </div>
                </a>

                {{-- Center: Desktop Nav Links --}}
                <div class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('events.index') }}" 
                       class="px-4 py-2 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('events.*') || request()->routeIs('home') ? 'text-[#ff5500] bg-orange-50 dark:bg-orange-500/10 font-bold border border-orange-200/60 dark:border-orange-500/30' : 'text-slate-700 dark:text-slate-300 hover:text-[#ff5500] dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/10' }}">
                        Event Lari
                    </a>
                    @auth
                        @if(auth()->user()->isRunner() || auth()->user()->isSuperAdmin())
                            <a href="{{ route('runner.dashboard') }}" 
                               class="px-4 py-2 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('runner.*') ? 'text-[#ff5500] bg-orange-50 dark:bg-orange-500/10 font-bold border border-orange-200/60 dark:border-orange-500/30' : 'text-slate-700 dark:text-slate-300 hover:text-[#ff5500] dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/10' }}">
                                Dashboard Ticket
                            </a>
                        @endif
                        @if(auth()->user()->isOrganizer() || auth()->user()->isSuperAdmin())
                            <a href="{{ route('organizer.events') }}" 
                               class="px-4 py-2 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('organizer.*') ? 'text-[#ff5500] bg-orange-50 dark:bg-orange-500/10 font-bold border border-orange-200/60 dark:border-orange-500/30' : 'text-slate-700 dark:text-slate-300 hover:text-[#ff5500] dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/10' }}">
                                Kelola Event
                            </a>
                        @endif
                        @if(auth()->user()->isMarshal() || auth()->user()->isSuperAdmin())
                            <a href="{{ route('marshal.scanner') }}" 
                               class="px-4 py-2 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('marshal.*') ? 'text-[#ff5500] bg-orange-50 dark:bg-orange-500/10 font-bold border border-orange-200/60 dark:border-orange-500/30' : 'text-slate-700 dark:text-slate-300 hover:text-[#ff5500] dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/10' }}">
                                Scanner Racepack
                            </a>
                        @endif
                    @endauth
                </div>

                {{-- Right: Theme Toggle & User Profile --}}
                <div class="flex items-center space-x-2 sm:space-x-3">
                    
                    {{-- ☀️ / 🌙 THEME SWITCHER TOGGLE BUTTON --}}
                    <button id="theme-toggle" type="button" 
                            class="p-2.5 rounded-xl text-slate-500 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/10 border border-slate-200 dark:border-white/10 transition-all"
                            title="Beralih Mode Terang / Gelap">
                        {{-- Icon Sun (Tampil di Mode Dark) --}}
                        <svg id="theme-toggle-light-icon" class="hidden w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 100 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path>
                        </svg>
                        {{-- Icon Moon (Tampil di Mode Light) --}}
                        <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5 text-slate-700" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                    </button>

                    @guest
                        <a href="{{ route('login') }}" class="px-3.5 py-2 text-sm font-bold text-slate-700 dark:text-slate-300 hover:text-[#ff5500] dark:hover:text-white transition-colors">Masuk</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-bold text-white btn-brand-orange rounded-xl transition-all">
                            Daftar
                        </a>
                    @else
                        <div class="flex items-center space-x-2 sm:space-x-3">
                            {{-- User Info --}}
                            <div class="hidden sm:flex items-center space-x-2.5 bg-slate-100/90 dark:bg-white/5 px-3 py-1.5 rounded-2xl border border-slate-200/80 dark:border-white/10">
                                <div class="w-8 h-8 rounded-full bg-[#ff5500] text-white font-extrabold text-xs flex items-center justify-center shadow-sm">
                                    {{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}
                                </div>
                                <div class="text-left">
                                    <p class="text-xs font-bold text-slate-900 dark:text-white leading-tight max-w-[120px] truncate">{{ auth()->user()->nama }}</p>
                                    <span class="inline-block text-[10px] font-bold text-[#ff5500]">
                                        {{ auth()->user()->role }}
                                    </span>
                                </div>
                            </div>

                            {{-- Settings Button --}}
                            <a href="{{ route('account.settings') }}" 
                               class="p-2 text-slate-500 dark:text-slate-300 hover:text-[#ff5500] dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/10 rounded-xl transition-all" 
                               title="Pengaturan Akun">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                </svg>
                            </a>

                            {{-- Logout Button --}}
                            <form method="POST" action="{{ route('logout') }}" class="inline-block">
                                @csrf
                                <button type="submit" class="p-2 text-slate-500 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl transition-all" title="Keluar">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endguest

                    {{-- Mobile Menu Toggle --}}
                    <button type="button" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')"
                        class="md:hidden p-2 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/10 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Mobile Dropdown Menu --}}
            <div id="mobile-menu" class="hidden md:hidden py-3 border-t border-slate-200 dark:border-white/10 space-y-1">
                <a href="{{ route('events.index') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-white/10">
                    Event Lari
                </a>
                @auth
                    @if(auth()->user()->isRunner() || auth()->user()->isSuperAdmin())
                        <a href="{{ route('runner.dashboard') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-white/10">
                            Dashboard Ticket
                        </a>
                    @endif
                    @if(auth()->user()->isOrganizer() || auth()->user()->isSuperAdmin())
                        <a href="{{ route('organizer.events') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-white/10">
                            Kelola Event
                        </a>
                    @endif
                    @if(auth()->user()->isMarshal() || auth()->user()->isSuperAdmin())
                        <a href="{{ route('marshal.scanner') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-white/10">
                            Scanner Racepack
                        </a>
                    @endif
                    <div class="border-t border-slate-200 dark:border-white/10 mt-2 pt-2">
                        <a href="{{ route('account.settings') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-white/10">
                            ⚙️ Pengaturan Akun
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    {{-- ========== FLASH MESSAGES ========== --}}
    @if(session('success') || session('error') || session('warning') || session('info'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4" id="flash-message">
            @if(session('success'))
                <div class="bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-200 rounded-2xl p-4 flex items-center justify-between text-sm font-medium shadow-sm backdrop-blur-md">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-600 dark:text-emerald-400 font-bold ml-4">✕</button>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-rose-50 dark:bg-rose-500/15 border border-rose-200 dark:border-rose-500/30 text-rose-800 dark:text-rose-200 rounded-2xl p-4 flex items-center justify-between text-sm font-medium shadow-sm backdrop-blur-md">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-rose-600 dark:text-rose-400 font-bold ml-4">✕</button>
                </div>
            @endif
            @if(session('warning'))
                <div class="bg-amber-50 dark:bg-amber-500/15 border border-amber-200 dark:border-amber-500/30 text-amber-800 dark:text-amber-200 rounded-2xl p-4 flex items-center justify-between text-sm font-medium shadow-sm backdrop-blur-md">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span>{{ session('warning') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-amber-600 dark:text-amber-400 font-bold ml-4">✕</button>
                </div>
            @endif
            @if(session('info'))
                <div class="bg-blue-50 dark:bg-blue-500/15 border border-blue-200 dark:border-blue-500/30 text-blue-800 dark:text-blue-200 rounded-2xl p-4 flex items-center justify-between text-sm font-medium shadow-sm backdrop-blur-md">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                        <span>{{ session('info') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-blue-600 dark:text-blue-400 font-bold ml-4">✕</button>
                </div>
            @endif
        </div>
    @endif

    {{-- ========== MAIN CONTENT ========== --}}
    <main class="flex-1 w-full max-w-full overflow-x-hidden">
        @yield('content')
    </main>

    {{-- ========== FOOTER (Adaptif Terang & Gelap) ========== --}}
    <footer class="bg-white dark:bg-[#070c18]/90 backdrop-blur-xl border-t border-slate-200 dark:border-white/10 mt-20 transition-colors duration-300 w-full max-w-full overflow-x-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo-icon.png') }}" alt="RunFest Logo Icon" class="h-8 w-auto">
                    <span class="text-sm font-black text-slate-900 dark:text-white">Run<span class="text-[#ff5500]">Fest</span> SaaS</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400">&copy; {{ date('Y') }} RunFest SaaS — Sports Event & Racepack Ticketing Platform.</p>
            </div>
        </div>
    </footer>

    {{-- Theme Switcher Logic Script --}}
    <script>
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
        const themeToggleBtn = document.getElementById('theme-toggle');

        // Set initial icon based on current theme state
        if (document.documentElement.classList.contains('dark')) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        themeToggleBtn.addEventListener('click', function() {
            // Toggle icons
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            // If dark mode was set
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
