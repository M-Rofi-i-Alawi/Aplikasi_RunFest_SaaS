@extends('layouts.app')

@section('title', auth()->user()->isSuperAdmin() ? 'Kelola Semua Event (SuperAdmin) — RunFest SaaS' : 'Kelola Event Lari Saya — RunFest SaaS')

@push('styles')
<style>
    .quota-track { background: rgba(0,0,0,0.06); }
    .dark .quota-track { background: rgba(255,255,255,0.1); }
    .quota-fill { background: linear-gradient(90deg, #F05423, #FF7A4D); border-radius: 999px; }
    .event-manage-card { transition: all .25s cubic-bezier(0.4, 0, 0.2, 1); }
    .event-manage-card:hover { transform: translateY(-2px); }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- ======================================================
         PAGE HEADER & CTA
         ====================================================== --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-white/10 pb-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                @if(auth()->user()->isSuperAdmin())
                    <span class="px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-widest text-[#F05423] bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/30">
                        🛡️ SUPERADMIN CONTROL CENTER
                    </span>
                @else
                    <span class="px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-widest text-[#F05423] bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/30">
                        🏢 ORGANIZER DASHBOARD
                    </span>
                @endif
            </div>
            <h1 class="text-3xl sm:text-4xl font-black italic uppercase tracking-tight text-slate-900 dark:text-white">
                @if(auth()->user()->isSuperAdmin())
                    Kelola Semua Event
                @else
                    Kelola Event Saya
                @endif
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 font-medium">
                @if(auth()->user()->isSuperAdmin())
                    Visibilitas & kontrol menyeluruh atas seluruh event marathon & fun run di platform RunFest SaaS.
                @else
                    Pantau pendaftaran, kelola kuota, perbarui rincian lomba, dan tinjau pendapatan event Anda.
                @endif
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('organizer.events.create') }}"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl btn-brand-orange text-white font-black text-xs sm:text-sm uppercase tracking-wider shadow-lg shadow-orange-500/25 transition-all hover:scale-105">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                + Buat Event Baru
            </a>
        </div>
    </div>

    {{-- ======================================================
         FLASH NOTIFICATIONS
         ====================================================== --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-emerald-50 dark:bg-emerald-500/10 border-2 border-emerald-500 text-emerald-800 dark:text-emerald-300 rounded-2xl px-5 py-4 shadow-sm animate-fade-in">
            <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0 font-black text-sm">✓</div>
            <div class="flex-1 text-sm font-bold">{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-3 bg-rose-50 dark:bg-rose-500/10 border-2 border-rose-500 text-rose-800 dark:text-rose-300 rounded-2xl px-5 py-4 shadow-sm animate-fade-in">
            <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center shrink-0 font-black text-sm">✕</div>
            <div class="flex-1 text-sm font-bold">{{ session('error') }}</div>
        </div>
    @endif

    {{-- ======================================================
         4 STATS GRID CARDS (GLASSMORPHISM)
         ====================================================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 1: Total Event --}}
        <div class="glass-card rounded-3xl p-5 border border-slate-200 dark:border-white/10 flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-white/10 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-slate-700 dark:text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
            </div>
            <div class="min-w-0">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Total Event Terdaftar</span>
                <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white leading-tight">{{ number_format($totalEvents) }}</span>
            </div>
        </div>

        {{-- Card 2: Event Publikasi --}}
        <div class="glass-card rounded-3xl p-5 border border-slate-200 dark:border-white/10 flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-500/15 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="min-w-0">
                <span class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block">Event Publikasi (Aktif)</span>
                <span class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 leading-tight">{{ number_format($totalPublikasi) }}</span>
            </div>
        </div>

        {{-- Card 3: Total Peserta --}}
        <div class="glass-card rounded-3xl p-5 border border-slate-200 dark:border-white/10 flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-500/15 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
            </div>
            <div class="min-w-0">
                <span class="text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-wider block">Peserta / Tiket Terjual</span>
                <span class="text-2xl sm:text-3xl font-black text-blue-600 dark:text-blue-400 leading-tight">{{ number_format($totalPeserta) }}</span>
            </div>
        </div>

        {{-- Card 4: Estimasi Pendapatan --}}
        <div class="glass-card rounded-3xl p-5 border border-slate-200 dark:border-white/10 flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 rounded-2xl bg-orange-50 dark:bg-orange-500/15 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-[#F05423]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            </div>
            <div class="min-w-0">
                <span class="text-[10px] font-black text-[#F05423] uppercase tracking-wider block">Estimasi Pendapatan (Rp)</span>
                <span class="text-xl sm:text-2xl font-black text-[#F05423] leading-tight truncate block">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- ======================================================
         EVENT LIST SECTION
         ====================================================== --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-black italic uppercase tracking-tight text-slate-900 dark:text-white">
                Daftar Lomba Terdaftar ({{ $events->total() }})
            </h2>
        </div>

        @if($events->count() > 0)
            <div class="space-y-4">
                @foreach($events as $event)
                    @php
                        $totalKuota = $event->kategori->sum('kuota_peserta');
                        $totalTerisi = $event->kategori->sum('terisi');
                        $persenKuota = $totalKuota > 0 ? min(round(($totalTerisi / $totalKuota) * 100), 100) : 0;

                        $statusBadge = match($event->status_event) {
                            'Publikasi'  => ['bg' => 'bg-emerald-500 text-white shadow-emerald-500/20', 'icon' => '🟢', 'label' => 'Publikasi'],
                            'Draft'      => ['bg' => 'bg-amber-500 text-white shadow-amber-500/20',     'icon' => '📝', 'label' => 'Draft'],
                            'Selesai'    => ['bg' => 'bg-blue-600 text-white shadow-blue-500/20',       'icon' => '🏁', 'label' => 'Selesai'],
                            'Dibatalkan' => ['bg' => 'bg-rose-600 text-white shadow-rose-500/20',       'icon' => '❌', 'label' => 'Dibatalkan'],
                            default      => ['bg' => 'bg-slate-500 text-white',                         'icon' => '❓', 'label' => $event->status_event],
                        };
                    @endphp

                    <div class="event-manage-card glass-card rounded-3xl overflow-hidden border border-slate-200 dark:border-white/10 shadow-sm p-5 sm:p-6">
                        <div class="flex flex-col lg:flex-row gap-5 lg:items-center justify-between">

                            {{-- Left Column: Thumbnail + Info --}}
                            <div class="flex flex-col sm:flex-row gap-4 min-w-0 flex-1">

                                {{-- Thumbnail Poster 16:9 --}}
                                <div class="w-full sm:w-44 sm:h-28 aspect-video sm:aspect-auto rounded-2xl overflow-hidden relative bg-slate-900 shrink-0 border border-slate-200 dark:border-white/10">
                                    @if($event->banner_image_url)
                                        <img src="{{ $event->banner_image_url }}" alt="{{ $event->nama_event }}"
                                             class="w-full h-full object-cover object-center">
                                    @else
                                        <img src="https://images.unsplash.com/photo-1530541930197-ff16ac917b0e?auto=format&fit=crop&w=400&q=80"
                                             alt="{{ $event->nama_event }}"
                                             class="w-full h-full object-cover object-center">
                                    @endif
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 to-transparent"></div>
                                    <div class="absolute bottom-1.5 left-2">
                                        <span class="text-[9px] font-black text-white bg-black/60 px-2 py-0.5 rounded-md backdrop-blur-md">
                                            {{ $event->tanggal_event->format('d M Y') }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Details --}}
                                <div class="space-y-2.5 min-w-0 flex-1">
                                    {{-- Title & Status --}}
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white truncate">
                                            <a href="{{ route('events.show', $event->slug) }}" class="hover:text-[#F05423] transition-colors">
                                                {{ $event->nama_event }}
                                            </a>
                                        </h3>
                                        <span class="px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider shadow-sm {{ $statusBadge['bg'] }}">
                                            {{ $statusBadge['icon'] }} {{ $statusBadge['label'] }}
                                        </span>
                                    </div>

                                    {{-- SuperAdmin: Show Organizer Owner --}}
                                    @if(auth()->user()->isSuperAdmin() && $event->organizer)
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-purple-50 dark:bg-purple-500/10 border border-purple-200 dark:border-purple-500/30 text-purple-700 dark:text-purple-300 text-xs font-bold">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                            Dibuat oleh: <strong class="text-purple-900 dark:text-purple-100">{{ $event->organizer->nama }}</strong> ({{ $event->organizer->email }})
                                        </div>
                                    @endif

                                    {{-- Meta Info --}}
                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-slate-600 dark:text-slate-400 font-medium">
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-[#F05423]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                            <span class="truncate max-w-[200px]">{{ $event->lokasi_venue }}</span>
                                        </span>
                                        @if($event->tanggal_rpc_mulai)
                                            <span class="inline-flex items-center gap-1">
                                                📦 RPC: <strong class="text-slate-900 dark:text-white">{{ $event->tanggal_rpc_mulai->format('d M') }} — {{ $event->tanggal_rpc_selesai ? $event->tanggal_rpc_selesai->format('d M Y') : '-' }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Categories & Quota Progress --}}
                                    <div class="space-y-1.5 pt-1">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            @foreach($event->kategori as $kat)
                                                <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold bg-slate-100 dark:bg-white/10 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-white/10">
                                                    {{ $kat->nama_kategori }} (Rp {{ number_format($kat->harga, 0, ',', '.') }})
                                                </span>
                                            @endforeach
                                        </div>
                                        <div class="flex items-center gap-3 text-[11px] font-bold text-slate-500 dark:text-slate-400">
                                            <span>Kuota Terisi: <strong class="text-[#F05423]">{{ $totalTerisi }}</strong> / {{ $totalKuota }} ({{ $persenKuota }}%)</span>
                                            <div class="w-28 h-1.5 rounded-full quota-track overflow-hidden">
                                                <div class="quota-fill h-full" style="width: {{ $persenKuota }}%"></div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            {{-- Right Column: Structured Action Buttons --}}
                            <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 shrink-0 border-t lg:border-t-0 pt-3 lg:pt-0 border-slate-100 dark:border-white/10">
                                {{-- Lihat Detail Publik --}}
                                <a href="{{ route('events.show', $event->slug) }}"
                                   class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-white/10 hover:bg-slate-200 dark:hover:bg-white/15 text-slate-700 dark:text-slate-200 font-extrabold text-xs uppercase tracking-wider transition-colors border border-slate-200 dark:border-white/10">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.64 0 8.577 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.64 0-8.577-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Lihat
                                </a>

                                {{-- Edit Event --}}
                                <a href="{{ route('organizer.events.edit', $event->id_event) }}"
                                   class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 dark:bg-blue-500/15 dark:hover:bg-blue-500/25 text-blue-700 dark:text-blue-300 font-extrabold text-xs uppercase tracking-wider transition-colors border border-blue-200 dark:border-blue-500/30">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                    Edit
                                </a>

                                {{-- Hapus / Batalkan Event (Safety Rule Alert) --}}
                                <form method="POST" action="{{ route('organizer.events.destroy', $event->id_event) }}"
                                      onsubmit="return confirm('⚠️ KONFIRMASI HAPUS EVENT:\n\nApakah Anda yakin ingin menghapus event &quot;{{ addslashes($event->nama_event) }}&quot;?\n\nSafety Rules:\n- Event dengan peserta LUNAS tidak dapat dihapus permanen (ubah ke status Dibatalkan sebagai gantinya).\n- Seluruh kategori dan pendaftaran non-lunas akan ikut terhapus.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-500/10 dark:hover:bg-rose-500/20 text-rose-700 dark:text-rose-300 font-extrabold text-xs uppercase tracking-wider transition-colors border border-rose-200 dark:border-rose-500/30">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8 flex justify-center">
                {{ $events->links() }}
            </div>
        @else
            <div class="glass-card rounded-3xl p-14 text-center shadow-sm">
                <div class="text-5xl mb-4">🏃</div>
                <h3 class="text-xl font-black text-slate-900 dark:text-white">Belum Ada Event Lari</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 mb-6 max-w-sm mx-auto">
                    Publikasikan event lari marathon atau fun run perdana Anda dan jangkau ribuan pelari di seluruh Indonesia.
                </p>
                <a href="{{ route('organizer.events.create') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl btn-brand-orange text-white font-extrabold text-xs uppercase tracking-wider shadow-lg shadow-orange-500/30">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Buat Event Pertama Sekarang
                </a>
            </div>
        @endif
    </div>

</div>
@endsection