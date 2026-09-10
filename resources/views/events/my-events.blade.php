@extends('layouts.app')

@section('title', 'Kelola Event Saya')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 border-b border-slate-200 pb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Kelola Event Saya</h1>
            <p class="text-sm text-slate-600 mt-1">Daftar event lari yang Anda buat sebagai Organizer.</p>
        </div>
        <a href="{{ route('organizer.events.create') }}"
           class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider transition-colors shadow-sm">
            + Buat Event Baru
        </a>
    </div>

    @if($events->count() > 0)
        <div class="space-y-4">
            @foreach($events as $event)
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center space-x-2">
                            <h3 class="text-lg font-bold text-slate-900">{{ $event->nama_event }}</h3>
                            <span class="px-2 py-0.5 rounded text-[11px] font-extrabold uppercase tracking-wider
                                {{ $event->status_event === 'Publikasi' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-slate-100 text-slate-700 border border-slate-300' }}">
                                {{ $event->status_event }}
                            </span>
                        </div>
                        <div class="flex flex-wrap items-center gap-4 text-xs font-medium text-slate-600">
                            <span>Tanggal: <strong class="text-slate-900">{{ $event->tanggal_event->format('d M Y') }}</strong></span>
                            <span>Venue: <strong class="text-slate-900">{{ $event->lokasi_venue }}</strong></span>
                            <span>Peserta: <strong class="text-blue-600 font-extrabold">{{ $event->pendaftaran_count }}</strong></span>
                            <span>Kategori: <strong class="text-slate-900">{{ $event->kategori->count() }}</strong></span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 flex-shrink-0">
                        <a href="{{ route('events.show', $event->slug) }}" 
                           class="px-3 py-1.5 rounded border border-slate-300 bg-white text-slate-700 font-bold text-xs hover:bg-slate-50 transition-colors">
                            Lihat
                        </a>
                        <a href="{{ route('organizer.events.edit', $event->id_event) }}" 
                           class="px-3 py-1.5 rounded bg-blue-50 border border-blue-200 text-blue-700 font-bold text-xs hover:bg-blue-100 transition-colors">
                            Edit Event
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $events->links() }}</div>
    @else
        <div class="bg-white rounded-xl border border-slate-200 p-12 text-center">
            <svg class="w-12 h-12 text-slate-400 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            <h3 class="text-lg font-bold text-slate-800">Belum Memiliki Event</h3>
            <p class="text-sm text-slate-500 mt-1 mb-4">Mulai publikasikan event lari Anda sekarang.</p>
            <a href="{{ route('organizer.events.create') }}" class="px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider shadow-sm">
                Buat Event Baru
            </a>
        </div>
    @endif
</div>
@endsection
