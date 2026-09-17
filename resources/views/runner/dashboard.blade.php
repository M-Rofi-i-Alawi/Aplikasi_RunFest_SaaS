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
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- ===== HEADER ===== --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-white/10 pb-6">
        <div>
            <span class="inline-block px-3 py-1 text-[10px] font-black uppercase tracking-widest text-[#F05423] bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/30 rounded-xl mb-2">
                🎫 Runner Dashboard
            </span>
            <h1 class="text-2xl sm:text-3xl font-black italic uppercase tracking-tight text-slate-900 dark:text-white">
                Tiket & Racepack Saya
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Selamat datang, <span class="font-bold text-slate-900 dark:text-white">{{ auth()->user()->nama }}</span>
            </p>
        </div>
        <a href="{{ route('events.index') }}"
           class="inline-flex items-center gap-2 justify-center px-5 py-2.5 rounded-xl btn-brand-orange text-white font-extrabold text-sm shadow-md transition-all">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Daftar Event Baru
        </a>
    </div>

    {{-- ===== WARNING IDENTITAS ===== --}}
    @if(auth()->user()->isRunner() && (empty(auth()->user()->nik) || strlen(auth()->user()->nik) !== 16 || empty(auth()->user()->foto_identitas) || !auth()->user()->google_id))
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-950/40 dark:to-orange-950/30 border-2 border-amber-300 dark:border-amber-500/40 rounded-3xl p-6 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-5">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center font-black text-xl shrink-0 shadow-lg shadow-amber-500/30">⚠️</div>
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="text-sm font-black text-amber-950 dark:text-amber-200 uppercase tracking-tight">Verifikasi Identitas Diperlukan</h3>
                        @if(!auth()->user()->google_id)
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300 border border-rose-200 dark:border-rose-500/30">Akun Manual</span>
                        @endif
                        @if(empty(auth()->user()->nik) || empty(auth()->user()->foto_identitas))
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30">KTP Belum Diunggah</span>
                        @endif
                    </div>
                    <p class="text-xs text-amber-900/80 dark:text-amber-300/90 leading-relaxed max-w-2xl">
                        Untuk mencegah <strong>akun fiktif & joki</strong>, Anda wajib mengisi NIK 16 digit dan mengunggah Foto KTP/Kartu Pelajar Asli sebelum pengambilan Racepack.
                    </p>
                </div>
            </div>
            <a href="{{ route('account.settings') }}" class="shrink-0 px-5 py-3 rounded-2xl bg-[#F05423] hover:bg-[#D4461A] text-white font-black text-xs uppercase tracking-wider transition-all shadow-md">
                Lengkapi NIK & KTP →
            </a>
        </div>
    @endif

    {{-- ===== STATS CARDS ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="glass-card rounded-2xl p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-white/10 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-slate-500 dark:text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z"/></svg>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">Total Tiket Terdaftar</span>
                <span class="text-3xl font-black text-slate-800 dark:text-white">{{ $pendaftaran->whereNotIn('status_pembayaran', ['Gagal'])->count() }}</span>
            </div>
        </div>
        <div class="glass-card rounded-2xl p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-orange-50 dark:bg-orange-500/15 flex items-center justify-center shrink-0">
                <span class="text-xl">🏃‍♂️</span>
            </div>
            <div>
                <span class="text-[10px] font-bold text-[#F05423] uppercase tracking-wide block">Tiket Aktif</span>
                <span class="text-3xl font-black text-[#F05423]">{{ $activeTickets->count() }}</span>
            </div>
        </div>
        <div class="glass-card rounded-2xl p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-blue-50 dark:bg-blue-500/15 flex items-center justify-center shrink-0">
                <span class="text-xl">🏁</span>
            </div>
            <div>
                <span class="text-[10px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wide block">Riwayat Event Selesai</span>
                <span class="text-3xl font-black text-blue-600 dark:text-blue-400">{{ $historyTickets->count() }}</span>
            </div>
        </div>
    </div>

    {{-- ===== TAB NAVIGATION & TICKET LIST ===== --}}
    <div class="space-y-6">

        {{-- Tab Buttons (Pill Tabs) --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 dark:border-white/10 pb-4">
            <div class="inline-flex p-1.5 rounded-2xl bg-slate-100 dark:bg-[#0b1329] border border-slate-200 dark:border-white/10 max-w-full overflow-x-auto gap-1">
                {{-- Tab 1: Tiket Aktif --}}
                <button type="button" id="tab-btn-active" onclick="switchTicketTab('active')"
                        class="tab-btn px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold italic uppercase tracking-wider transition-all duration-200 flex items-center gap-2 bg-[#F05423] text-white shadow-md shadow-orange-500/20 whitespace-nowrap">
                    <span>🏃‍♂️</span> Tiket Aktif ({{ $activeTickets->count() }})
                </button>

                {{-- Tab 2: Riwayat Event Selesai --}}
                <button type="button" id="tab-btn-history" onclick="switchTicketTab('history')"
                        class="tab-btn px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold italic uppercase tracking-wider transition-all duration-200 flex items-center gap-2 bg-slate-100 dark:bg-[#0f2137] text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white border border-transparent whitespace-nowrap">
                    <span>🏁</span> Riwayat Event Selesai ({{ $historyTickets->count() }})
                </button>
            </div>

            <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">
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
                            <div class="ticket-header-stripe px-6 py-4 flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ asset('images/logo-icon.png') }}" alt="RunFest" class="h-7 w-auto object-contain brightness-[5] grayscale opacity-80">
                                    <div>
                                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 block">RunFest SaaS — E-Ticket Aktif</span>
                                        <h3 class="text-base font-black text-white leading-tight truncate max-w-[220px] sm:max-w-none">{{ $daftar->event->nama_event }}</h3>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="px-3 py-1 rounded-xl text-xs font-black uppercase tracking-wider bg-emerald-500 text-white shadow-lg shadow-emerald-500/30">
                                        ✓ LUNAS
                                    </span>
                                    <span class="px-3 py-1 rounded-xl text-xs font-black uppercase tracking-wider border
                                        {{ $daftar->status_racepack === 'Sudah Diambil' ? 'bg-[#F05423] border-[#F05423] text-white' : 'bg-slate-700 border-slate-600 text-slate-300' }}">
                                        RP: {{ $daftar->status_racepack }}
                                    </span>
                                </div>
                            </div>

                            {{-- ---- MAIN BODY: 2 Panel with Perforated Divider ---- --}}
                            <div class="flex flex-col md:flex-row">

                                {{-- LEFT PANEL — Main Ticket --}}
                                <div class="flex-1 p-6 space-y-5 min-w-0">

                                    {{-- BIB Number --}}
                                    <div>
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-black uppercase tracking-widest block mb-1">Nomor BIB Peserta</span>
                                        @if(!empty($daftar->bib_number))
                                            <div class="bib-number text-5xl sm:text-6xl">{{ $daftar->bib_number }}</div>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-sm font-extrabold bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30">
                                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                Diterbitkan Setelah Lunas
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Participant Details Grid --}}
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                        <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-3 border border-slate-100 dark:border-white/8">
                                            <span class="text-[9px] text-slate-400 font-black uppercase tracking-wide block mb-0.5">Nama Peserta</span>
                                            <span class="text-sm font-extrabold text-slate-900 dark:text-white truncate block">{{ auth()->user()->nama }}</span>
                                        </div>
                                        <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-3 border border-slate-100 dark:border-white/8">
                                            <span class="text-[9px] text-slate-400 font-black uppercase tracking-wide block mb-0.5">Kategori</span>
                                            <span class="text-sm font-extrabold text-[#F05423] truncate block">{{ $daftar->kategori->nama_kategori }}</span>
                                        </div>
                                        <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-3 border border-slate-100 dark:border-white/8">
                                            <span class="text-[9px] text-slate-400 font-black uppercase tracking-wide block mb-0.5">Ukuran Jersey</span>
                                            <span class="text-xl font-black text-slate-900 dark:text-white tracking-wider">{{ $daftar->ukuran_jersey }}</span>
                                        </div>
                                        <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-3 border border-slate-100 dark:border-white/8">
                                            <span class="text-[9px] text-slate-400 font-black uppercase tracking-wide block mb-0.5">Gol. Darah</span>
                                            <span class="text-base font-black text-slate-900 dark:text-white">{{ $daftar->runner->golongan_darah ?? auth()->user()->golongan_darah ?? '-' }}</span>
                                        </div>
                                        <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-3 border border-slate-100 dark:border-white/8 col-span-1 sm:col-span-2">
                                            <span class="text-[9px] text-slate-400 font-black uppercase tracking-wide block mb-0.5">Tanggal Lomba</span>
                                            <span class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $daftar->event->tanggal_event->format('d M Y') }}</span>
                                        </div>
                                    </div>

                                    {{-- Location --}}
                                    <div class="flex items-start gap-2 text-sm">
                                        <span class="text-base shrink-0 mt-0.5">📍</span>
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
                                        <div class="border-t border-dashed border-slate-200 dark:border-white/10 pt-4">
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
                                    @endif

                                    {{-- Action Buttons --}}
                                    <div class="flex flex-wrap items-center gap-2.5 border-t border-slate-100 dark:border-white/10 pt-4">
                                        <a href="{{ route('runner.ticket.invoice', $daftar->id_pendaftaran) }}" target="_blank"
                                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-500/15 dark:hover:bg-emerald-500/25 text-emerald-700 dark:text-emerald-300 font-extrabold text-xs uppercase tracking-wider transition-colors border border-emerald-200 dark:border-emerald-500/30">
                                            🖨️ Cetak Invoice
                                        </a>
                                        @if($daftar->google_calendar_race_day_url)
                                            <a href="{{ $daftar->google_calendar_race_day_url }}" target="_blank" rel="noopener noreferrer"
                                               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 dark:bg-blue-500/15 dark:hover:bg-blue-500/25 text-blue-700 dark:text-blue-300 font-extrabold text-xs uppercase tracking-wider transition-colors border border-blue-200 dark:border-blue-500/30">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 002 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zM5 8V6h14v2H5zm2 4h5v5H7v-5z"/></svg>
                                                Simpan Hari Lomba
                                            </a>
                                        @endif
                                        @if($daftar->google_calendar_rpc_url)
                                            <a href="{{ $daftar->google_calendar_rpc_url }}" target="_blank" rel="noopener noreferrer"
                                               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-violet-50 hover:bg-violet-100 dark:bg-violet-500/15 dark:hover:bg-violet-500/25 text-violet-700 dark:text-violet-300 font-extrabold text-xs uppercase tracking-wider transition-colors border border-violet-200 dark:border-violet-500/30">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 002 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zM5 8V6h14v2H5zm2 4h5v5H7v-5z"/></svg>
                                                Simpan Jadwal RPC
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                {{-- PERFORATED DIVIDER --}}
                                <div class="hidden md:block perforated-divider self-stretch w-0 my-4"></div>
                                <div class="md:hidden border-t-2 border-dashed border-slate-200 dark:border-white/10 mx-6 relative">
                                    <div class="absolute left-1/2 -translate-x-1/2 -top-4 bg-slate-200 dark:bg-slate-700 text-slate-400 dark:text-slate-500 text-[10px] px-3 py-1 rounded-full font-bold uppercase tracking-wide">✂ E-Ticket</div>
                                </div>

                                {{-- RIGHT PANEL — High-Contrast QR + Verification --}}
                                <div class="md:w-52 xl:w-56 p-6 flex flex-col items-center justify-center text-center gap-4 bg-slate-50 dark:bg-white/3 shrink-0">
                                    <div>
                                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 block mb-3">QR Code E-Ticket</span>
                                        <div class="qr-box data-qr-box bg-white p-3 rounded-2xl border border-slate-200 shadow-md inline-block" data-preserve-white>
                                            {!! QrCode::size(140)->generate($daftar->qr_code_token) !!}
                                        </div>
                                    </div>

                                    {{-- Racepack Status --}}
                                    <div class="w-full">
                                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 block mb-1.5">Status Racepack</span>
                                        @if($daftar->status_racepack === 'Sudah Diambil')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-black bg-[#F05423] text-white shadow-md">
                                                ✓ Sudah Diambil
                                            </span>
                                            @if($daftar->waktu_pengambilan_racepack)
                                                <p class="text-[10px] text-slate-400 mt-1.5 font-mono">{{ $daftar->waktu_pengambilan_racepack->format('d M Y, H:i') }}</p>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-black bg-amber-100 dark:bg-amber-500/15 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30">
                                                ⏳ Belum Diambil
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Token --}}
                                    <p class="text-[9px] text-slate-400 dark:text-slate-500 font-mono break-all select-all leading-relaxed w-full">{{ $daftar->qr_code_token }}</p>

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
                            <div class="bg-slate-900 dark:bg-[#0b1329] px-6 py-4 text-white flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <span class="text-[10px] text-orange-400 font-extrabold uppercase tracking-wider">Nama Event</span>
                                    <h3 class="text-lg font-black leading-tight">{{ $daftar->event->nama_event }}</h3>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="px-3 py-1 rounded-xl text-xs font-black uppercase tracking-wider {{ $daftar->status_pembayaran === 'Pending' ? 'bg-amber-500' : 'bg-rose-500' }} text-white">
                                        {{ $daftar->status_pembayaran }}
                                    </span>
                                </div>
                            </div>

                            {{-- Body --}}
                            <div class="p-6 space-y-4">
                                <div class="flex items-center gap-3">
                                    <div class="bg-slate-100 dark:bg-white/10 p-4 rounded-2xl border border-slate-200 dark:border-white/15 text-center">
                                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">Nomor BIB</span>
                                        <span class="text-2xl font-black text-slate-400 dark:text-slate-500 italic">—</span>
                                        <span class="text-[10px] text-slate-400 block mt-1">Belum Lunas</span>
                                    </div>
                                    <div class="space-y-1 flex-1">
                                        <div><span class="text-xs text-slate-400 font-bold uppercase block">Kategori</span><span class="font-extrabold text-slate-800 dark:text-slate-200">{{ $daftar->kategori->nama_kategori }}</span></div>
                                        <div><span class="text-xs text-slate-400 font-bold uppercase block">Tanggal</span><span class="font-bold text-slate-700 dark:text-slate-300">{{ $daftar->event->tanggal_event->format('d M Y') }}</span></div>
                                        <div><span class="text-xs text-slate-400 font-bold uppercase block">Jersey</span><span class="font-extrabold text-[#F05423] text-lg">{{ $daftar->ukuran_jersey }}</span></div>
                                    </div>
                                </div>

                                {{-- Pending Actions --}}
                                @if($daftar->status_pembayaran === 'Pending')
                                    <div class="flex flex-wrap items-center gap-3 pt-2 border-t border-slate-100 dark:border-white/10">
                                        @if(!empty($daftar->pembayaran->snap_token))
                                            <button type="button"
                                                    id="pay-btn-{{ $daftar->id_pendaftaran }}"
                                                    onclick="bayarMidtrans('{{ $daftar->pembayaran->snap_token }}')"
                                                    class="pay-button px-5 py-2.5 rounded-xl bg-[#F05423] hover:bg-[#D4461A] text-white font-extrabold text-xs uppercase tracking-wider shadow-lg hover:shadow-orange-500/25 transition">
                                                💳 Bayar Sekarang
                                            </button>
                                        @else
                                            <form method="POST" action="{{ route('runner.payment-token', $daftar->id_pendaftaran) }}">
                                                @csrf
                                                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#F05423] hover:bg-[#D4461A] text-white font-extrabold text-xs uppercase tracking-wider shadow-lg transition">
                                                    💳 Lanjut ke Pembayaran
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('runner.cancel-registration', $daftar->id_pendaftaran) }}"
                                              onsubmit="return confirm('Batalkan pendaftaran ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-500/15 text-rose-700 dark:text-rose-300 font-extrabold text-xs uppercase border border-rose-200 dark:border-rose-500/30 transition">
                                                ✕ Batalkan
                                            </button>
                                        </form>
                                    </div>
                                @endif
                                @if($daftar->status_pembayaran === 'Gagal')
                                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 font-bold text-xs border border-rose-200 dark:border-rose-500/30">
                                        ✕ Pendaftaran Dibatalkan / Gagal
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif

                @endforeach
            @else
                {{-- Empty State: Tiket Aktif --}}
                <div class="glass-card rounded-3xl p-12 text-center border border-slate-200 dark:border-white/10 shadow-sm">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-orange-50 dark:bg-orange-500/10 flex items-center justify-center text-3xl mb-4 shadow-inner">🏃‍♂️</div>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white">Belum Ada Tiket Aktif</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 mb-6 max-w-md mx-auto">
                        Anda belum memiliki tiket untuk event lari mendatang. Daftarkan diri ke event lari pilihanmu sekarang!
                    </p>
                    <a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl btn-brand-orange text-white font-extrabold text-xs uppercase tracking-wider shadow-lg shadow-orange-500/25 transition">
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
                        <div class="ticket-header-stripe-history px-6 py-4 flex flex-wrap items-center justify-between gap-3 text-white">
                            <div class="flex items-center gap-3">
                                <img src="{{ asset('images/logo-icon.png') }}" alt="RunFest" class="h-7 w-auto object-contain brightness-[5] grayscale opacity-70">
                                <div>
                                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 block">RunFest SaaS — E-Ticket Arsip</span>
                                    <h3 class="text-base font-black text-slate-200 leading-tight truncate max-w-[220px] sm:max-w-none">{{ $daftar->event->nama_event }}</h3>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="px-3 py-1 rounded-xl text-xs font-black uppercase tracking-wider bg-slate-700 text-slate-200 border border-slate-600 shadow-sm">
                                    🏁 Event Selesai
                                </span>
                                <span class="px-3 py-1 rounded-xl text-xs font-black uppercase tracking-wider bg-blue-500/20 text-blue-300 border border-blue-500/40">
                                    ✓ LUNAS
                                </span>
                                <span class="px-3 py-1 rounded-xl text-xs font-black uppercase tracking-wider border
                                    {{ $daftar->status_racepack === 'Sudah Diambil' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' : 'bg-slate-700 text-slate-400 border-slate-600' }}">
                                    RP: {{ $daftar->status_racepack === 'Sudah Diambil' ? 'Diambil' : 'Ditutup' }}
                                </span>
                            </div>
                        </div>

                        {{-- ---- MAIN BODY: 2 Panel with Perforated Divider ---- --}}
                        <div class="flex flex-col md:flex-row">

                            {{-- LEFT PANEL — Ticket Info & Finisher Record --}}
                            <div class="flex-1 p-6 space-y-5 min-w-0">

                                {{-- BIB Number (Rekam Jejak) --}}
                                <div>
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 font-black uppercase tracking-widest block mb-1">Nomor BIB Peserta (Rekam Jejak)</span>
                                    @if(!empty($daftar->bib_number))
                                        <div class="bib-number bib-number-history text-5xl sm:text-6xl">{{ $daftar->bib_number }}</div>
                                    @else
                                        <span class="text-2xl font-black text-slate-400 italic">—</span>
                                    @endif
                                </div>

                                {{-- Participant Details Grid --}}
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-3 border border-slate-100 dark:border-white/8">
                                        <span class="text-[9px] text-slate-400 font-black uppercase tracking-wide block mb-0.5">Nama Peserta</span>
                                        <span class="text-sm font-extrabold text-slate-900 dark:text-white truncate block">{{ auth()->user()->nama }}</span>
                                    </div>
                                    <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-3 border border-slate-100 dark:border-white/8">
                                        <span class="text-[9px] text-slate-400 font-black uppercase tracking-wide block mb-0.5">Kategori</span>
                                        <span class="text-sm font-extrabold text-slate-700 dark:text-slate-200 truncate block">{{ $daftar->kategori->nama_kategori }}</span>
                                    </div>
                                    <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-3 border border-slate-100 dark:border-white/8">
                                        <span class="text-[9px] text-slate-400 font-black uppercase tracking-wide block mb-0.5">Ukuran Jersey</span>
                                        <span class="text-xl font-black text-slate-800 dark:text-slate-200 tracking-wider">{{ $daftar->ukuran_jersey }}</span>
                                    </div>
                                    <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-3 border border-slate-100 dark:border-white/8">
                                        <span class="text-[9px] text-slate-400 font-black uppercase tracking-wide block mb-0.5">Gol. Darah</span>
                                        <span class="text-base font-black text-slate-800 dark:text-slate-200">{{ $daftar->runner->golongan_darah ?? auth()->user()->golongan_darah ?? '-' }}</span>
                                    </div>
                                    <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-3 border border-slate-100 dark:border-white/8 col-span-1 sm:col-span-2">
                                        <span class="text-[9px] text-slate-400 font-black uppercase tracking-wide block mb-0.5">Tanggal Pelaksanaan</span>
                                        <span class="text-sm font-extrabold text-slate-900 dark:text-white">
                                            {{ $daftar->event->tanggal_event->format('d M Y') }}
                                            <span class="text-xs font-semibold text-slate-400 dark:text-slate-500">(Telah Selesai)</span>
                                        </span>
                                    </div>
                                </div>

                                {{-- Location --}}
                                <div class="flex items-start gap-2 text-sm">
                                    <span class="text-base shrink-0 mt-0.5">📍</span>
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
                                @endif

                                {{-- Action Buttons: Invoice & Record --}}
                                <div class="flex flex-wrap items-center gap-2.5 border-t border-slate-100 dark:border-white/10 pt-4">
                                    <a href="{{ route('runner.ticket.invoice', $daftar->id_pendaftaran) }}" target="_blank"
                                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-500/15 dark:hover:bg-emerald-500/25 text-emerald-700 dark:text-emerald-300 font-extrabold text-xs uppercase tracking-wider transition-colors border border-emerald-200 dark:border-emerald-500/30 shadow-sm">
                                        🖨️ Cetak Invoice
                                    </a>
                                    <span class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10">
                                        🏅 Rekam Jejak Pelari Resmi
                                    </span>
                                </div>
                            </div>

                            {{-- PERFORATED DIVIDER --}}
                            <div class="hidden md:block perforated-divider self-stretch w-0 my-4"></div>
                            <div class="md:hidden border-t-2 border-dashed border-slate-200 dark:border-white/10 mx-6 relative">
                                <div class="absolute left-1/2 -translate-x-1/2 -top-4 bg-slate-200 dark:bg-slate-700 text-slate-400 dark:text-slate-500 text-[10px] px-3 py-1 rounded-full font-bold uppercase tracking-wide">✂ E-Ticket Arsip</div>
                            </div>

                            {{-- RIGHT PANEL — Archived / Disabled QR Code --}}
                            <div class="md:w-52 xl:w-56 p-6 flex flex-col items-center justify-center text-center gap-4 bg-slate-100/70 dark:bg-black/20 shrink-0">
                                <div>
                                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 block mb-3">QR Code E-Ticket</span>

                                    {{-- QR Code with Archive Dark Overlay --}}
                                    <div class="relative inline-block rounded-2xl overflow-hidden border border-slate-300 dark:border-white/10 shadow-md">
                                        <div class="p-3 bg-white opacity-25 filter grayscale" data-preserve-white>
                                            {!! QrCode::size(130)->generate($daftar->qr_code_token) !!}
                                        </div>
                                        <div class="absolute inset-0 bg-slate-950/85 backdrop-blur-[2px] flex flex-col items-center justify-center p-3 text-center">
                                            <div class="w-9 h-9 rounded-full bg-slate-800 border border-slate-600 flex items-center justify-center text-base text-slate-300 mb-1 shadow-inner">
                                                🔒
                                            </div>
                                            <span class="text-[11px] font-black uppercase tracking-wider text-slate-200">Event Selesai</span>
                                            <span class="text-[9px] text-slate-400 leading-tight mt-0.5">Masa Berlaku Berakhir</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Racepack Status --}}
                                <div class="w-full">
                                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 block mb-1.5">Status Tiket</span>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-200 dark:bg-white/10 text-slate-600 dark:text-slate-300 border border-slate-300 dark:border-white/10">
                                        🔒 Diarsipkan
                                    </span>
                                </div>

                                {{-- Token --}}
                                <p class="text-[9px] text-slate-400 dark:text-slate-500 font-mono break-all select-all leading-relaxed w-full">{{ $daftar->qr_code_token }}</p>

                                {{-- Notice --}}
                                <p class="text-[9px] text-slate-400 italic leading-tight">QR Code dinonaktifkan karena event telah selesai dilaksanakan.</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                {{-- Empty State: Riwayat Event Selesai --}}
                <div class="glass-card rounded-3xl p-12 text-center border border-slate-200 dark:border-white/10 shadow-sm">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-100 dark:bg-white/10 flex items-center justify-center text-3xl mb-4 shadow-inner">🏁</div>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white">Belum Ada Riwayat Event Lari yang Selesai</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 mb-6 max-w-md mx-auto">
                        Tiket event yang telah selesai dilaksanakan atau melewati tanggal lomba akan otomatis diarsipkan di sini sebagai rekam jejak lari Anda.
                    </p>
                    <a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-extrabold text-xs uppercase tracking-wider transition shadow">
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

        if (tab === 'history') {
            contentActive.classList.add('hidden');
            contentHistory.classList.remove('hidden');

            // History Active styling
            tabHistoryBtn.className = 'tab-btn px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold italic uppercase tracking-wider transition-all duration-200 flex items-center gap-2 bg-[#F05423] text-white shadow-md shadow-orange-500/20 whitespace-nowrap';
            // Active Inactive styling
            tabActiveBtn.className = 'tab-btn px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold italic uppercase tracking-wider transition-all duration-200 flex items-center gap-2 bg-slate-100 dark:bg-[#0f2137] text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white border border-transparent whitespace-nowrap';
        } else {
            contentActive.classList.remove('hidden');
            contentHistory.classList.add('hidden');

            // Active Active styling
            tabActiveBtn.className = 'tab-btn px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold italic uppercase tracking-wider transition-all duration-200 flex items-center gap-2 bg-[#F05423] text-white shadow-md shadow-orange-500/20 whitespace-nowrap';
            // History Inactive styling
            tabHistoryBtn.className = 'tab-btn px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold italic uppercase tracking-wider transition-all duration-200 flex items-center gap-2 bg-slate-100 dark:bg-[#0f2137] text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white border border-transparent whitespace-nowrap';
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