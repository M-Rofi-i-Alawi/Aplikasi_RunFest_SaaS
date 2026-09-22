@extends('layouts.app')

@section('title', 'Dashboard Tiket Runner — RunFest SaaS')

@push('styles')
<style>
    /* ---- BIB BOARDING PASS ---- */
    .boarding-pass {
        background: #fff;
        border-radius: 1.25rem;
        overflow: hidden;
        box-shadow: 0 4px 30px -8px rgba(0,0,0,0.12);
        border: 1px solid #e2e8f0;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .dark .boarding-pass {
        background: rgba(15, 33, 55, 0.85);
        border-color: rgba(255,255,255,0.1);
        box-shadow: 0 8px 40px -10px rgba(0,0,0,0.5);
    }
    .boarding-pass-history {
        border-color: #cbd5e1;
    }
    .dark .boarding-pass-history {
        background: rgba(13, 26, 44, 0.85);
        border-color: rgba(255,255,255,0.07);
    }
    .perforated-divider {
        border-left: 2px dashed #cbd5e1;
        position: relative;
    }
    .dark .perforated-divider { border-color: rgba(255,255,255,0.12); }
    .perforated-divider::before,
    .perforated-divider::after {
        content: '';
        display: block;
        width: 18px; height: 18px;
        background: #f8fafc;
        border-radius: 50%;
        position: absolute;
        left: -10px;
    }
    .dark .perforated-divider::before,
    .dark .perforated-divider::after { background: #0a1825; }
    .perforated-divider::before { top: -9px; }
    .perforated-divider::after  { bottom: -9px; }
    .bib-number {
        font-family: 'Plus Jakarta Sans', 'Courier New', monospace;
        font-weight: 900;
        font-style: italic;
        letter-spacing: -0.02em;
        color: #F05423;
        line-height: 1;
    }
    .bib-number-history {
        color: #64748b;
    }
    .dark .bib-number-history {
        color: #94a3b8;
    }
    .ticket-header-stripe {
        background: linear-gradient(135deg, #0F2137 0%, #1A3350 60%, #0F2137 100%);
    }
    .ticket-header-stripe-history {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    }
    /* Ticket for non-Lunas: keep original glass-card style */
    .ticket-pending { transition: all .3s ease; }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-3.5 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6 sm:space-y-8">

    {{-- ===== HEADER ===== --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-white/10 pb-5 sm:pb-6">
        <div>
            <span class="inline-block px-3 py-1 text-[10px] font-black uppercase tracking-widest text-[#F05423] bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/30 rounded-xl mb-1.5 sm:mb-2">
                🎫 Runner Dashboard
            </span>
            <h1 class="text-2xl sm:text-3xl font-black italic uppercase tracking-tight text-slate-900 dark:text-white">
                Tiket & Racepack Saya
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Selamat datang, <span class="font-bold text-slate-900 dark:text-white">{{ auth()->user()->nama }}</span>
            </p>
        </div>
        <a href="{{ route('events.index') }}"
           class="inline-flex items-center gap-2 justify-center px-5 py-2.5 rounded-xl btn-brand-orange text-white font-extrabold text-xs sm:text-sm shadow-md transition-all self-start md:self-auto">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Daftar Event Baru
        </a>
    </div>

    {{-- ===== WARNING IDENTITAS ===== --}}
    @if(auth()->user()->isRunner() && (empty(auth()->user()->nik) || strlen(auth()->user()->nik) !== 16 || empty(auth()->user()->foto_identitas) || !auth()->user()->google_id))
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-950/40 dark:to-orange-950/30 border-2 border-amber-300 dark:border-amber-500/40 rounded-3xl p-5 sm:p-6 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 sm:gap-5">
            <div class="flex items-start gap-3 sm:gap-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center font-black text-lg sm:text-xl shrink-0 shadow-lg shadow-amber-500/30">⚠️</div>
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                        <h3 class="text-xs sm:text-sm font-black text-amber-950 dark:text-amber-200 uppercase tracking-tight">Verifikasi Identitas Diperlukan</h3>
                        @if(!auth()->user()->google_id)
                            <span class="px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-black uppercase bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300 border border-rose-200 dark:border-rose-500/30">Akun Manual</span>
                        @endif
                        @if(empty(auth()->user()->nik) || empty(auth()->user()->foto_identitas))
                            <span class="px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-black uppercase bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30">KTP Belum Diunggah</span>
                        @endif
                    </div>
                    <p class="text-xs text-amber-900/80 dark:text-amber-300/90 leading-relaxed max-w-2xl">
                        Untuk mencegah <strong>akun fiktif & joki</strong>, Anda wajib mengisi NIK 16 digit dan mengunggah Foto KTP/Kartu Pelajar Asli sebelum pengambilan Racepack.
                    </p>
                </div>
            </div>
            <a href="{{ route('account.settings') }}" class="shrink-0 w-full sm:w-auto text-center px-5 py-2.5 sm:py-3 rounded-2xl bg-[#F05423] hover:bg-[#D4461A] text-white font-black text-xs uppercase tracking-wider transition-all shadow-md">
                Lengkapi NIK & KTP →
            </a>
        </div>
    @endif

    {{-- ===== STATS CARDS (COMPACT RESPONSIVE 3-COL) ===== --}}
    <div class="grid grid-cols-3 gap-2.5 sm:gap-4">
        {{-- Total Tiket --}}
        <div class="glass-card rounded-2xl p-3 sm:p-5 flex flex-col sm:flex-row items-center sm:items-center gap-2 sm:gap-4 text-center sm:text-left">
            <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl bg-slate-100 dark:bg-white/10 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-slate-500 dark:text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z"/></svg>
            </div>
            <div class="min-w-0">
                <span class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-wide block truncate">Total Tiket</span>
                <span class="text-xl sm:text-3xl font-black text-slate-800 dark:text-white leading-none mt-0.5 block">{{ $pendaftaran->whereNotIn('status_pembayaran', ['Gagal'])->count() }}</span>
            </div>
        </div>

        {{-- Tiket Aktif --}}
        <div class="glass-card rounded-2xl p-3 sm:p-5 flex flex-col sm:flex-row items-center sm:items-center gap-2 sm:gap-4 text-center sm:text-left">
            <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl bg-orange-50 dark:bg-orange-500/15 flex items-center justify-center shrink-0">
                <span class="text-base sm:text-xl">🏃‍♂️</span>
            </div>
            <div class="min-w-0">
                <span class="text-[9px] sm:text-[10px] font-bold text-[#F05423] uppercase tracking-wide block truncate">Tiket Aktif</span>
                <span class="text-xl sm:text-3xl font-black text-[#F05423] leading-none mt-0.5 block">{{ $activeTickets->count() }}</span>
            </div>
        </div>

        {{-- Riwayat Selesai --}}
        <div class="glass-card rounded-2xl p-3 sm:p-5 flex flex-col sm:flex-row items-center sm:items-center gap-2 sm:gap-4 text-center sm:text-left">
            <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl bg-blue-50 dark:bg-blue-500/15 flex items-center justify-center shrink-0">
                <span class="text-base sm:text-xl">🏁</span>
            </div>
            <div class="min-w-0">
                <span class="text-[9px] sm:text-[10px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wide block truncate">Selesai</span>
                <span class="text-xl sm:text-3xl font-black text-blue-600 dark:text-blue-400 leading-none mt-0.5 block">{{ $historyTickets->count() }}</span>
            </div>
        </div>
    </div>

    {{-- ===== TAB NAVIGATION & TICKET LIST ===== --}}
    <div class="space-y-5 sm:space-y-6">

        {{-- Tab Buttons (Responsive Segmented Pill Control) --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-200 dark:border-white/10 pb-4">
            <div class="grid grid-cols-2 p-1 rounded-2xl bg-slate-100 dark:bg-[#0b1329] border border-slate-200 dark:border-white/10 w-full sm:w-auto sm:inline-flex gap-1">
                {{-- Tab 1: Tiket Aktif --}}
                <button type="button" id="tab-btn-active" onclick="switchTicketTab('active')"
                        class="tab-btn py-2.5 px-3 sm:px-6 rounded-xl text-xs sm:text-sm font-bold italic uppercase tracking-wider transition-all duration-200 flex items-center justify-center gap-1.5 sm:gap-2 bg-[#F05423] text-white shadow-md shadow-orange-500/20">
                    <span class="shrink-0">🏃‍♂️</span>
                    <span class="truncate">Tiket Aktif</span>
                    <span id="badge-count-active" class="px-1.5 py-0.5 rounded-md text-[10px] sm:text-xs font-black bg-white/20 text-white shrink-0">({{ $activeTickets->count() }})</span>
                </button>

                {{-- Tab 2: Riwayat Event Selesai --}}
                <button type="button" id="tab-btn-history" onclick="switchTicketTab('history')"
                        class="tab-btn py-2.5 px-3 sm:px-6 rounded-xl text-xs sm:text-sm font-bold italic uppercase tracking-wider transition-all duration-200 flex items-center justify-center gap-1.5 sm:gap-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white border border-transparent">
                    <span class="shrink-0">🏁</span>
                    <span class="truncate">Riwayat Selesai</span>
                    <span id="badge-count-history" class="px-1.5 py-0.5 rounded-md text-[10px] sm:text-xs font-black bg-slate-200 dark:bg-white/10 text-slate-700 dark:text-slate-300 shrink-0">({{ $historyTickets->count() }})</span>
                </button>
            </div>

            <div class="hidden sm:block text-xs text-slate-400 dark:text-slate-500 font-medium text-right">
                Sistem Tiket Digital & Arsip Rekam Jejak Pelari
            </div>
        </div>

        {{-- =========================================================================
             TAB 1 CONTENT: TIKET AKTIF
             ========================================================================= --}}
        <div id="tab-content-active" class="space-y-6">
            @if($activeTickets->count() > 0)
                @foreach($activeTickets as $daftar)

                    @if($daftar->status_pembayaran === 'Lunas')
                        {{-- ===================================================
                             BOARDING PASS TICKET (Aktif & Lunas)
                             =================================================== --}}
                        <div class="boarding-pass overflow-x-hidden">

                            {{-- ---- TOP STRIPE: Event Name & Badges ---- --}}
                            <div class="ticket-header-stripe px-4 sm:px-6 py-3.5 sm:py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-white">
                                <div class="flex items-center gap-3 min-w-0">
                                    <img src="{{ asset('images/logo-icon.png') }}" alt="RunFest" class="h-6 sm:h-7 w-auto object-contain brightness-[5] grayscale opacity-80 shrink-0">
                                    <div class="min-w-0">
                                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 block">RunFest SaaS — E-Ticket Aktif</span>
                                        <h3 class="text-sm sm:text-base font-black text-white leading-tight break-words sm:truncate">{{ $daftar->event->nama_event }}</h3>
                                    </div>
                                </div>
                                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 shrink-0">
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] sm:text-xs font-black uppercase tracking-wider bg-emerald-500 text-white shadow-md shadow-emerald-500/30">
                                        ✓ LUNAS
                                    </span>
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] sm:text-xs font-black uppercase tracking-wider border
                                        {{ $daftar->status_racepack === 'Sudah Diambil' ? 'bg-[#F05423] border-[#F05423] text-white' : 'bg-slate-700 border-slate-600 text-slate-300' }}">
                                        RP: {{ $daftar->status_racepack }}
                                    </span>
                                </div>
                            </div>

                            {{-- ---- MAIN BODY: 2 Panel with Perforated Divider ---- --}}
                            <div class="flex flex-col md:flex-row">

                                {{-- LEFT PANEL — Main Ticket --}}
                                <div class="flex-1 p-4 sm:p-6 space-y-4 sm:space-y-5 min-w-0">

                                    {{-- BIB Number --}}
                                    <div>
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-black uppercase tracking-widest block mb-1">Nomor BIB Peserta</span>
                                        @if(!empty($daftar->bib_number))
                                            <div class="bib-number text-4xl sm:text-6xl">{{ $daftar->bib_number }}</div>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs sm:text-sm font-extrabold bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30">
                                                <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                Diterbitkan Setelah Lunas
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Participant Details Grid (Balanced & No Truncation) --}}
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 sm:gap-3">
                                        {{-- 1. Nama Peserta (Full width on mobile, 2 cols on desktop) --}}
                                        <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-3 border border-slate-100 dark:border-white/8 col-span-2 sm:col-span-2">
                                            <span class="text-[9px] text-slate-400 font-black uppercase tracking-wide block mb-0.5">Nama Peserta</span>
                                            <span class="text-sm sm:text-base font-extrabold text-slate-900 dark:text-white break-words block leading-snug">{{ auth()->user()->nama }}</span>
                                        </div>

                                        {{-- 2. Kategori --}}
                                        <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-3 border border-slate-100 dark:border-white/8 col-span-1">
                                            <span class="text-[9px] text-slate-400 font-black uppercase tracking-wide block mb-0.5">Kategori</span>
                                            <span class="text-sm font-extrabold text-[#F05423] truncate block">{{ $daftar->kategori->nama_kategori }}</span>
                                        </div>

                                        {{-- 3. Tanggal Lomba --}}
                                        <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-3 border border-slate-100 dark:border-white/8 col-span-1">
                                            <span class="text-[9px] text-slate-400 font-black uppercase tracking-wide block mb-0.5">Tanggal Lomba</span>
                                            <span class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white block">{{ $daftar->event->tanggal_event->format('d M Y') }}</span>
                                        </div>

                                        {{-- 4. Ukuran Jersey --}}
                                        <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-3 border border-slate-100 dark:border-white/8 col-span-1">
                                            <span class="text-[9px] text-slate-400 font-black uppercase tracking-wide block mb-0.5">Ukuran Jersey</span>
                                            <span class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-wider">{{ $daftar->ukuran_jersey }}</span>
                                        </div>

                                        {{-- 5. Golongan Darah --}}
                                        <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-3 border border-slate-100 dark:border-white/8 col-span-1">
                                            <span class="text-[9px] text-slate-400 font-black uppercase tracking-wide block mb-0.5">Gol. Darah</span>
                                            <span class="text-base font-black text-slate-900 dark:text-white">{{ $daftar->runner->golongan_darah ?? auth()->user()->golongan_darah ?? '-' }}</span>
                                        </div>
                                    </div>

                                    {{-- Location --}}
                                    <div class="flex items-start gap-2 text-xs sm:text-sm pt-1">
                                        <span class="text-sm sm:text-base shrink-0 mt-0.5">📍</span>
                                        <div>
                                            <span class="text-[9px] text-slate-400 font-black uppercase tracking-wide block">Venue</span>
                                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $daftar->event->lokasi_venue }}</span>
                                            <a href="{{ $daftar->event->maps_url }}" target="_blank" rel="noopener noreferrer"
                                               class="inline-flex items-center gap-1 text-[11px] text-[#F05423] hover:underline font-bold mt-0.5 block">
                                                🗺️ Buka Google Maps
                                            </a>
                                        </div>
                                    </div>

                                    {{-- Checklist RPC --}}
                                    @if($daftar->status_racepack === 'Belum Diambil')
                                        <div class="border-t border-dashed border-slate-200 dark:border-white/10 pt-3.5">
                                            <p class="text-[10px] font-black text-slate-600 dark:text-slate-300 uppercase tracking-widest mb-2">📋 Checklist Pengambilan Racepack</p>
                                            <ul class="space-y-1.5 text-xs text-slate-600 dark:text-slate-400 font-medium">
                                                <li class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                    Bawa KTP / KIA / Kartu Pelajar Asli
                                                </li>
                                                <li class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                    Tunjukkan QR Code E-Ticket
                                                </li>
                                                <li class="flex items-center gap-2 text-amber-600 dark:text-amber-400">
                                                    <svg class="w-4 h-4 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                                    Jika diwakilkan: Surat Kuasa + KTP Perwakilan
                                                </li>
                                            </ul>
                                        </div>
                                    @endif

                                    {{-- Payment Info --}}
                                    @if($daftar->pembayaran)
                                        <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500 dark:text-slate-400 font-medium pt-1">
                                            <span>TRX: <strong class="font-mono text-slate-700 dark:text-slate-200">{{ $daftar->pembayaran->kode_transaksi }}</strong></span>
                                            <span>Via: <strong>{{ $daftar->pembayaran->metode_pembayaran }}</strong></span>
                                            <span>Total: <strong class="text-[#F05423]">Rp {{ number_format($daftar->pembayaran->total_bayar, 0, ',', '.') }}</strong></span>
                                        </div>
                                        @if($daftar->pembayaran->biaya_layanan > 0)
                                            <div class="flex flex-wrap gap-x-3 gap-y-0.5 text-[11px] text-slate-400 dark:text-slate-500 font-medium mt-0.5">
                                                <span>Tiket: Rp {{ number_format($daftar->pembayaran->harga_tiket, 0, ',', '.') }}</span>
                                                <span>+</span>
                                                <span>Biaya Layanan: Rp {{ number_format($daftar->pembayaran->biaya_layanan, 0, ',', '.') }}</span>
                                            </div>
                                        @endif
                                    @endif

                                    {{-- Action Buttons --}}
                                    <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-2 sm:gap-2.5 border-t border-slate-100 dark:border-white/10 pt-4">
                                        <a href="{{ route('runner.ticket.invoice', $daftar->id_pendaftaran) }}" target="_blank"
                                           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-500/15 dark:hover:bg-emerald-500/25 text-emerald-700 dark:text-emerald-300 font-extrabold text-xs uppercase tracking-wider transition-colors border border-emerald-200 dark:border-emerald-500/30">
                                            🖨️ Cetak Invoice
                                        </a>
                                        @if($daftar->google_calendar_race_day_url)
                                            <a href="{{ $daftar->google_calendar_race_day_url }}" target="_blank" rel="noopener noreferrer"
                                               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 dark:bg-blue-500/15 dark:hover:bg-blue-500/25 text-blue-700 dark:text-blue-300 font-extrabold text-xs uppercase tracking-wider transition-colors border border-blue-200 dark:border-blue-500/30">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 002 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zM5 8V6h14v2H5zm2 4h5v5H7v-5z"/></svg>
                                                Simpan Hari Lomba
                                            </a>
                                        @endif
                                        @if($daftar->google_calendar_rpc_url)
                                            <a href="{{ $daftar->google_calendar_rpc_url }}" target="_blank" rel="noopener noreferrer"
                                               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-violet-50 hover:bg-violet-100 dark:bg-violet-500/15 dark:hover:bg-violet-500/25 text-violet-700 dark:text-violet-300 font-extrabold text-xs uppercase tracking-wider transition-colors border border-violet-200 dark:border-violet-500/30">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 002 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zM5 8V6h14v2H5zm2 4h5v5H7v-5z"/></svg>
                                                Simpan Jadwal RPC
                                            </a>
                                        @endif
                                        {{-- Hubungi Panitia CP (Lunas) --}}
                                        @php
                                            $rawWaLunas  = !empty($daftar->event->no_wa_cp) ? $daftar->event->no_wa_cp : (!empty($daftar->event->organizer->no_hp) ? $daftar->event->organizer->no_hp : config('services.admin_wa', env('ADMIN_WA', '6287812822400')));
                                            $cleanWaLunas = preg_replace('/\D/', '', (string)$rawWaLunas);
                                            if (str_starts_with($cleanWaLunas, '0')) {
                                                $cleanWaLunas = '62' . substr($cleanWaLunas, 1);
                                            }
                                            $cpMsgLunas = urlencode('Halo Panitia ' . $daftar->event->nama_event . ', saya ' . auth()->user()->nama . ' (No. BIB: ' . ($daftar->bib_number ?? '-') . ') ingin menanyakan seputar teknis event lomba.');
                                        @endphp
                                        @if($cleanWaLunas)
                                            <a href="https://wa.me/{{ $cleanWaLunas }}?text={{ $cpMsgLunas }}" target="_blank" rel="noopener noreferrer"
                                               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-green-50 hover:bg-green-100 dark:bg-green-500/15 dark:hover:bg-green-500/25 text-green-700 dark:text-green-300 font-extrabold text-xs uppercase tracking-wider transition-colors border border-green-200 dark:border-green-500/30">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.114 1.524 5.843L.057 23.569a.75.75 0 00.974.974l5.726-1.467A11.952 11.952 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.891 0-3.667-.522-5.179-1.428l-.371-.22-3.838.983.999-3.712-.242-.384A9.953 9.953 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                                                💬 Hubungi Panitia
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                {{-- PERFORATED DIVIDER (DESKTOP & REALISTIC MOBILE TEAR SLIP) --}}
                                <div class="hidden md:block perforated-divider self-stretch w-0 my-4"></div>
                                <div class="md:hidden relative my-2">
                                    <div class="absolute -left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-slate-100 dark:bg-[#0a1825]"></div>
                                    <div class="border-t-2 border-dashed border-slate-200 dark:border-white/10 mx-5"></div>
                                    <div class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-slate-100 dark:bg-[#0a1825]"></div>
                                    <div class="absolute left-1/2 -translate-x-1/2 top-1/2 -translate-y-1/2 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-[9px] font-black uppercase px-2.5 py-0.5 rounded-full border border-slate-200 dark:border-white/10">
                                        ✂ E-Ticket
                                    </div>
                                </div>

                                {{-- RIGHT PANEL — High-Contrast QR + Verification --}}
                                <div class="md:w-52 xl:w-56 p-5 sm:p-6 flex flex-col items-center justify-center text-center gap-3 sm:gap-4 bg-slate-50/70 dark:bg-black/20 shrink-0">
                                    <div>
                                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 block mb-2 sm:mb-3">QR Code E-Ticket</span>
                                        <div class="qr-box data-qr-box bg-white p-2.5 sm:p-3 rounded-2xl border border-slate-200 shadow-md inline-block" data-preserve-white>
                                            {!! QrCode::size(130)->generate($daftar->qr_code_token) !!}
                                        </div>
                                    </div>

                                    {{-- Racepack Status --}}
                                    <div class="w-full">
                                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 block mb-1">Status Racepack</span>
                                        @if($daftar->status_racepack === 'Sudah Diambil')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-black bg-[#F05423] text-white shadow-md">
                                                ✓ Sudah Diambil
                                            </span>
                                            @if($daftar->waktu_pengambilan_racepack)
                                                <p class="text-[10px] text-slate-400 mt-1 font-mono">{{ $daftar->waktu_pengambilan_racepack->format('d M Y, H:i') }}</p>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-black bg-amber-100 dark:bg-amber-500/15 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30">
                                                ⏳ Belum Diambil
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Token --}}
                                    <p class="text-[9px] text-slate-400 dark:text-slate-500 font-mono break-all select-all leading-relaxed w-full max-w-[200px]">{{ $daftar->qr_code_token }}</p>

                                    {{-- Marshal instruction --}}
                                    <p class="text-[9px] text-slate-400 italic">Scan QR oleh petugas Marshal saat pengambilan Racepack</p>
                                </div>
                            </div>
                        </div>

                    @else
                        {{-- ===================================================
                             NON-LUNAS TICKET (Pending / Gagal) — simplified card
                             =================================================== --}}
                        <div class="glass-card rounded-3xl overflow-hidden ticket-pending border border-slate-200 dark:border-white/15 shadow-md">
                            {{-- Header Bar --}}
                            <div class="bg-slate-900 dark:bg-[#0b1329] px-4 sm:px-6 py-3.5 sm:py-4 text-white flex flex-wrap items-center justify-between gap-2.5 sm:gap-3">
                                <div class="min-w-0">
                                    <span class="text-[9px] sm:text-[10px] text-orange-400 font-extrabold uppercase tracking-wider block">Nama Event</span>
                                    <h3 class="text-base sm:text-lg font-black leading-tight break-words">{{ $daftar->event->nama_event }}</h3>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="px-2.5 sm:px-3 py-1 rounded-xl text-[10px] sm:text-xs font-black uppercase tracking-wider {{ $daftar->status_pembayaran === 'Pending' ? 'bg-amber-500' : 'bg-rose-500' }} text-white">
                                        {{ $daftar->status_pembayaran }}
                                    </span>
                                </div>
                            </div>

                            {{-- Body --}}
                            <div class="p-4 sm:p-6 space-y-4">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                    <div class="bg-slate-100 dark:bg-white/10 p-3 sm:p-4 rounded-2xl border border-slate-200 dark:border-white/15 text-center shrink-0">
                                        <span class="text-[9px] sm:text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-0.5">Nomor BIB</span>
                                        <span class="text-xl sm:text-2xl font-black text-slate-400 dark:text-slate-500 italic">—</span>
                                        <span class="text-[9px] sm:text-[10px] text-slate-400 block mt-0.5">Belum Lunas</span>
                                    </div>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 flex-1">
                                        <div class="bg-slate-50 dark:bg-white/5 p-2.5 rounded-xl border border-slate-100 dark:border-white/5">
                                            <span class="text-[9px] text-slate-400 font-bold uppercase block">Kategori</span>
                                            <span class="text-xs sm:text-sm font-extrabold text-slate-800 dark:text-slate-200 truncate block">{{ $daftar->kategori->nama_kategori }}</span>
                                        </div>
                                        <div class="bg-slate-50 dark:bg-white/5 p-2.5 rounded-xl border border-slate-100 dark:border-white/5">
                                            <span class="text-[9px] text-slate-400 font-bold uppercase block">Tanggal</span>
                                            <span class="text-xs sm:text-sm font-bold text-slate-700 dark:text-slate-300 block">{{ $daftar->event->tanggal_event->format('d M Y') }}</span>
                                        </div>
                                        <div class="bg-slate-50 dark:bg-white/5 p-2.5 rounded-xl border border-slate-100 dark:border-white/5 col-span-2 sm:col-span-1">
                                            <span class="text-[9px] text-slate-400 font-bold uppercase block">Jersey</span>
                                            <span class="text-sm sm:text-base font-extrabold text-[#F05423]">{{ $daftar->ukuran_jersey }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Pending Actions --}}
                                @if($daftar->status_pembayaran === 'Pending')
                                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 pt-2 border-t border-slate-100 dark:border-white/10">
                                        @if(!empty($daftar->pembayaran->snap_token))
                                            <button type="button"
                                                    id="pay-btn-{{ $daftar->id_pendaftaran }}"
                                                    onclick="bayarMidtrans('{{ $daftar->pembayaran->snap_token }}')"
                                                    class="pay-button px-5 py-2.5 rounded-xl bg-[#F05423] hover:bg-[#D4461A] text-white font-extrabold text-xs uppercase tracking-wider shadow-lg hover:shadow-orange-500/25 transition text-center">
                                                💳 Bayar Sekarang
                                            </button>
                                        @else
                                            <form method="POST" action="{{ route('runner.payment-token', $daftar->id_pendaftaran) }}" class="w-full sm:w-auto">
                                                @csrf
                                                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-[#F05423] hover:bg-[#D4461A] text-white font-extrabold text-xs uppercase tracking-wider shadow-lg transition text-center">
                                                    💳 Lanjut ke Pembayaran
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('runner.cancel-registration', $daftar->id_pendaftaran) }}"
                                              onsubmit="return confirm('Batalkan pendaftaran ini?')" class="w-full sm:w-auto">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-500/15 text-rose-700 dark:text-rose-300 font-extrabold text-xs uppercase border border-rose-200 dark:border-rose-500/30 transition text-center">
                                                ✕ Batalkan
                                            </button>
                                        </form>
                                        {{-- Hubungi Panitia CP (Pending) --}}
                                        @php
                                            $rawWaPending  = !empty($daftar->event->no_wa_cp) ? $daftar->event->no_wa_cp : (!empty($daftar->event->organizer->no_hp) ? $daftar->event->organizer->no_hp : config('services.admin_wa', env('ADMIN_WA', '6287812822400')));
                                            $cleanWaPending = preg_replace('/\D/', '', (string)$rawWaPending);
                                            if (str_starts_with($cleanWaPending, '0')) {
                                                $cleanWaPending = '62' . substr($cleanWaPending, 1);
                                            }
                                            $kodetrxPending = $daftar->pembayaran->kode_transaksi ?? '-';
                                            $cpMsgPending = urlencode('Halo Panitia ' . $daftar->event->nama_event . ', saya ' . auth()->user()->nama . ' (Kode Transaksi: ' . $kodetrxPending . ') mengalami kendala dalam pendaftaran tiket lomba.');
                                        @endphp
                                        @if($cleanWaPending)
                                            <a href="https://wa.me/{{ $cleanWaPending }}?text={{ $cpMsgPending }}" target="_blank" rel="noopener noreferrer"
                                               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-green-50 hover:bg-green-100 dark:bg-green-500/15 dark:hover:bg-green-500/25 text-green-700 dark:text-green-300 font-extrabold text-xs uppercase tracking-wider transition-colors border border-green-200 dark:border-green-500/30">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.114 1.524 5.843L.057 23.569a.75.75 0 00.974.974l5.726-1.467A11.952 11.952 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.891 0-3.667-.522-5.179-1.428l-.371-.22-3.838.983.999-3.712-.242-.384A9.953 9.953 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                                                💬 Hubungi Panitia
                                            </a>
                                        @endif
                                    </div>
                                @endif
                                @if($daftar->status_pembayaran === 'Gagal')
                                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 pt-2 border-t border-slate-100 dark:border-white/10">
                                        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 font-bold text-xs border border-rose-200 dark:border-rose-500/30">
                                            ✕ Pendaftaran Dibatalkan / Gagal
                                        </span>
                                        {{-- Hubungi Panitia CP (Gagal) --}}
                                        @php
                                            $rawWaGagal  = !empty($daftar->event->no_wa_cp) ? $daftar->event->no_wa_cp : (!empty($daftar->event->organizer->no_hp) ? $daftar->event->organizer->no_hp : config('services.admin_wa', env('ADMIN_WA', '6287812822400')));
                                            $cleanWaGagal = preg_replace('/\D/', '', (string)$rawWaGagal);
                                            if (str_starts_with($cleanWaGagal, '0')) {
                                                $cleanWaGagal = '62' . substr($cleanWaGagal, 1);
                                            }
                                            $kodetrxGagal = $daftar->pembayaran->kode_transaksi ?? '-';
                                            $cpMsgGagal = urlencode('Halo Panitia ' . $daftar->event->nama_event . ', saya ' . auth()->user()->nama . ' (Kode Transaksi: ' . $kodetrxGagal . ') mengalami kendala dalam pendaftaran tiket lomba.');
                                        @endphp
                                        @if($cleanWaGagal)
                                            <a href="https://wa.me/{{ $cleanWaGagal }}?text={{ $cpMsgGagal }}" target="_blank" rel="noopener noreferrer"
                                               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-green-50 hover:bg-green-100 dark:bg-green-500/15 dark:hover:bg-green-500/25 text-green-700 dark:text-green-300 font-extrabold text-xs uppercase tracking-wider transition-colors border border-green-200 dark:border-green-500/30">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.114 1.524 5.843L.057 23.569a.75.75 0 00.974.974l5.726-1.467A11.952 11.952 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.891 0-3.667-.522-5.179-1.428l-.371-.22-3.838.983.999-3.712-.242-.384A9.953 9.953 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                                                💬 Hubungi Panitia
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                @endforeach
            @else
                {{-- Empty State: Tiket Aktif --}}
                <div class="glass-card rounded-3xl p-8 sm:p-12 text-center border border-slate-200 dark:border-white/10 shadow-sm">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 mx-auto rounded-2xl bg-orange-50 dark:bg-orange-500/10 flex items-center justify-center text-2xl sm:text-3xl mb-3 sm:mb-4 shadow-inner">🏃‍♂️</div>
                    <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">Belum Ada Tiket Aktif</h3>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1.5 mb-5 max-w-md mx-auto">
                        Anda belum memiliki tiket untuk event lari mendatang. Daftarkan diri ke event lari pilihanmu sekarang!
                    </p>
                    <a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 sm:px-6 sm:py-3 rounded-xl btn-brand-orange text-white font-extrabold text-xs uppercase tracking-wider shadow-lg shadow-orange-500/25 transition">
                        Lihat Katalog Event →
                    </a>
                </div>
            @endif
        </div>

        {{-- =========================================================================
             TAB 2 CONTENT: RIWAYAT EVENT SELESAI
             ========================================================================= --}}
        <div id="tab-content-history" class="space-y-6 hidden">
            @if($historyTickets->count() > 0)
                @foreach($historyTickets as $daftar)
                    {{-- ===================================================
                         BOARDING PASS TICKET ARSIP (Riwayat Event Selesai)
                         =================================================== --}}
                    <div class="boarding-pass boarding-pass-history overflow-x-hidden">

                        {{-- ---- TOP STRIPE: Event Name & Archive Badges ---- --}}
                        <div class="ticket-header-stripe-history px-4 sm:px-6 py-3.5 sm:py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-white">
                            <div class="flex items-center gap-3 min-w-0">
                                <img src="{{ asset('images/logo-icon.png') }}" alt="RunFest" class="h-6 sm:h-7 w-auto object-contain brightness-[5] grayscale opacity-70 shrink-0">
                                <div class="min-w-0">
                                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 block">RunFest SaaS — E-Ticket Arsip</span>
                                    <h3 class="text-sm sm:text-base font-black text-slate-200 leading-tight break-words sm:truncate">{{ $daftar->event->nama_event }}</h3>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 shrink-0">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] sm:text-xs font-black uppercase tracking-wider bg-slate-700 text-slate-200 border border-slate-600 shadow-sm">
                                    🏁 Event Selesai
                                </span>
                                <span class="px-2.5 py-1 rounded-lg text-[10px] sm:text-xs font-black uppercase tracking-wider bg-blue-500/20 text-blue-300 border border-blue-500/40">
                                    ✓ LUNAS
                                </span>
                                <span class="px-2.5 py-1 rounded-lg text-[10px] sm:text-xs font-black uppercase tracking-wider border
                                    {{ $daftar->status_racepack === 'Sudah Diambil' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' : 'bg-slate-700 text-slate-400 border-slate-600' }}">
                                    RP: {{ $daftar->status_racepack === 'Sudah Diambil' ? 'Diambil' : 'Ditutup' }}
                                </span>
                            </div>
                        </div>

                        {{-- ---- MAIN BODY: 2 Panel with Perforated Divider ---- --}}
                        <div class="flex flex-col md:flex-row">

                            {{-- LEFT PANEL — Ticket Info & Finisher Record --}}
                            <div class="flex-1 p-4 sm:p-6 space-y-4 sm:space-y-5 min-w-0">

                                {{-- BIB Number (Rekam Jejak) --}}
                                <div>
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 font-black uppercase tracking-widest block mb-1">Nomor BIB Peserta (Rekam Jejak)</span>
                                    @if(!empty($daftar->bib_number))
                                        <div class="bib-number bib-number-history text-4xl sm:text-6xl">{{ $daftar->bib_number }}</div>
                                    @else
                                        <span class="text-2xl font-black text-slate-400 italic">—</span>
                                    @endif
                                </div>

                                {{-- Participant Details Grid (Balanced & No Truncation) --}}
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 sm:gap-3">
                                    {{-- 1. Nama Peserta (Full width on mobile, 2 cols on desktop) --}}
                                    <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-3 border border-slate-100 dark:border-white/8 col-span-2 sm:col-span-2">
                                        <span class="text-[9px] text-slate-400 font-black uppercase tracking-wide block mb-0.5">Nama Peserta</span>
                                        <span class="text-sm sm:text-base font-extrabold text-slate-900 dark:text-white break-words block leading-snug">{{ auth()->user()->nama }}</span>
                                    </div>

                                    {{-- 2. Kategori --}}
                                    <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-3 border border-slate-100 dark:border-white/8 col-span-1">
                                        <span class="text-[9px] text-slate-400 font-black uppercase tracking-wide block mb-0.5">Kategori</span>
                                        <span class="text-sm font-extrabold text-slate-700 dark:text-slate-200 truncate block">{{ $daftar->kategori->nama_kategori }}</span>
                                    </div>

                                    {{-- 3. Tanggal Pelaksanaan --}}
                                    <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-3 border border-slate-100 dark:border-white/8 col-span-1">
                                        <span class="text-[9px] text-slate-400 font-black uppercase tracking-wide block mb-0.5">Tanggal Pelaksanaan</span>
                                        <span class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white block">
                                            {{ $daftar->event->tanggal_event->format('d M Y') }}
                                        </span>
                                        <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 block">(Telah Selesai)</span>
                                    </div>

                                    {{-- 4. Ukuran Jersey --}}
                                    <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-3 border border-slate-100 dark:border-white/8 col-span-1">
                                        <span class="text-[9px] text-slate-400 font-black uppercase tracking-wide block mb-0.5">Ukuran Jersey</span>
                                        <span class="text-lg sm:text-xl font-black text-slate-800 dark:text-slate-200 tracking-wider">{{ $daftar->ukuran_jersey }}</span>
                                    </div>

                                    {{-- 5. Golongan Darah --}}
                                    <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-3 border border-slate-100 dark:border-white/8 col-span-1">
                                        <span class="text-[9px] text-slate-400 font-black uppercase tracking-wide block mb-0.5">Gol. Darah</span>
                                        <span class="text-base font-black text-slate-800 dark:text-slate-200">{{ $daftar->runner->golongan_darah ?? auth()->user()->golongan_darah ?? '-' }}</span>
                                    </div>
                                </div>

                                {{-- Location --}}
                                <div class="flex items-start gap-2 text-xs sm:text-sm pt-1">
                                    <span class="text-sm sm:text-base shrink-0 mt-0.5">📍</span>
                                    <div>
                                        <span class="text-[9px] text-slate-400 font-black uppercase tracking-wide block">Venue</span>
                                        <span class="font-bold text-slate-700 dark:text-slate-300">{{ $daftar->event->lokasi_venue }}</span>
                                        <a href="{{ $daftar->event->maps_url }}" target="_blank" rel="noopener noreferrer"
                                           class="inline-flex items-center gap-1 text-[11px] text-blue-600 dark:text-blue-400 hover:underline font-bold mt-0.5 block">
                                            🗺️ Lokasi Venue
                                        </a>
                                    </div>
                                </div>

                                {{-- Payment Info --}}
                                @if($daftar->pembayaran)
                                    <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500 dark:text-slate-400 font-medium pt-1">
                                        <span>TRX: <strong class="font-mono text-slate-700 dark:text-slate-200">{{ $daftar->pembayaran->kode_transaksi }}</strong></span>
                                        <span>Via: <strong>{{ $daftar->pembayaran->metode_pembayaran }}</strong></span>
                                        <span>Total: <strong class="text-slate-700 dark:text-slate-200">Rp {{ number_format($daftar->pembayaran->total_bayar, 0, ',', '.') }}</strong></span>
                                    </div>
                                    @if($daftar->pembayaran->biaya_layanan > 0)
                                        <div class="flex flex-wrap gap-x-3 gap-y-0.5 text-[11px] text-slate-400 dark:text-slate-500 font-medium mt-0.5">
                                            <span>Tiket: Rp {{ number_format($daftar->pembayaran->harga_tiket, 0, ',', '.') }}</span>
                                            <span>+</span>
                                            <span>Biaya Layanan: Rp {{ number_format($daftar->pembayaran->biaya_layanan, 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                @endif

                                {{-- Action Buttons: Invoice & Record --}}
                                <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-2 sm:gap-2.5 border-t border-slate-100 dark:border-white/10 pt-4">
                                    <a href="{{ route('runner.ticket.invoice', $daftar->id_pendaftaran) }}" target="_blank"
                                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-500/15 dark:hover:bg-emerald-500/25 text-emerald-700 dark:text-emerald-300 font-extrabold text-xs uppercase tracking-wider transition-colors border border-emerald-200 dark:border-emerald-500/30 shadow-sm">
                                        🖨️ Cetak Invoice
                                    </a>
                                    <span class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-center">
                                        🏅 Rekam Jejak Pelari Resmi
                                    </span>
                                    {{-- Hubungi Panitia CP (Riwayat/Selesai) --}}
                                    @php
                                        $rawWaHistory  = !empty($daftar->event->no_wa_cp) ? $daftar->event->no_wa_cp : (!empty($daftar->event->organizer->no_hp) ? $daftar->event->organizer->no_hp : config('services.admin_wa', env('ADMIN_WA', '6287812822400')));
                                        $cleanWaHistory = preg_replace('/\D/', '', (string)$rawWaHistory);
                                        if (str_starts_with($cleanWaHistory, '0')) {
                                            $cleanWaHistory = '62' . substr($cleanWaHistory, 1);
                                        }
                                        $cpMsgHistory = urlencode('Halo Panitia ' . $daftar->event->nama_event . ', saya ' . auth()->user()->nama . ' (No. BIB: ' . ($daftar->bib_number ?? '-') . ') ingin menanyakan seputar teknis event lomba.');
                                    @endphp
                                    @if($cleanWaHistory)
                                        <a href="https://wa.me/{{ $cleanWaHistory }}?text={{ $cpMsgHistory }}" target="_blank" rel="noopener noreferrer"
                                           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-green-50 hover:bg-green-100 dark:bg-green-500/15 dark:hover:bg-green-500/25 text-green-700 dark:text-green-300 font-extrabold text-xs uppercase tracking-wider transition-colors border border-green-200 dark:border-green-500/30">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.114 1.524 5.843L.057 23.569a.75.75 0 00.974.974l5.726-1.467A11.952 11.952 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.891 0-3.667-.522-5.179-1.428l-.371-.22-3.838.983.999-3.712-.242-.384A9.953 9.953 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                                            💬 Hubungi Panitia
                                        </a>
                                    @endif
                                </div>
                            </div>

                            {{-- PERFORATED DIVIDER (DESKTOP & REALISTIC MOBILE TEAR SLIP) --}}
                            <div class="hidden md:block perforated-divider self-stretch w-0 my-4"></div>
                            <div class="md:hidden relative my-2">
                                <div class="absolute -left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-slate-100 dark:bg-[#0a1825]"></div>
                                <div class="border-t-2 border-dashed border-slate-200 dark:border-white/10 mx-5"></div>
                                <div class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-slate-100 dark:bg-[#0a1825]"></div>
                                <div class="absolute left-1/2 -translate-x-1/2 top-1/2 -translate-y-1/2 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-[9px] font-black uppercase px-2.5 py-0.5 rounded-full border border-slate-200 dark:border-white/10">
                                    ✂ E-Ticket Arsip
                                </div>
                            </div>

                            {{-- RIGHT PANEL — Archived / Disabled QR Code --}}
                            <div class="md:w-52 xl:w-56 p-5 sm:p-6 flex flex-col items-center justify-center text-center gap-3 sm:gap-4 bg-slate-100/70 dark:bg-black/20 shrink-0">
                                <div>
                                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 block mb-2 sm:mb-3">QR Code E-Ticket</span>

                                    {{-- QR Code with Archive Dark Overlay --}}
                                    <div class="relative inline-block rounded-2xl overflow-hidden border border-slate-300 dark:border-white/10 shadow-md">
                                        <div class="p-2.5 sm:p-3 bg-white opacity-25 filter grayscale" data-preserve-white>
                                            {!! QrCode::size(130)->generate($daftar->qr_code_token) !!}
                                        </div>
                                        <div class="absolute inset-0 bg-slate-950/85 backdrop-blur-[2px] flex flex-col items-center justify-center p-3 text-center">
                                            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-slate-800 border border-slate-600 flex items-center justify-center text-sm sm:text-base text-slate-300 mb-1 shadow-inner">
                                                🔒
                                            </div>
                                            <span class="text-[10px] sm:text-[11px] font-black uppercase tracking-wider text-slate-200">Event Selesai</span>
                                            <span class="text-[9px] text-slate-400 leading-tight mt-0.5">Masa Berlaku Berakhir</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Racepack Status --}}
                                <div class="w-full">
                                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 block mb-1">Status Tiket</span>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-200 dark:bg-white/10 text-slate-600 dark:text-slate-300 border border-slate-300 dark:border-white/10">
                                        🔒 Diarsipkan
                                    </span>
                                </div>

                                {{-- Token --}}
                                <p class="text-[9px] text-slate-400 dark:text-slate-500 font-mono break-all select-all leading-relaxed w-full max-w-[200px]">{{ $daftar->qr_code_token }}</p>

                                {{-- Notice --}}
                                <p class="text-[9px] text-slate-400 italic leading-tight">QR Code dinonaktifkan karena event telah selesai dilaksanakan.</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                {{-- Empty State: Riwayat Event Selesai --}}
                <div class="glass-card rounded-3xl p-8 sm:p-12 text-center border border-slate-200 dark:border-white/10 shadow-sm">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 mx-auto rounded-2xl bg-slate-100 dark:bg-white/10 flex items-center justify-center text-2xl sm:text-3xl mb-3 sm:mb-4 shadow-inner">🏁</div>
                    <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">Belum Ada Riwayat Event Selesai</h3>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1.5 mb-5 max-w-md mx-auto">
                        Tiket event yang telah selesai dilaksanakan atau melewati tanggal lomba akan otomatis diarsipkan di sini sebagai rekam jejak lari Anda.
                    </p>
                    <a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 sm:px-6 sm:py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-extrabold text-xs uppercase tracking-wider transition shadow">
                        Jelajahi Event Lari Lainnya →
                    </a>
                </div>
            @endif
        </div>

    </div>

</div>

{{-- Midtrans Snap --}}
<script type="text/javascript"
    src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ config('midtrans.client_key', env('MIDTRANS_CLIENT_KEY')) }}">
</script>
<script>
    // Tab Navigation Logic
    function switchTicketTab(tab) {
        const tabActiveBtn = document.getElementById('tab-btn-active');
        const tabHistoryBtn = document.getElementById('tab-btn-history');
        const contentActive = document.getElementById('tab-content-active');
        const contentHistory = document.getElementById('tab-content-history');

        if (!tabActiveBtn || !tabHistoryBtn || !contentActive || !contentHistory) return;

        const activeClasses = 'tab-btn py-2.5 px-3 sm:px-6 rounded-xl text-xs sm:text-sm font-bold italic uppercase tracking-wider transition-all duration-200 flex items-center justify-center gap-1.5 sm:gap-2 bg-[#F05423] text-white shadow-md shadow-orange-500/20';
        const inactiveClasses = 'tab-btn py-2.5 px-3 sm:px-6 rounded-xl text-xs sm:text-sm font-bold italic uppercase tracking-wider transition-all duration-200 flex items-center justify-center gap-1.5 sm:gap-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white border border-transparent';

        if (tab === 'history') {
            contentActive.classList.add('hidden');
            contentHistory.classList.remove('hidden');

            tabHistoryBtn.className = activeClasses;
            tabActiveBtn.className = inactiveClasses;
        } else {
            contentActive.classList.remove('hidden');
            contentHistory.classList.add('hidden');

            tabActiveBtn.className = activeClasses;
            tabHistoryBtn.className = inactiveClasses;
        }

        if (history.replaceState) {
            history.replaceState(null, null, '#' + tab);
        }
    }

    // Initialize Tab based on hash
    document.addEventListener('DOMContentLoaded', () => {
        if (window.location.hash === '#history') {
            switchTicketTab('history');
        }
    });

    // Payment Logic
    function bayarMidtrans(token) {
        if (typeof window.snap === 'undefined') { alert('Sistem pembayaran belum siap.'); return; }
        window.snap.pay(token, {
            onSuccess: () => window.location.href = "{{ route('runner.dashboard') }}",
            onPending: () => window.location.href = "{{ route('runner.dashboard') }}",
            onError:   () => { alert('Pembayaran gagal!'); window.location.href = "{{ route('runner.dashboard') }}"; },
            onClose:   () => window.location.reload(),
        });
    }
    @if(session('snap_token'))
        document.addEventListener('DOMContentLoaded', () => setTimeout(() => bayarMidtrans('{{ session('snap_token') }}'), 500));
    @endif
</script>
@endsection