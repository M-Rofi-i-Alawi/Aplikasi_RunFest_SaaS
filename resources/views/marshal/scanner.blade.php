@extends('layouts.app')

@section('title', 'Field Terminal Scanner Racepack — RunFest SaaS')

@push('styles')
<style>
    /* Styling agar video camera stream dari html5-qrcode memenuhi container 100% */
    #reader {
        border: none !important;
        background: #090d16 !important;
        position: relative;
        overflow: hidden !important;
        width: 100% !important;
        height: 100% !important;
    }

    #reader video {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        border-radius: 1.5rem;
    }

    #reader__scan_region {
        width: 100% !important;
        height: 100% !important;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden !important;
        border: none !important;
    }

    /* Hilangkan SEMUA overlay bawaan library html5-qrcode */
    #qr-shaded-region,
    #reader__scan_region img,
    #reader__scan_region svg,
    #reader svg,
    #reader img,
    #reader canvas:not(:first-child),
    #reader__dashboard_section_csr,
    #reader__dashboard_section_csr span,
    #reader__status_span,
    #reader__header_message {
        display: none !important;
        border: none !important;
        opacity: 0 !important;
        visibility: hidden !important;
        width: 0 !important;
        height: 0 !important;
    }

    #reader div {
        border: none !important;
    }

    /* Animasi Laser Line Scanning Menyala */
    @keyframes scanLaserLine {
        0% { top: 6%; opacity: 0.8; }
        50% { top: 92%; opacity: 1; }
        100% { top: 6%; opacity: 0.8; }
    }

    .scanning-laser-line {
        position: absolute;
        left: 4%;
        right: 4%;
        height: 3px;
        background: linear-gradient(90deg, transparent, #F05423 20%, #FF8A65 50%, #F05423 80%, transparent);
        box-shadow: 0 0 16px #F05423, 0 0 28px rgba(240, 84, 35, 0.7);
        animation: scanLaserLine 2.2s ease-in-out infinite;
        z-index: 25;
        pointer-events: none;
    }

    /* Tactical Terminal Container Styling */
    .tactical-terminal-bg {
        background: #0a1825;
    }

    .modal-fade-in {
        animation: modalScaleIn 0.22s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    @keyframes modalScaleIn {
        from { opacity: 0; transform: scale(0.94) translateY(8px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }

    /* Custom Sleek Scrollbar for Modal Body */
    .custom-modal-scroll::-webkit-scrollbar {
        width: 6px;
    }
    .custom-modal-scroll::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-modal-scroll::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.3);
        border-radius: 9999px;
    }
    .custom-modal-scroll::-webkit-scrollbar-thumb:hover {
        background: rgba(148, 163, 184, 0.5);
    }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto px-3 sm:px-6 py-4 sm:py-6 space-y-4 sm:space-y-5 w-full">

    {{-- ======================================================
         TACTICAL TERMINAL HEADER & MARSHAL IDENTITY
         ====================================================== --}}
    <div class="tactical-terminal-bg rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-slate-800 shadow-xl text-white relative overflow-hidden">
        {{-- Subtle ambient glow --}}
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-[#F05423]/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 relative z-10">
            {{-- Branding & Marshal Info --}}
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-gradient-to-tr from-[#F05423] to-orange-400 text-white flex items-center justify-center shrink-0 shadow-lg shadow-orange-500/25">
                    <img src="{{ asset('images/logo-icon.png') }}" alt="RunFest" class="h-6 sm:h-7 w-auto object-contain brightness-[5] grayscale">
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-[9px] font-black uppercase tracking-widest text-[#F05423] bg-orange-500/15 border border-orange-500/30 px-2 py-0.5 rounded-md">
                            MARSHAL TERMINAL
                        </span>
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    </div>
                    <h1 class="text-lg sm:text-xl font-black italic uppercase tracking-tight text-white mt-0.5">
                        Verifikasi Racepack
                    </h1>
                    <p class="text-xs text-slate-400 font-medium">
                        Petugas: <strong class="text-white">{{ auth()->user()->nama }}</strong>
                    </p>
                </div>
            </div>

            {{-- Controls: Switch Camera & Power Button --}}
            <div class="flex items-center gap-2 self-stretch sm:self-auto justify-end">
                <button type="button" id="btn-switch-camera" onclick="switchCamera()" title="Beralih Kamera Depan/Belakang"
                    class="hidden items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-300 bg-white/10 hover:bg-white/15 border border-white/10 transition-all">
                    <svg class="w-4 h-4 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                    </svg>
                    <span class="hidden sm:inline">Ganti Kamera</span>
                </button>

                <button type="button" id="btn-stop-camera" onclick="stopCamera()"
                    class="hidden items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-rose-400 bg-rose-500/15 hover:bg-rose-500/25 border border-rose-500/30 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 0 1 7.5 5.25h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25h-9a2.25 2.25 0 0 1-2.25-2.25v-9Z"/>
                    </svg>
                    <span>Matikan</span>
                </button>

                <span id="camera-status-badge" class="text-[11px] font-bold bg-white/10 text-slate-300 px-3 py-1.5 rounded-xl border border-white/15">
                    Kamera Siap
                </span>
            </div>
        </div>
    </div>

    {{-- Alert Notification Banner (untuk hasil Kuning / Merah / Sukses) --}}
    <div id="scan-alert-container" class="hidden"></div>

    {{-- ======================================================
         TACTICAL TERMINAL COCKPIT (DESKTOP: 2-COLUMN | MOBILE: STACKED)
         ====================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-5 items-start">

        {{-- LEFT COLUMN (lg:col-span-7): COMPACT CAMERA VIEWPORT & RETICLE --}}
        <div class="lg:col-span-7 tactical-terminal-bg rounded-2xl sm:rounded-3xl p-4 sm:p-5 border-2 border-slate-800 space-y-3.5 shadow-[0_0_30px_rgba(10,24,37,0.5)]">
            <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#F05423] animate-pulse"></span>
                    <h2 class="text-xs font-black uppercase tracking-wider text-slate-200">Kamera Scanner Lapangan</h2>
                </div>
                <span class="text-[10px] text-slate-400 font-mono font-bold uppercase">Frame 1:1 Presisi</span>
            </div>

            {{-- Camera Viewport: COMPACT SQUARE FRAME (NO STRETCH, NO ZOOMED-IN FOREHEAD!) --}}
            <div class="relative w-full max-w-[320px] sm:max-w-[360px] aspect-square mx-auto bg-[#090d16] rounded-2xl sm:rounded-3xl overflow-hidden border-2 border-[#F05423]/70 shadow-[0_0_20px_rgba(240,84,35,0.25)] flex items-center justify-center">
                
                {{-- Target Reticle Frame Overlay: 4 Corner Brackets + Scanning Laser --}}
                <div id="target-reticle" class="absolute inset-0 pointer-events-none z-20 flex items-center justify-center p-4 sm:p-6">
                    <div class="w-44 h-44 sm:w-52 sm:h-52 relative">
                        {{-- 4 Energy Orange Corners --}}
                        <div class="absolute -top-1 -left-1 w-6 h-6 sm:w-7 sm:h-7 border-t-4 border-l-4 border-[#F05423] rounded-tl-xl shadow-md"></div>
                        <div class="absolute -top-1 -right-1 w-6 h-6 sm:w-7 sm:h-7 border-t-4 border-r-4 border-[#F05423] rounded-tr-xl shadow-md"></div>
                        <div class="absolute -bottom-1 -left-1 w-6 h-6 sm:w-7 sm:h-7 border-b-4 border-l-4 border-[#F05423] rounded-bl-xl shadow-md"></div>
                        <div class="absolute -bottom-1 -right-1 w-6 h-6 sm:w-7 sm:h-7 border-b-4 border-r-4 border-[#F05423] rounded-br-xl shadow-md"></div>

                        {{-- Scanning Laser Line Animation --}}
                        <div class="scanning-laser-line"></div>
                    </div>
                </div>

                {{-- Element Html5Qrcode Reader Live --}}
                <div id="reader" class="w-full h-full"></div>

                {{-- Overlay Konfirmasi / Aktifkan Kamera --}}
                <div id="camera-prompt" class="absolute inset-0 z-30 flex flex-col items-center justify-center p-5 text-center bg-[#090d16]/95 backdrop-blur-md space-y-3 rounded-2xl sm:rounded-3xl">
                    <div class="w-14 h-14 rounded-2xl bg-[#F05423]/15 border-2 border-[#F05423]/40 flex items-center justify-center shadow-lg shadow-orange-500/20">
                        <svg class="w-7 h-7 text-[#F05423]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                        </svg>
                    </div>
                    <div class="space-y-1 max-w-xs">
                        <h3 class="text-sm sm:text-base font-black italic uppercase tracking-wide text-white">Aktifkan Kamera Terminal</h3>
                        <p class="text-[11px] text-slate-400 leading-relaxed">
                            Arahkan kamera ke QR Code tiket peserta untuk pemindaian instan.
                        </p>
                    </div>
                    <button type="button" onclick="confirmStartCamera()" id="btn-activate-camera"
                        class="px-5 py-2.5 btn-brand-orange text-white text-xs font-black uppercase tracking-wider rounded-xl shadow-lg shadow-orange-500/30 transition-all flex items-center justify-center gap-2 hover:scale-105">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z"/></svg>
                        Mulai Scan Kamera
                    </button>
                </div>

                {{-- Placeholder saat Kamera Gagal / Ditolak --}}
                <div id="camera-placeholder" class="hidden absolute inset-0 z-30 flex flex-col items-center justify-center p-5 text-center bg-slate-900/95 backdrop-blur-sm text-white space-y-2.5 rounded-2xl sm:rounded-3xl">
                    <svg class="w-10 h-10 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                    <div class="space-y-0.5">
                        <p class="text-xs font-bold text-white">Kamera Tidak Dapat Diakses</p>
                        <p class="text-[10px] text-slate-400 max-w-xs leading-relaxed">Berikan izin kamera browser atau gunakan input manual.</p>
                    </div>
                    <button type="button" onclick="showCameraPrompt()"
                        class="px-3.5 py-1.5 bg-[#F05423] hover:bg-[#D4461A] text-white text-[11px] font-extrabold rounded-lg shadow-md transition-all">
                        Coba Lagi
                    </button>
                </div>

            </div>

            {{-- Petunjuk Singkat --}}
            <div class="flex items-center justify-center gap-2 text-center text-[11px] text-slate-400 font-medium">
                <span class="w-1.5 h-1.5 rounded-full bg-orange-400 animate-pulse"></span>
                <span>Arahkan kamera ke QR Code E-Ticket Pelari untuk verifikasi otomatis</span>
            </div>
        </div>

        {{-- RIGHT COLUMN (lg:col-span-5): INPUT MANUAL & LIVE SCAN HISTORY --}}
        <div class="lg:col-span-5 space-y-4">
            
            {{-- Card 1: Input Manual / Barcode Scanner Gun --}}
            <div class="tactical-terminal-bg rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-slate-800 space-y-3 shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-200 flex items-center gap-1.5">
                        <span>⌨️</span> Input Manual / Barcode Gun
                    </span>
                    <span class="text-[9px] text-slate-400 font-bold uppercase">Tekan Enter ↵</span>
                </div>

                <div class="flex flex-col sm:flex-row gap-2">
                    <input type="text" id="qr-input"
                        class="flex-1 px-3.5 py-2.5 bg-slate-900/90 border border-slate-700 rounded-xl text-white placeholder-slate-500 text-xs sm:text-sm focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 outline-none font-mono font-bold tracking-wider"
                        placeholder="Nomor BIB (misal: 10K-0001)..."
                        autocomplete="off">
                    <button type="button" onclick="scanQR()" id="scan-btn"
                        class="px-5 py-2.5 btn-brand-orange text-white font-black text-xs uppercase tracking-wider rounded-xl shadow-md transition-all shrink-0 hover:scale-105 active:scale-95">
                        VERIFIKASI
                    </button>
                </div>
                <p class="text-[10px] text-slate-400 leading-tight">
                    Mendukung pemindai USB/Bluetooth scanner gun atau ketik langsung nomor BIB/token peserta.
                </p>
            </div>

            {{-- Card 2: Riwayat Scan Sesi Ini --}}
            <div class="tactical-terminal-bg rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-slate-800 space-y-3 shadow-md text-white">
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-200 flex items-center gap-1.5">
                        <span>📋</span> Riwayat Scan Sesi Ini
                    </h3>
                    <span id="history-count" class="text-[10px] font-bold text-slate-400 uppercase bg-slate-900 px-2 py-0.5 rounded-lg border border-slate-800">0 Tiket</span>
                </div>
                <div id="history-list" class="space-y-2 text-xs max-h-56 sm:max-h-64 overflow-y-auto custom-modal-scroll pr-1">
                    <p class="text-xs text-slate-500 italic py-4 text-center">Belum ada riwayat scan pada sesi ini.</p>
                </div>
            </div>

        </div>

    </div>

    {{-- ======================================================
         MODAL VERIFIKASI IDENTITAS & SERAH TERIMA (POPUP KONFIRMASI)
         ====================================================== --}}
    <div id="confirm-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-2.5 sm:p-4 md:p-6 overflow-hidden">
        {{-- Backdrop with blur --}}
        <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" onclick="closeConfirmModal()"></div>

        {{-- Modal Content Card (Bekerja sempurna di Mobile & Desktop dengan Flex Pinned Header & Footer) --}}
        <div class="relative bg-white dark:bg-[#0b1b2d] rounded-2xl sm:rounded-3xl shadow-2xl w-full max-w-lg md:max-w-3xl lg:max-w-4xl max-h-[92vh] sm:max-h-[88vh] flex flex-col border border-slate-200 dark:border-white/15 overflow-hidden modal-fade-in z-10">

            {{-- 1. PINNED HEADER (shrink-0: Selalu terlihat di atas) --}}
            <div class="shrink-0 bg-gradient-to-r from-emerald-600 via-emerald-600 to-teal-700 text-white px-4 sm:px-6 py-3.5 sm:py-4 flex items-center justify-between shadow-md border-b border-emerald-500/30">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white/20 border border-white/30 flex items-center justify-center font-black text-lg sm:text-xl shadow-inner shrink-0">
                        ✓
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-emerald-100 bg-emerald-700/60 px-2 py-0.5 rounded">
                                STATUS TIKET: LUNAS
                            </span>
                            <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>
                        </div>
                        <h3 class="text-sm sm:text-base md:text-lg font-black uppercase tracking-wide leading-tight mt-0.5 text-white">
                            Konfirmasi Serah Terima Racepack
                        </h3>
                    </div>
                </div>
                <button type="button" onclick="closeConfirmModal()" class="text-white/80 hover:text-white p-2 rounded-xl hover:bg-white/10 transition-colors shrink-0" title="Tutup Modal (Esc)">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- 2. SCROLLABLE BODY (flex-1: Scrollable jika layar sangat pendek, tanpa menggeser tombol aksi) --}}
            <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4 sm:space-y-5 custom-modal-scroll">
                
                {{-- Responsive Grid: Di Dekstop 2 Kolom (12-Grid), di Mobile Ramping Bertumpuk --}}
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 sm:gap-5 items-start">
                    
                    {{-- KOLOM KIRI (md:col-span-5): Atribut Utama Racepack (BIB & Jersey) --}}
                    <div class="md:col-span-5 space-y-3.5">
                        
                        {{-- Mobile: Side-by-Side (2 cols) | Desktop: Stacked Cards --}}
                        <div class="grid grid-cols-2 md:grid-cols-1 gap-3">
                            
                            {{-- Kartu Nomor BIB --}}
                            <div class="bg-slate-50 dark:bg-slate-900/90 p-3.5 sm:p-5 rounded-2xl border-2 border-slate-200 dark:border-white/10 text-center flex flex-col justify-center relative overflow-hidden group shadow-sm">
                                <div class="absolute top-0 left-0 right-0 h-1 bg-[#F05423]"></div>
                                <span class="text-[9px] sm:text-[10px] text-slate-400 font-black uppercase tracking-widest block mb-1">
                                    NOMOR BIB RESMI
                                </span>
                                <div id="modal-bib-number" class="font-mono text-2xl sm:text-3xl lg:text-4xl font-black italic text-[#F05423] tracking-tight leading-none break-all py-1">
                                    —
                                </div>
                                <span class="text-[9px] text-slate-500 font-bold uppercase tracking-wider hidden sm:block mt-1">
                                    Cocokkan dengan Nomor Fisik BIB
                                </span>
                            </div>

                            {{-- Kartu Ukuran Jersey --}}
                            <div class="bg-gradient-to-tr from-[#F05423] to-orange-500 text-white p-3.5 sm:p-5 rounded-2xl shadow-lg shadow-orange-500/25 text-center flex flex-col justify-center items-center relative overflow-hidden">
                                <span class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-orange-100 block mb-1">
                                    UKURAN JERSEY
                                </span>
                                <div id="modal-jersey-size" class="text-3xl sm:text-4xl lg:text-5xl font-black italic tracking-wider leading-none py-0.5">
                                    M
                                </div>
                                <span class="text-[9px] font-bold text-orange-100/90 uppercase tracking-wider hidden sm:block mt-1">
                                    Ambil Jersey Sesuai Ukuran Ini
                                </span>
                            </div>
                        </div>

                        {{-- Checklist Cepat Racepack (Bantuan Visual Petugas Lapangan di Desktop) --}}
                        <div class="hidden md:block bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/25 rounded-2xl p-3.5 text-xs text-emerald-800 dark:text-emerald-300 space-y-2">
                            <div class="font-black text-[10px] uppercase tracking-wider text-emerald-700 dark:text-emerald-400 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                ITEM PAKET RACEPACK:
                            </div>
                            <ul class="text-[11px] font-semibold space-y-1 pl-1 text-slate-700 dark:text-slate-300">
                                <li class="flex items-center gap-2"><span>🏷️</span> Nomor BIB + Peniti</li>
                                <li class="flex items-center gap-2"><span>👕</span> Jersey Race Sesuai Ukuran</li>
                                <li class="flex items-center gap-2"><span>🎒</span> Race Bag & Goodie Bag Sponsor</li>
                            </ul>
                        </div>

                    </div>

                    {{-- KOLOM KANAN (md:col-span-7): Verifikasi Identitas & Formulir Serah Terima --}}
                    <div class="md:col-span-7 space-y-3.5">
                        
                        {{-- Kartu Identitas Fisik (Anti-Joki) --}}
                        <div class="bg-blue-50/80 dark:bg-blue-500/10 border-2 border-blue-200 dark:border-blue-500/30 rounded-2xl p-3.5 sm:p-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] sm:text-[11px] font-black uppercase tracking-wider text-blue-800 dark:text-blue-300 flex items-center gap-1.5">
                                    <span>🪪</span> VERIFIKASI IDENTITAS FISIK (ANTI-JOKI)
                                </span>
                                <span class="text-[9px] font-bold text-blue-600 dark:text-blue-400 hidden sm:inline">Wajib Cocok</span>
                            </div>

                            <div class="flex items-start gap-3 sm:gap-4">
                                {{-- Thumbnail Foto KTP dengan Proporsi Standar 16:10 --}}
                                <div id="modal-foto-wrapper" class="shrink-0">
                                    <div class="w-28 h-20 sm:w-36 sm:h-24 aspect-[16/10] rounded-xl bg-slate-200 dark:bg-slate-800 border-2 border-slate-300 dark:border-white/20 overflow-hidden flex items-center justify-center text-center">
                                        <span class="text-[9px] text-slate-400">Memuat Foto...</span>
                                    </div>
                                </div>

                                {{-- Informasi Peserta: TEKS LENGKAP TANPA TRUNCATE --}}
                                <div class="min-w-0 flex-1 space-y-1.5">
                                    <div>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase block leading-none">Nama Lengkap Peserta</span>
                                        <strong id="modal-nama-runner" class="text-sm sm:text-base font-black text-slate-900 dark:text-white leading-tight break-words block mt-0.5">
                                            —
                                        </strong>
                                    </div>
                                    <div>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase block leading-none">NIK / Identitas Resmi</span>
                                        <span id="modal-nik" class="text-xs font-mono font-bold text-slate-700 dark:text-slate-300 break-all block mt-0.5">
                                            —
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase block leading-none">Kategori & Event</span>
                                        <span id="modal-kategori-event" class="text-xs font-extrabold text-[#F05423] leading-tight break-words block mt-0.5">
                                            —
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <p class="text-[10px] sm:text-[11px] text-blue-800 dark:text-blue-200 font-semibold bg-blue-100/70 dark:bg-blue-500/20 px-3 py-1.5 rounded-xl leading-snug flex items-center gap-1.5">
                                <span class="shrink-0">⚠️</span>
                                <span>Cocokkan wajah & nama peserta dengan KTP/KIA fisik sebelum serah terima.</span>
                            </p>
                        </div>

                        {{-- Pengambilan Diwakilkan Accordion --}}
                        <div class="bg-amber-50/90 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 rounded-2xl p-3 sm:p-3.5 space-y-2.5">
                            <label class="flex items-center gap-2.5 cursor-pointer select-none">
                                <input type="checkbox" id="modal-diwakilkan" onchange="toggleDelegationForm()"
                                    class="w-4 h-4 rounded border-2 border-amber-400 text-amber-600 focus:ring-amber-500 cursor-pointer">
                                <span class="text-xs font-bold text-amber-900 dark:text-amber-200 leading-tight">
                                    Pengambilan Diwakilkan (Wajib Surat Kuasa + KTP Perwakilan)
                                </span>
                            </label>

                            <div id="delegation-form" class="hidden space-y-2.5 pt-2.5 border-t border-amber-200 dark:border-amber-500/30">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                    <div>
                                        <label class="text-[9px] font-bold text-amber-900 dark:text-amber-300 uppercase tracking-wider block mb-1">
                                            Nama Perwakilan (Sesuai KTP)
                                        </label>
                                        <input type="text" id="modal-nama-pengambil"
                                            class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-amber-300 dark:border-amber-500/30 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-amber-500 outline-none font-semibold"
                                            placeholder="Nama pengambil...">
                                    </div>
                                    <div>
                                        <label class="text-[9px] font-bold text-amber-900 dark:text-amber-300 uppercase tracking-wider block mb-1">
                                            NIK Perwakilan (16 Digit)
                                        </label>
                                        <input type="text" id="modal-nik-pengambil" maxlength="16"
                                            class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-amber-300 dark:border-amber-500/30 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-amber-500 outline-none font-mono font-bold"
                                            placeholder="320xxxxxxxxxxxxx">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Catatan Tambahan (Opsional) --}}
                        <div>
                            <label class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">
                                Catatan Tambahan Marshal (Opsional)
                            </label>
                            <input type="text" id="modal-catatan"
                                class="w-full px-3.5 py-2 sm:py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 outline-none"
                                placeholder="Kondisi jersey, medali, atau catatan khusus...">
                        </div>

                    </div>

                </div>

            </div>

            {{-- 3. PINNED FOOTER (shrink-0: SELALU TERDOCKING DI BAWAH, TIDAK PERNAH TERPOTONG SCREEN) --}}
            <div class="shrink-0 bg-slate-50 dark:bg-[#07131f] border-t border-slate-200 dark:border-white/10 p-3 sm:p-4 px-4 sm:px-6 flex items-center gap-2.5 sm:gap-3 z-10 shadow-lg">
                <button type="button" onclick="closeConfirmModal()"
                    class="w-28 sm:w-36 py-3 px-3 bg-slate-200 hover:bg-slate-300 dark:bg-white/10 dark:hover:bg-white/15 text-slate-700 dark:text-slate-300 font-extrabold text-xs uppercase tracking-wider rounded-xl transition-all border border-slate-300 dark:border-white/10 shrink-0 text-center">
                    ✕ Batal
                </button>
                <button type="button" onclick="submitConfirmation()" id="btn-confirm-handover"
                    class="flex-1 py-3 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs sm:text-sm uppercase tracking-wider rounded-xl shadow-lg shadow-emerald-500/25 transition-all flex items-center justify-center gap-2 active:scale-[0.98]">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    <span class="truncate">Konfirmasi Serah Terima</span>
                </button>
            </div>

            {{-- Hidden Field for Pendaftaran ID --}}
            <input type="hidden" id="modal-id-pendaftaran" value="">
        </div>
    </div>

</div>
@endsection

@push('scripts')
{{-- Html5Qrcode Library --}}
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
    const qrInput = document.getElementById('qr-input');
    const historyList = document.getElementById('history-list');
    const historyCountEl = document.getElementById('history-count');
    const cameraPrompt = document.getElementById('camera-prompt');
    const cameraPlaceholder = document.getElementById('camera-placeholder');
    const cameraStatusBadge = document.getElementById('camera-status-badge');
    const alertContainer = document.getElementById('scan-alert-container');
    const btnSwitchCamera = document.getElementById('btn-switch-camera');
    const btnStopCamera = document.getElementById('btn-stop-camera');

    let html5QrCode = null;
    let isCameraRunning = false;
    let isScanningPaused = false;
    let isProcessing = false;
    let currentFacingMode = 'environment';
    let lastScannedToken = "";
    let historyCounter = 0;

    // ================================================================
    // SCAN SUCCESS HANDLER (DEBOUNCED & PAUSES CAMERA)
    // ================================================================
    function onScanSuccess(decodedText) {
        if (isScanningPaused || isProcessing) return;
        if (decodedText === lastScannedToken) return;

        isScanningPaused = true;
        lastScannedToken = decodedText;
        qrInput.value = decodedText;

        if (cameraStatusBadge) {
            cameraStatusBadge.innerText = 'Memproses...';
            cameraStatusBadge.className = 'text-[11px] font-bold bg-amber-500/20 text-amber-300 px-3 py-1.5 rounded-xl border border-amber-500/40';
        }

        // Jalankan verifikasi ke backend
        scanQR(decodedText);
    }

    // ================================================================
    // CAMERA CONTROLS (START / STOP / SWITCH)
    // ================================================================
    function confirmStartCamera() {
        const btn = document.getElementById('btn-activate-camera');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg> Memulai Kamera...';
        }
        startCamera(currentFacingMode);
    }

    function startCamera(facingMode) {
        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("reader");
        }

        const config = {
            fps: 15,
            aspectRatio: 1.0,
        };

        html5QrCode.start(
            { facingMode: facingMode },
            config,
            onScanSuccess
        ).then(() => {
            isCameraRunning = true;
            cameraPrompt.classList.add('hidden');
            cameraPlaceholder.classList.add('hidden');
            cameraStatusBadge.innerText = 'Kamera Aktif (' + (facingMode === 'environment' ? 'Belakang' : 'Depan') + ')';
            cameraStatusBadge.className = 'text-[11px] font-bold bg-emerald-500/20 text-emerald-300 px-3 py-1.5 rounded-xl border border-emerald-500/40';

            btnSwitchCamera.classList.remove('hidden');
            btnSwitchCamera.classList.add('inline-flex');
            btnStopCamera.classList.remove('hidden');
            btnStopCamera.classList.add('inline-flex');
        }).catch(err => {
            console.warn("Gagal membuka kamera dengan facingMode:", facingMode, err);
            // Fallback ke kamera default perangkat jika facingMode spesifik ditolak
            if (facingMode === 'environment') {
                startCamera('user');
                return;
            }
            cameraPrompt.classList.add('hidden');
            cameraPlaceholder.classList.remove('hidden');
            cameraStatusBadge.innerText = 'Kamera Gagal';
            cameraStatusBadge.className = 'text-[11px] font-bold bg-rose-500/20 text-rose-300 px-3 py-1.5 rounded-xl border border-rose-500/40';
        });
    }

    async function stopCamera() {
        if (html5QrCode && isCameraRunning) {
            try {
                await html5QrCode.stop();
                html5QrCode = null;
                isCameraRunning = false;
            } catch (err) {
                console.warn("Error stopping camera:", err);
                html5QrCode = null;
                isCameraRunning = false;
            }
        }
        btnSwitchCamera.classList.add('hidden');
        btnSwitchCamera.classList.remove('inline-flex');
        btnStopCamera.classList.add('hidden');
        btnStopCamera.classList.remove('inline-flex');
        cameraStatusBadge.innerText = 'Kamera Nonaktif';
        cameraStatusBadge.className = 'text-[11px] font-bold bg-white/10 text-slate-400 px-3 py-1.5 rounded-xl border border-white/15';
        showCameraPrompt();
    }

    async function switchCamera() {
        currentFacingMode = currentFacingMode === 'environment' ? 'user' : 'environment';
        if (html5QrCode && isCameraRunning) {
            try {
                await html5QrCode.stop();
                html5QrCode = null;
                isCameraRunning = false;
            } catch (err) {
                console.warn(err);
            }
            startCamera(currentFacingMode);
        }
    }

    function showCameraPrompt() {
        cameraPlaceholder.classList.add('hidden');
        cameraPrompt.classList.remove('hidden');
        const btn = document.getElementById('btn-activate-camera');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z"/></svg> Mulai Scan Kamera';
        }
    }

    // ================================================================
    // SCAN VERIFICATION API REQUEST
    // ================================================================
    qrInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            scanQR();
        }
    });

    async function scanQR(forcedToken = null) {
        if (isProcessing) return;

        const token = forcedToken || qrInput.value.trim();
        if (!token) {
            showAlertBanner('merah', 'TIDAK VALID', 'Masukkan atau arahkan kamera ke QR Code / Nomor BIB terlebih dahulu.');
            return;
        }

        isProcessing = true;
        const scanBtn = document.getElementById('scan-btn');
        if (scanBtn) {
            scanBtn.disabled = true;
            scanBtn.innerText = 'MEMPROSES...';
        }

        try {
            const response = await fetch('/api/racepack/scan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ qr_code_token: token }),
            });

            const data = await response.json();

            if (data.indicator === 'hijau') {
                // TIKET VALID & LUNAS -> BEKUKAN KAMERA DAN BUKA MODAL KONFIRMASI SECARA OTOMATIS
                alertContainer.classList.add('hidden');
                openConfirmModalWithData(data.data);
            } else {
                // TIKET SUDAH DIAMBIL ATAU TIDAK VALID -> TAMPILKAN BANNER PERINGATAN
                showAlertBanner(data.indicator, data.status.toUpperCase(), data.message, data.data);
                addToHistory(data, token);
                resumeCameraAfterCooldown(3000);
            }

        } catch (error) {
            showAlertBanner('merah', 'ERROR KONEKSI', 'Gagal terhubung ke server. Periksa koneksi jaringan terminal.');
            resumeCameraAfterCooldown(2500);
        } finally {
            isProcessing = false;
            if (scanBtn) {
                scanBtn.disabled = false;
                scanBtn.innerText = 'VERIFIKASI';
            }
            if (!forcedToken) {
                qrInput.value = '';
            }
        }
    }

    // ================================================================
    // MODAL KONFIRMASI DENGAN HIERARKI KONTRAS TINGGI
    // ================================================================
    function openConfirmModalWithData(data) {
        document.getElementById('modal-id-pendaftaran').value = data.id_pendaftaran;
        document.getElementById('modal-bib-number').innerText = data.bib_number || 'BELUM TERBIT';
        document.getElementById('modal-jersey-size').innerText = data.ukuran_jersey || '-';
        document.getElementById('modal-nama-runner').innerText = data.nama_runner || '-';
        document.getElementById('modal-nik').innerText = data.nik ? 'NIK: ' + data.nik : 'NIK: Belum diisi peserta';
        document.getElementById('modal-kategori-event').innerText = (data.kategori || '') + ' · ' + (data.nama_event || '');

        // Foto Identitas (Rasio Standar KTP 16:10 dengan Efek Zoom Interaktif)
        const fotoWrapper = document.getElementById('modal-foto-wrapper');
        if (data.foto_identitas_url) {
            fotoWrapper.innerHTML = `
                <div class="relative group cursor-pointer" onclick="window.open('${data.foto_identitas_url}', '_blank')" title="Klik untuk membuka foto asli resolusi penuh di tab baru">
                    <img src="${data.foto_identitas_url}" alt="Foto KTP/KIA"
                         class="w-28 h-20 sm:w-36 sm:h-24 aspect-[16/10] object-cover rounded-xl border-2 border-slate-300 dark:border-white/20 shadow-md group-hover:border-[#F05423] transition-all">
                    <span class="absolute bottom-1 right-1 bg-slate-950/80 text-white text-[8px] sm:text-[9px] font-bold px-1.5 py-0.5 rounded backdrop-blur-sm opacity-90 group-hover:opacity-100 transition-opacity flex items-center gap-0.5 shadow">
                        🔍 Perbesar
                    </span>
                </div>
            `;
        } else {
            fotoWrapper.innerHTML = `
                <div class="w-28 h-20 sm:w-36 sm:h-24 aspect-[16/10] rounded-xl bg-slate-100 dark:bg-white/5 border-2 border-dashed border-slate-300 dark:border-white/15 flex flex-col items-center justify-center text-center p-1.5">
                    <span class="text-base">🪪</span>
                    <span class="text-[9px] text-slate-400 font-bold leading-tight mt-0.5">KTP/KIA<br>Belum Ada</span>
                </div>
            `;
        }

        // Reset delegation form
        document.getElementById('modal-diwakilkan').checked = false;
        document.getElementById('delegation-form').classList.add('hidden');
        document.getElementById('modal-nama-pengambil').value = '';
        document.getElementById('modal-nik-pengambil').value = '';
        document.getElementById('modal-catatan').value = '';

        // Tampilkan Modal
        document.getElementById('confirm-modal').classList.remove('hidden');
    }

    function closeConfirmModal() {
        document.getElementById('confirm-modal').classList.add('hidden');
        qrInput.value = '';
        qrInput.focus();
        resumeCameraAfterCooldown(1500);
    }

    // Keyboard shortcut ESC untuk menutup modal konfirmasi
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modal = document.getElementById('confirm-modal');
            if (modal && !modal.classList.contains('hidden')) {
                closeConfirmModal();
            }
        }
    });

    function toggleDelegationForm() {
        const isChecked = document.getElementById('modal-diwakilkan').checked;
        document.getElementById('delegation-form').classList.toggle('hidden', !isChecked);
    }

    async function submitConfirmation() {
        const btn = document.getElementById('btn-confirm-handover');
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg> Menyimpan...';

        const isDiwakilkan = document.getElementById('modal-diwakilkan').checked;
        const payload = {
            id_pendaftaran: parseInt(document.getElementById('modal-id-pendaftaran').value),
            is_diwakilkan: isDiwakilkan,
            nama_pengambil: isDiwakilkan ? document.getElementById('modal-nama-pengambil').value : null,
            nik_pengambil: isDiwakilkan ? document.getElementById('modal-nik-pengambil').value : null,
            catatan_rpc: document.getElementById('modal-catatan').value || null,
        };

        try {
            const response = await fetch('/api/racepack/confirm', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify(payload),
            });

            const data = await response.json();

            if (response.ok) {
                closeConfirmModal();
                showAlertBanner('hijau', 'SUKSES', data.message || 'Racepack berhasil diserahkan kepada peserta!', data.data);
                addToHistory(data);
            } else {
                alert('Gagal: ' + (data.message || 'Terjadi kesalahan saat konfirmasi.'));
            }

        } catch (error) {
            alert('Gagal menghubungi server: ' + error.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg> Konfirmasi Serah Terima Racepack';
        }
    }

    // ================================================================
    // ALERT BANNER (UNTUK HASIL SCAN & FEEDBACK)
    // ================================================================
    function showAlertBanner(indicator, title, message, data = null) {
        alertContainer.classList.remove('hidden');

        const styles = {
            hijau: {
                bg: 'bg-emerald-500/15 border-2 border-emerald-500 text-emerald-300',
                icon: '✅',
                titleColor: 'text-emerald-400',
            },
            kuning: {
                bg: 'bg-amber-500/15 border-2 border-amber-500 text-amber-300',
                icon: '⚠️',
                titleColor: 'text-amber-400',
            },
            merah: {
                bg: 'bg-rose-500/15 border-2 border-rose-500 text-rose-300',
                icon: '❌',
                titleColor: 'text-rose-400',
            },
        };

        const s = styles[indicator] || styles.merah;

        let extraDetails = '';
        if (data && indicator === 'kuning') {
            extraDetails = `
                <div class="mt-2 text-xs font-mono opacity-90 border-t border-amber-500/30 pt-2">
                    BIB: <strong>${data.bib_number || '-'}</strong> · Peserta: <strong>${data.nama_runner || '-'}</strong>
                    ${data.waktu_pengambilan ? '<br>Waktu: ' + data.waktu_pengambilan : ''}
                    ${data.is_diwakilkan ? '<br>Diambilkan oleh: ' + (data.nama_pengambil || '-') : ''}
                </div>
            `;
        }

        alertContainer.innerHTML = `
            <div class="rounded-2xl p-4 ${s.bg} flex items-start gap-3 shadow-lg">
                <span class="text-2xl shrink-0">${s.icon}</span>
                <div class="min-w-0 flex-1">
                    <h4 class="text-xs font-black uppercase tracking-wider ${s.titleColor}">${title}</h4>
                    <p class="text-xs font-bold leading-relaxed mt-0.5">${message}</p>
                    ${extraDetails}
                </div>
                <button type="button" onclick="alertContainer.classList.add('hidden')" class="text-white/60 hover:text-white text-sm font-black p-1">✕</button>
            </div>
        `;

        alertContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function resumeCameraAfterCooldown(ms) {
        setTimeout(() => {
            isScanningPaused = false;
            lastScannedToken = "";
            if (cameraStatusBadge && isCameraRunning) {
                cameraStatusBadge.innerText = 'Kamera Aktif (' + (currentFacingMode === 'environment' ? 'Belakang' : 'Depan') + ')';
                cameraStatusBadge.className = 'text-[11px] font-bold bg-emerald-500/20 text-emerald-300 px-3 py-1.5 rounded-xl border border-emerald-500/40';
            }
        }, ms);
    }

    // ================================================================
    // HISTORY LOG HANDLER
    // ================================================================
    function addToHistory(data, inputToken = '') {
        const placeholder = historyList.querySelector('p.italic');
        if (placeholder) placeholder.remove();

        historyCounter++;
        historyCountEl.innerText = historyCounter + ' Tiket';

        const time = new Date().toLocaleTimeString('id-ID');
        const bib = data.data?.bib_number || 'N/A';
        const runner = data.data?.nama_runner || 'Peserta';
        const indicator = data.indicator || 'merah';

        const badgeColors = {
            hijau: 'border-emerald-500/40 bg-emerald-500/10 text-emerald-300',
            kuning: 'border-amber-500/40 bg-amber-500/10 text-amber-300',
            merah: 'border-rose-500/40 bg-rose-500/10 text-rose-300',
        };

        const item = document.createElement('div');
        item.className = `p-3 rounded-xl border ${badgeColors[indicator] || badgeColors.merah} flex items-center justify-between gap-3 text-xs`;
        item.innerHTML = `
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2">
                    <strong class="font-mono text-sm font-black text-white">${bib}</strong>
                    <span class="text-[10px] font-black uppercase tracking-wider px-1.5 py-0.5 rounded bg-black/40">
                        ${indicator.toUpperCase()}
                    </span>
                </div>
                <p class="text-xs text-slate-300 truncate mt-0.5 font-medium">${runner}</p>
            </div>
            <span class="text-[10px] font-mono text-slate-400 shrink-0">${time}</span>
        `;

        historyList.insertBefore(item, historyList.firstChild);
    }
</script>
@endpush