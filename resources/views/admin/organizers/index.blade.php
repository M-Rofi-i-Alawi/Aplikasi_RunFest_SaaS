@extends('layouts.app')

@section('title', 'Kelola Organizer — SuperAdmin RunFest')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-brand-navy pb-16">
    <!-- Header -->
    <div class="bg-gradient-to-r from-brand-navy via-brand-navy-light to-brand-navy text-white py-10 px-4 sm:px-6 lg:px-8 border-b border-white/10">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <div class="flex items-center gap-2.5 mb-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold tracking-wider uppercase bg-brand-orange/20 text-brand-orange border border-brand-orange/30">
                        SuperAdmin Control
                    </span>
                    <span class="text-xs text-slate-400">SaaS Multi-Tenant Management</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-black italic uppercase tracking-tight text-white flex items-center gap-3">
                    <span>👥 Kelola Organizer</span>
                </h1>
                <p class="text-slate-300 mt-1.5 text-sm sm:text-base max-w-2xl">
                    Pantau, verifikasi status, dan tambahkan akun Event Organizer (EO) baru untuk penyelenggaraan event lari.
                </p>
            </div>

            <!-- Action Button -->
            <div class="flex items-center gap-3">
                <button type="button" onclick="openOrganizerModal()"
                        class="btn-brand-orange text-white px-5 py-3 rounded-xl text-sm font-extrabold flex items-center gap-2.5 shadow-lg hover:shadow-orange-500/25 transition-all">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Tambah Organizer Baru</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Admin Nav Tabs -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-[60px] sm:top-[68px] z-20 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex gap-2 overflow-x-auto py-2.5 scrollbar-none">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 whitespace-nowrap transition-colors">
                    📊 Dashboard
                </a>
                <a href="{{ route('admin.organizers') }}"
                   class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold bg-brand-orange text-white shadow-md shadow-orange-500/20 whitespace-nowrap">
                    👥 Organizer
                </a>
                <a href="{{ route('admin.events') }}"
                   class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 whitespace-nowrap transition-colors">
                    🏃 Moderasi Event
                </a>
                <a href="{{ route('admin.marshals') }}"
                   class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 whitespace-nowrap transition-colors">
                    📱 Tim Marshal
                </a>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">

        <!-- Stat Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
            <div class="glass-card p-4 sm:p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total EO</p>
                        <p class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mt-1">
                            {{ $stats['total'] ?? $organizers->total() }}
                        </p>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-xl">
                        👥
                    </div>
                </div>
            </div>

            <div class="glass-card p-4 sm:p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">EO Aktif</p>
                        <p class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-1">
                            {{ $stats['aktif'] ?? 0 }}
                        </p>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-xl">
                        ✓
                    </div>
                </div>
            </div>

            <div class="glass-card p-4 sm:p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Pending</p>
                        <p class="text-2xl sm:text-3xl font-black text-amber-600 dark:text-amber-400 mt-1">
                            {{ $stats['pending'] ?? 0 }}
                        </p>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-xl">
                        ⏳
                    </div>
                </div>
            </div>

            <div class="glass-card p-4 sm:p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider">Diblokir</p>
                        <p class="text-2xl sm:text-3xl font-black text-rose-600 dark:text-rose-400 mt-1">
                            {{ $stats['diblokir'] ?? 0 }}
                        </p>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold text-xl">
                        🚫
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="glass-card p-4 sm:p-5 mb-6">
            <form method="GET" action="{{ route('admin.organizers') }}" class="flex flex-col md:flex-row gap-3">
                <div class="flex-1 relative">
                    <span class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari nama, email, atau no. HP organizer..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm focus:ring-2 focus:ring-brand-orange focus:border-transparent outline-none">
                </div>

                <div class="w-full md:w-48">
                    <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm focus:ring-2 focus:ring-brand-orange outline-none">
                        <option value="">Semua Status</option>
                        <option value="Aktif" @selected(request('status') === 'Aktif')>🟢 Aktif</option>
                        <option value="Pending" @selected(request('status') === 'Pending')>🟡 Pending</option>
                        <option value="Diblokir" @selected(request('status') === 'Diblokir')>🔴 Diblokir</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="btn-brand-orange text-white px-5 py-2.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2">
                        <span>Filter</span>
                    </button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('admin.organizers') }}"
                           class="px-4 py-2.5 rounded-xl text-sm font-semibold border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Organizer Table -->
        <div class="glass-card overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h2 class="font-extrabold text-gray-900 dark:text-white text-base">Daftar Akun Event Organizer</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                        {{ $organizers->total() }} EO Terdaftar
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50/75 dark:bg-gray-800/60 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <th class="px-5 py-3.5 text-center w-12">No</th>
                            <th class="px-6 py-3.5">Nama EO</th>
                            <th class="px-6 py-3.5">Email</th>
                            <th class="px-6 py-3.5">No. HP / WA</th>
                            <th class="px-6 py-3.5 text-center">Jumlah Event</th>
                            <th class="px-6 py-3.5">Tanggal Bergabung</th>
                            <th class="px-6 py-3.5 text-center">Status Akun</th>
                            <th class="px-6 py-3.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($organizers as $index => $eo)
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors">
                                <!-- No -->
                                <td class="px-5 py-4 text-center font-bold text-gray-400 text-xs">
                                    {{ $organizers->firstItem() ? ($organizers->firstItem() + $index) : ($index + 1) }}
                                </td>

                                <!-- Nama EO -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-brand-orange to-orange-400 flex items-center justify-center text-white font-black text-sm flex-shrink-0 shadow-sm">
                                            {{ strtoupper(substr($eo->nama, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 dark:text-white leading-snug">{{ $eo->nama }}</p>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[10px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300">
                                                    Organizer
                                                </span>
                                                <span class="text-xs text-gray-400">ID #{{ $eo->id_user }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Email -->
                                <td class="px-6 py-4">
                                    <a href="mailto:{{ $eo->email }}" class="font-medium text-gray-700 dark:text-gray-300 hover:text-brand-orange dark:hover:text-brand-orange transition-colors flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                        </svg>
                                        <span>{{ $eo->email }}</span>
                                    </a>
                                </td>

                                <!-- No. HP / WhatsApp -->
                                <td class="px-6 py-4">
                                    @if($eo->no_hp)
                                        @php
                                            $cleanHp = preg_replace('/[^0-9]/', '', $eo->no_hp);
                                            if (str_starts_with($cleanHp, '0')) {
                                                $waHp = '62' . substr($cleanHp, 1);
                                            } else {
                                                $waHp = $cleanHp;
                                            }
                                        @endphp
                                        <a href="https://wa.me/{{ $waHp }}" target="_blank" rel="noopener noreferrer"
                                           class="inline-flex items-center gap-1.5 font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                            </svg>
                                            <span>{{ $eo->no_hp }}</span>
                                        </a>
                                    @else
                                        <span class="text-gray-400 italic text-xs">- Belum Diisi -</span>
                                    @endif
                                </td>

                                <!-- Jumlah Event Dibuat -->
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200">
                                        🏃 {{ $eo->event_lari_count }} Event
                                    </span>
                                </td>

                                <!-- Tanggal Bergabung -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="font-medium text-gray-800 dark:text-gray-200">{{ $eo->created_at->isoFormat('D MMMM YYYY') }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $eo->created_at->diffForHumans() }}</p>
                                </td>

                                <!-- Status Akun -->
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $badgeStyle = match($eo->status_akun) {
                                            'Aktif'    => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-900/40 dark:text-emerald-300 dark:border-emerald-700',
                                            'Diblokir' => 'bg-rose-100 text-rose-800 border-rose-200 dark:bg-rose-900/40 dark:text-rose-300 dark:border-rose-700',
                                            default    => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-900/40 dark:text-amber-300 dark:border-amber-700',
                                        };
                                        $statusIcon = match($eo->status_akun) {
                                            'Aktif'    => '● Aktif',
                                            'Diblokir' => '✕ Diblokir',
                                            default    => '⏳ Pending',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $badgeStyle }}">
                                        {{ $statusIcon }}
                                    </span>
                                </td>

                                <!-- Aksi -->
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        @if($eo->status_akun !== 'Aktif')
                                            <form action="{{ route('admin.organizers.update-status', $eo->id_user) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status_akun" value="Aktif">
                                                <button type="submit"
                                                        class="text-xs bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg font-bold transition-all shadow-sm flex items-center gap-1"
                                                        title="Aktifkan Akun Organizer">
                                                    <span>✓ Aktifkan</span>
                                                </button>
                                            </form>
                                        @endif

                                        @if($eo->status_akun !== 'Pending' && $eo->status_akun !== 'Diblokir')
                                            <form action="{{ route('admin.organizers.update-status', $eo->id_user) }}" method="POST"
                                                  onsubmit="return confirm('Kembalikan status akun {{ $eo->nama }} ke Pending?')">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status_akun" value="Pending">
                                                <button type="submit"
                                                        class="text-xs bg-amber-500 hover:bg-amber-600 text-white px-2.5 py-1.5 rounded-lg font-bold transition-all shadow-sm"
                                                        title="Kembalikan ke Status Pending">
                                                    <span>Pending</span>
                                                </button>
                                            </form>
                                        @endif

                                        @if($eo->status_akun !== 'Diblokir')
                                            <form action="{{ route('admin.organizers.update-status', $eo->id_user) }}" method="POST"
                                                  onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin memblokir akun organizer {{ $eo->nama }}? User tidak akan dapat login.')">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status_akun" value="Diblokir">
                                                <button type="submit"
                                                        class="text-xs bg-rose-500 hover:bg-rose-600 text-white px-2.5 py-1.5 rounded-lg font-bold transition-all shadow-sm"
                                                        title="Blokir Akun Organizer">
                                                    <span>Blokir</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center text-gray-400">
                                    <div class="max-w-xs mx-auto text-center">
                                        <div class="w-16 h-16 mx-auto mb-4 rounded-3xl bg-orange-100 dark:bg-orange-950/40 text-brand-orange flex items-center justify-center text-2xl font-black">
                                            👥
                                        </div>
                                        <p class="font-extrabold text-gray-800 dark:text-gray-200 text-base">Tidak ada organizer ditemukan</p>
                                        <p class="text-xs text-gray-400 mt-1 mb-4">Coba ubah kata kunci pencarian atau daftarkan organizer baru sekarang.</p>
                                        <button type="button" onclick="openOrganizerModal()"
                                                class="btn-brand-orange text-white px-4 py-2 rounded-xl text-xs font-extrabold">
                                            + Tambah Organizer Baru
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($organizers->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/30">
                    {{ $organizers->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- ======================================================================== -->
<!-- MODAL: TAMBAH AKUN ORGANIZER BARU -->
<!-- ======================================================================== -->
<div id="add-organizer-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <!-- Backdrop Blur Overlay -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeOrganizerModal()"></div>

    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative w-full max-w-lg rounded-3xl bg-white dark:bg-brand-navy p-6 sm:p-8 shadow-2xl border border-gray-100 dark:border-white/10 z-10 transition-all transform scale-100">
            <!-- Modal Header -->
            <div class="flex items-start justify-between pb-4 border-b border-gray-100 dark:border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-brand-orange to-orange-400 flex items-center justify-center text-white text-xl font-bold shadow-md shadow-orange-500/20">
                        👥
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">
                            Tambah Organizer Baru
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">
                            Daftarkan akun EO resmi dengan status <strong class="text-emerald-600 dark:text-emerald-400">Langsung Aktif</strong>.
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeOrganizerModal()"
                        class="p-1.5 rounded-xl text-gray-400 hover:text-gray-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Error Notification in Modal -->
            @if(isset($errors) && $errors->any())
                <div class="mt-4 p-3.5 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs">
                    <p class="font-bold mb-1 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        Periksa kembali isian form berikut:
                    </p>
                    <ul class="list-disc list-inside space-y-0.5 text-[11px] text-rose-700 dark:text-rose-300">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Modal Form -->
            <form action="{{ route('admin.organizers.store') }}" method="POST" class="mt-5 space-y-4">
                @csrf

                <!-- Nama Organizer -->
                <div>
                    <label for="nama" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-slate-300 mb-1.5">
                        Nama Organizer / Komunitas / Perusahaan <span class="text-brand-orange">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none text-gray-400">
                            🏢
                        </span>
                        <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required maxlength="255"
                               placeholder="cth. Runner Mania Organizer"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-orange focus:border-transparent outline-none">
                    </div>
                </div>

                <!-- Email Organizer -->
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-slate-300 mb-1.5">
                        Alamat Email Resmi <span class="text-brand-orange">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none text-gray-400">
                            ✉️
                        </span>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required maxlength="255"
                               placeholder="organizer@runfest.id"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-orange focus:border-transparent outline-none">
                    </div>
                    <p class="text-[11px] text-gray-400 mt-1">Digunakan sebagai username saat login ke portal Organizer.</p>
                </div>

                <!-- No HP / WhatsApp -->
                <div>
                    <label for="no_hp" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-slate-300 mb-1.5">
                        Nomor HP / WhatsApp <span class="text-brand-orange">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none text-gray-400">
                            📱
                        </span>
                        <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" required maxlength="20"
                               placeholder="081234567890"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-orange focus:border-transparent outline-none">
                    </div>
                    <p class="text-[11px] text-gray-400 mt-1">Kontak resmi narahubung event organizer.</p>
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-slate-300">
                            Password Awal Akun <span class="text-brand-orange">*</span>
                        </label>
                        <button type="button" onclick="generateRandomPassword()"
                                class="text-[11px] font-bold text-brand-orange hover:underline">
                            ⚡ Acak Password
                        </button>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none text-gray-400">
                            🔒
                        </span>
                        <input type="password" id="password" name="password" required minlength="8"
                               placeholder="Minimal 8 karakter..."
                               class="w-full pl-10 pr-11 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-orange focus:border-transparent outline-none">
                        <button type="button" onclick="togglePasswordVisibility()"
                                class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 text-xs font-bold">
                            <span id="eye-icon">👁️</span>
                        </button>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-1">Minimal 8 karakter. Organizer dapat mengganti password melalui halaman akun.</p>
                </div>

                <!-- Info Box -->
                <div class="p-3.5 rounded-2xl bg-brand-orange/10 border border-brand-orange/20 text-xs text-brand-navy dark:text-slate-200 flex items-start gap-2.5">
                    <span class="text-base leading-none">💡</span>
                    <p class="leading-relaxed text-[11px]">
                        Akun yang dibuat oleh SuperAdmin akan langsung diberi peran <strong>Event Organizer (EO)</strong> dengan status akun <strong>Aktif</strong> tanpa perlu proses moderasi manual.
                    </p>
                </div>

                <!-- Footer Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-white/10">
                    <button type="button" onclick="closeOrganizerModal()"
                            class="px-5 py-2.5 rounded-xl text-sm font-bold text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="btn-brand-orange text-white px-6 py-2.5 rounded-xl text-sm font-extrabold shadow-lg hover:shadow-orange-500/25 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <span>Simpan & Daftarkan EO</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openOrganizerModal() {
        const modal = document.getElementById('add-organizer-modal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
                const namaInput = document.getElementById('nama');
                if (namaInput) namaInput.focus();
            }, 100);
        }
    }

    function closeOrganizerModal() {
        const modal = document.getElementById('add-organizer-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }

    function togglePasswordVisibility() {
        const input = document.getElementById('password');
        const icon = document.getElementById('eye-icon');
        if (input) {
            if (input.type === 'password') {
                input.type = 'text';
                if (icon) icon.innerText = '🙈';
            } else {
                input.type = 'password';
                if (icon) icon.innerText = '👁️';
            }
        }
    }

    function generateRandomPassword() {
        const chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$';
        let pass = '';
        for (let i = 0; i < 12; i++) {
            pass += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        const input = document.getElementById('password');
        if (input) {
            input.type = 'text';
            input.value = pass;
            const icon = document.getElementById('eye-icon');
            if (icon) icon.innerText = '🙈';
        }
    }

    // Close on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeOrganizerModal();
        }
    });

    // Automatically open modal if validation errors exist on submit
    @if(isset($errors) && $errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            openOrganizerModal();
        });
    @endif
</script>
@endpush
@endsection
