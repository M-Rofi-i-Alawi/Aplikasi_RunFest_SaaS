@extends('layouts.app')

@section('title', 'Moderasi Event — Admin RunFest')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-brand-navy">
    <!-- Header -->
    <div class="bg-gradient-to-r from-brand-navy to-brand-navy-light text-white py-10 px-4">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-black italic uppercase tracking-tight">Moderasi Event</h1>
            <p class="text-slate-300 mt-1 text-sm">Review dan kelola semua event yang diajukan oleh Organizer.</p>
        </div>
    </div>

    <!-- Admin Nav -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-20 shadow-sm">
        <div class="max-w-7xl mx-auto px-4">
            <nav class="flex gap-1 overflow-x-auto py-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 whitespace-nowrap transition-colors">Dashboard</a>
                <a href="{{ route('admin.organizers') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 whitespace-nowrap transition-colors">Organizer</a>
                <a href="{{ route('admin.events') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold bg-brand-orange text-white whitespace-nowrap">Event</a>
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

        <!-- Filter -->
        <div class="glass-card p-5 mb-6">
            <form method="GET" action="{{ route('admin.events') }}" class="flex flex-col sm:flex-row gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama event..." class="flex-1 border border-gray-200 dark:border-gray-600 rounded-lg px-4 py-2.5 text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-brand-orange focus:border-transparent outline-none">
                <select name="status" class="border border-gray-200 dark:border-gray-600 rounded-lg px-4 py-2.5 text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-brand-orange outline-none">
                    <option value="">Semua Status</option>
                    <option value="Draft"      @selected(request('status') === 'Draft')>Draft</option>
                    <option value="Moderasi"   @selected(request('status') === 'Moderasi')>Moderasi</option>
                    <option value="Publikasi"  @selected(request('status') === 'Publikasi')>Publikasi</option>
                    <option value="Selesai"    @selected(request('status') === 'Selesai')>Selesai</option>
                    <option value="Dibatalkan" @selected(request('status') === 'Dibatalkan')>Dibatalkan</option>
                </select>
                <button type="submit" class="btn-brand-orange text-white px-5 py-2.5 rounded-lg text-sm font-semibold">Filter</button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.events') }}" class="px-5 py-2.5 rounded-lg text-sm font-semibold border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Reset</a>
                @endif
            </form>
        </div>

        <!-- Event Table -->
        <div class="glass-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                            <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Event</th>
                            <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">Organizer</th>
                            <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden sm:table-cell">Tanggal</th>
                            <th class="text-center px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="text-center px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($events as $event)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($event->banner_image_url)
                                            <img src="{{ $event->banner_image_url }}" alt="Banner" class="w-12 h-10 rounded-lg object-cover flex-shrink-0">
                                        @else
                                            <div class="w-12 h-10 rounded-lg bg-gradient-to-br from-brand-orange/20 to-orange-100 dark:from-brand-orange/10 dark:to-gray-700 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-brand-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-white">{{ $event->nama_event }}</p>
                                            <p class="text-xs text-gray-400 truncate max-w-xs">{{ $event->lokasi_venue }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell">
                                    <p class="text-gray-700 dark:text-gray-300">{{ $event->organizer->nama ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-400">{{ $event->organizer->email ?? '' }}</p>
                                </td>
                                <td class="px-6 py-4 hidden sm:table-cell">
                                    <p class="text-gray-700 dark:text-gray-300 font-medium">{{ $event->tanggal_event?->isoFormat('D MMM YYYY') }}</p>
                                    <p class="text-xs text-gray-400">Dibuat {{ $event->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $statusClass = match($event->status_event) {
                                            'Publikasi'  => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400',
                                            'Moderasi'   => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400',
                                            'Selesai'    => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400',
                                            'Dibatalkan' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400',
                                            default      => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                        {{ $event->status_event }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-1 flex-wrap">
                                        @if($event->status_event !== 'Publikasi')
                                            <form action="{{ route('admin.events.update-status', $event->id_event) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status_event" value="Publikasi">
                                                <button type="submit" class="text-xs bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-lg font-semibold transition-colors">Publikasi</button>
                                            </form>
                                        @endif
                                        @if(!in_array($event->status_event, ['Dibatalkan', 'Selesai']))
                                            <form action="{{ route('admin.events.update-status', $event->id_event) }}" method="POST" onsubmit="return confirm('Batalkan event ini?')">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status_event" value="Dibatalkan">
                                                <button type="submit" class="text-xs bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg font-semibold transition-colors">Batalkan</button>
                                            </form>
                                        @endif
                                        @if($event->status_event === 'Publikasi')
                                            <form action="{{ route('admin.events.update-status', $event->id_event) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status_event" value="Selesai">
                                                <button type="submit" class="text-xs bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg font-semibold transition-colors">Selesai</button>
                                            </form>
                                        @endif
                                        <a href="{{ route('events.show', $event->slug) }}" target="_blank" class="text-xs border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 px-3 py-1.5 rounded-lg font-semibold transition-colors">Lihat</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <p class="font-medium">Tidak ada event ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($events->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $events->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
