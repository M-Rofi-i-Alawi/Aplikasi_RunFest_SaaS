@extends('layouts.app')

@section('title', 'Admin Dashboard — RunFest SaaS')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-brand-navy">
    <!-- Admin Header -->
    <div class="bg-gradient-to-r from-brand-navy to-brand-navy-light dark:from-gray-900 dark:to-gray-800 text-white py-10 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-brand-orange/20 border border-brand-orange/30">
                            <svg class="w-5 h-5 text-brand-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </span>
                        <span class="text-brand-orange text-sm font-semibold uppercase tracking-widest">SuperAdmin Panel</span>
                    </div>
                    <h1 class="text-3xl font-black italic uppercase tracking-tight">Dashboard Sistem</h1>
                    <p class="text-slate-300 mt-1 text-sm">Ringkasan statistik dan aktivitas terkini platform RunFest SaaS.</p>
                </div>
                <div class="text-right text-sm text-slate-400">
                    <div class="text-white font-semibold">{{ auth()->user()->nama }}</div>
                    <div>{{ now()->isoFormat('dddd, D MMMM YYYY') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Nav -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-20 shadow-sm">
        <div class="max-w-7xl mx-auto px-4">
            <nav class="flex gap-1 overflow-x-auto py-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold bg-brand-orange text-white whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.organizers') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 whitespace-nowrap transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Organizer
                    @if($stats['organizers_pending'] > 0)
                        <span class="bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">{{ $stats['organizers_pending'] }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.events') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 whitespace-nowrap transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Event
                    @if($stats['events_moderasi'] > 0)
                        <span class="bg-amber-500 text-white text-xs rounded-full px-1.5 py-0.5">{{ $stats['events_moderasi'] }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.marshals') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 whitespace-nowrap transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Marshal
                </a>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-8">

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-xl text-green-800 dark:text-green-300 text-sm flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
            <div class="glass-card p-5">
                <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider">Total Event</p>
                <p class="text-3xl font-black text-brand-navy dark:text-white mt-1">{{ number_format($stats['total_events']) }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $stats['events_publikasi'] }} dipublikasi</p>
            </div>
            <div class="glass-card p-5 border-l-4 border-amber-400">
                <p class="text-xs text-amber-600 dark:text-amber-400 font-semibold uppercase tracking-wider">Butuh Moderasi</p>
                <p class="text-3xl font-black text-amber-500 mt-1">{{ number_format($stats['events_moderasi']) }}</p>
                <p class="text-xs text-gray-400 mt-1">event menunggu review</p>
            </div>
            <div class="glass-card p-5">
                <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider">Total Organizer</p>
                <p class="text-3xl font-black text-brand-navy dark:text-white mt-1">{{ number_format($stats['total_organizers']) }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $stats['organizers_aktif'] }} aktif</p>
            </div>
            <div class="glass-card p-5 border-l-4 border-red-400">
                <p class="text-xs text-red-600 dark:text-red-400 font-semibold uppercase tracking-wider">EO Pending</p>
                <p class="text-3xl font-black text-red-500 mt-1">{{ number_format($stats['organizers_pending']) }}</p>
                <p class="text-xs text-gray-400 mt-1">belum diverifikasi</p>
            </div>
            <div class="glass-card p-5">
                <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider">Total Pelari</p>
                <p class="text-3xl font-black text-brand-navy dark:text-white mt-1">{{ number_format($stats['total_runners']) }}</p>
                <p class="text-xs text-gray-400 mt-1">akun terdaftar</p>
            </div>
            <div class="glass-card p-5">
                <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider">Total Tiket</p>
                <p class="text-3xl font-black text-brand-navy dark:text-white mt-1">{{ number_format($stats['total_tiket']) }}</p>
                <p class="text-xs text-gray-400 mt-1">semua pendaftaran</p>
            </div>
            <div class="glass-card p-5 border-l-4 border-green-400">
                <p class="text-xs text-green-600 dark:text-green-400 font-semibold uppercase tracking-wider">Tiket Lunas</p>
                <p class="text-3xl font-black text-green-500 mt-1">{{ number_format($stats['tiket_lunas']) }}</p>
                <p class="text-xs text-gray-400 mt-1">pembayaran sukses</p>
            </div>
            <div class="glass-card p-5">
                <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider">Total Marshal</p>
                <p class="text-3xl font-black text-brand-navy dark:text-white mt-1">{{ number_format($stats['total_marshals']) }}</p>
                <p class="text-xs text-gray-400 mt-1">di seluruh event</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Event Butuh Moderasi -->
            <div class="glass-card p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                        Event Butuh Moderasi
                    </h2>
                    <a href="{{ route('admin.events') }}" class="text-xs text-brand-orange font-semibold hover:underline">Lihat Semua →</a>
                </div>

                @forelse($pendingEvents as $event)
                    <div class="flex items-start gap-3 py-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                        <div class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm text-gray-900 dark:text-white truncate">{{ $event->nama_event }}</p>
                            <p class="text-xs text-gray-500">{{ $event->organizer->nama ?? 'N/A' }} • {{ $event->tanggal_event?->isoFormat('D MMM YYYY') }}</p>
                        </div>
                        <form action="{{ route('admin.events.update-status', $event->id_event) }}" method="POST" class="flex-shrink-0">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status_event" value="Publikasi">
                            <button type="submit" class="text-xs bg-green-500 text-white px-3 py-1.5 rounded-lg font-semibold hover:bg-green-600 transition-colors">Publikasikan</button>
                        </form>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400">
                        <svg class="w-10 h-10 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm">Tidak ada event yang menunggu moderasi.</p>
                    </div>
                @endforelse
            </div>

            <!-- EO Pending Verifikasi -->
            <div class="glass-card p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-red-400 animate-pulse"></span>
                        EO Pending Verifikasi
                    </h2>
                    <a href="{{ route('admin.organizers') }}" class="text-xs text-brand-orange font-semibold hover:underline">Lihat Semua →</a>
                </div>

                @forelse($pendingOrganizers as $eo)
                    <div class="flex items-center gap-3 py-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-brand-orange to-orange-400 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                            {{ strtoupper(substr($eo->nama, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm text-gray-900 dark:text-white truncate">{{ $eo->nama }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $eo->email }}</p>
                        </div>
                        <form action="{{ route('admin.organizers.update-status', $eo->id_user) }}" method="POST" class="flex-shrink-0">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status_akun" value="Aktif">
                            <button type="submit" class="text-xs bg-brand-orange text-white px-3 py-1.5 rounded-lg font-semibold hover:bg-brand-orange-dark transition-colors">Aktifkan</button>
                        </form>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400">
                        <svg class="w-10 h-10 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm">Tidak ada EO yang menunggu verifikasi.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
