@extends('layouts.app')

@section('title', 'Manajemen Organizer — Admin RunFest')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-brand-navy">
    <!-- Header -->
    <div class="bg-gradient-to-r from-brand-navy to-brand-navy-light text-white py-10 px-4">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-black italic uppercase tracking-tight">Manajemen Organizer</h1>
            <p class="text-slate-300 mt-1 text-sm">Verifikasi, aktivasi, dan kelola akun Event Organizer (EO).</p>
        </div>
    </div>

    <!-- Admin Nav -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-20 shadow-sm">
        <div class="max-w-7xl mx-auto px-4">
            <nav class="flex gap-1 overflow-x-auto py-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 whitespace-nowrap transition-colors">Dashboard</a>
                <a href="{{ route('admin.organizers') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold bg-brand-orange text-white whitespace-nowrap">Organizer</a>
                <a href="{{ route('admin.events') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 whitespace-nowrap transition-colors">Event</a>
                <a href="{{ route('admin.marshals') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 whitespace-nowrap transition-colors">Marshal</a>
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
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-xl text-red-800 dark:text-red-300 text-sm flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Filter & Search -->
        <div class="glass-card p-5 mb-6">
            <form method="GET" action="{{ route('admin.organizers') }}" class="flex flex-col sm:flex-row gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email organizer..." class="flex-1 border border-gray-200 dark:border-gray-600 rounded-lg px-4 py-2.5 text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-brand-orange focus:border-transparent outline-none">
                <select name="status" class="border border-gray-200 dark:border-gray-600 rounded-lg px-4 py-2.5 text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-brand-orange outline-none">
                    <option value="">Semua Status</option>
                    <option value="Pending" @selected(request('status') === 'Pending')>Pending</option>
                    <option value="Aktif" @selected(request('status') === 'Aktif')>Aktif</option>
                    <option value="Diblokir" @selected(request('status') === 'Diblokir')>Diblokir</option>
                </select>
                <button type="submit" class="btn-brand-orange text-white px-5 py-2.5 rounded-lg text-sm font-semibold">Filter</button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.organizers') }}" class="px-5 py-2.5 rounded-lg text-sm font-semibold border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Reset</a>
                @endif
            </form>
        </div>

        <!-- Organizer Table -->
        <div class="glass-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                            <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Organizer</th>
                            <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden sm:table-cell">Kontak</th>
                            <th class="text-center px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">Event</th>
                            <th class="text-center px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="text-center px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($organizers as $eo)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-brand-orange to-orange-400 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                            {{ strtoupper(substr($eo->nama, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-white">{{ $eo->nama }}</p>
                                            <p class="text-xs text-gray-400">Bergabung {{ $eo->created_at->isoFormat('D MMM YYYY') }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 hidden sm:table-cell">
                                    <p class="text-gray-700 dark:text-gray-300">{{ $eo->email }}</p>
                                    <p class="text-xs text-gray-400">{{ $eo->no_hp ?? '-' }}</p>
                                </td>
                                <td class="px-6 py-4 text-center hidden md:table-cell">
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $eo->event_lari_count }}</span>
                                    <span class="text-xs text-gray-400 block">event dibuat</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $badgeClass = match($eo->status_akun) {
                                            'Aktif'    => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400',
                                            'Diblokir' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400',
                                            default    => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                        {{ $eo->status_akun }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        @if($eo->status_akun !== 'Aktif')
                                            <form action="{{ route('admin.organizers.update-status', $eo->id_user) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status_akun" value="Aktif">
                                                <button type="submit" class="text-xs bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-lg font-semibold transition-colors">Aktifkan</button>
                                            </form>
                                        @endif
                                        @if($eo->status_akun !== 'Diblokir')
                                            <form action="{{ route('admin.organizers.update-status', $eo->id_user) }}" method="POST" onsubmit="return confirm('Yakin blokir akun {{ $eo->nama }}?')">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status_akun" value="Diblokir">
                                                <button type="submit" class="text-xs bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg font-semibold transition-colors">Blokir</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <p class="font-medium">Tidak ada organizer ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($organizers->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $organizers->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
