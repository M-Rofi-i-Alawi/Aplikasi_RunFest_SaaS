@extends('layouts.app')

@section('title', 'Scanner Racepack Marshal')

@push('styles')
<style>
    /* Styling khusus agar video camera stream dari html5-qrcode memenuhi container 100% */
    #reader {
        border: none !important;
        background: #090d16 !important;
        position: relative;
        overflow: hidden !important;
        width: 100% !important;
    }

    #reader video {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        border-radius: 1rem;
    }

    #reader__scan_region {
        width: 100% !important;
        height: 100% !important;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden !important;
    }

    #reader__scan_region video {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
    }

    /* Animasi Laser Line Scanning */
    @keyframes scan-laser {
        0% { top: 10%; opacity: 0.8; }
        50% { top: 90%; opacity: 1; }
        100% { top: 10%; opacity: 0.8; }
    }

    .laser-line {
        position: absolute;
        left: 8%;
        right: 8%;
        height: 2.5px;
        background: linear-gradient(90deg, transparent, #ff5500, #3b82f6, #ff5500, transparent);
        box-shadow: 0 0 15px #ff5500, 0 0 8px #3b82f6;
        animation: scan-laser 2.2s ease-in-out infinite;
        z-index: 20;
        pointer-events: none;
    }
</style>
@endpush

@section('content')
<div class="max-w-xl mx-auto px-4 py-8 space-y-6">

    {{-- HEADER --}}
    <div class="text-center space-y-1">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-[#ff5500] to-orange-400 text-white flex items-center justify-center mx-auto mb-2 shadow-lg shadow-orange-500/20 font-bold">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Scanner Racepack</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Arahkan kamera ke QR Code E-Ticket atau ketik token secara manual.</p>
    </div>

    {{-- OPSI 1: SCANNER KAMERA LIVE --}}
    <div class="glass-card bg-white dark:bg-[#0b1329]/80 p-5 rounded-3xl border border-slate-200 dark:border-white/15 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-white/10 pb-3">
            <span class="text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-wider flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping inline-block"></span>
                Opsi 1: Kamera Live Scan
            </span>
            <div class="flex items-center gap-2">
                {{-- Tombol Matikan Kamera (hanya muncul saat kamera aktif) --}}
                <button id="btn-stop-camera" onclick="stopCamera()"
                    class="hidden items-center gap-1.5 px-3 py-1.5 rounded-xl text-[11px] font-extrabold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 hover:bg-rose-100 dark:hover:bg-rose-500/20 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 0 1 7.5 5.25h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25h-9a2.25 2.25 0 0 1-2.25-2.25v-9Z"/>
                    </svg>
                    Matikan
                </button>
                <span id="camera-status-badge" class="text-[11px] font-bold bg-slate-100 dark:bg-white/10 text-slate-500 dark:text-slate-400 px-2.5 py-1 rounded-xl border border-slate-200 dark:border-white/20">
                    Kamera Nonaktif
                </span>
            </div>
        </div>

        {{-- Container Kamera Viewport --}}
        <div class="relative w-full h-72 sm:h-80 bg-[#090d16] rounded-2xl overflow-hidden shadow-inner border border-slate-800 flex items-center justify-center">
            
            {{-- Target Reticle Frame Overlay --}}
            <div class="absolute inset-0 pointer-events-none z-10 flex items-center justify-center p-6">
                <div class="w-48 h-48 sm:w-56 sm:h-56 relative border-2 border-white/20 rounded-2xl shadow-[0_0_0_9999px_rgba(9,13,22,0.45)]">
                    {{-- 4 Corner Brackets --}}
                    <div class="absolute -top-1 -left-1 w-6 h-6 border-t-4 border-l-4 border-[#ff5500] rounded-tl-lg"></div>
                    <div class="absolute -top-1 -right-1 w-6 h-6 border-t-4 border-r-4 border-[#ff5500] rounded-tr-lg"></div>
                    <div class="absolute -bottom-1 -left-1 w-6 h-6 border-b-4 border-l-4 border-[#ff5500] rounded-bl-lg"></div>
                    <div class="absolute -bottom-1 -right-1 w-6 h-6 border-b-4 border-r-4 border-[#ff5500] rounded-br-lg"></div>

                    {{-- Laser Line Animation --}}
                    <div class="laser-line"></div>
                </div>
            </div>

            {{-- Element Reader Kamera Live --}}
            <div id="reader" class="w-full h-full"></div>

            {{-- Overlay Konfirmasi Sebelum Kamera Aktif --}}
            <div id="camera-prompt" class="absolute inset-0 z-30 flex flex-col items-center justify-center p-6 text-center bg-[#090d16]/95 backdrop-blur-sm space-y-5 rounded-2xl">
                {{-- Ikon Kamera --}}
                <div class="w-16 h-16 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                    </svg>
                </div>

                {{-- Teks Konfirmasi --}}
                <div class="space-y-1.5">
                    <h3 class="text-base font-extrabold text-white">Aktifkan Kamera?</h3>
                    <p class="text-xs text-slate-400 max-w-[220px] leading-relaxed">Kamera akan digunakan untuk memindai QR Code E-Ticket peserta secara langsung.</p>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex flex-col gap-2.5 w-full max-w-[220px]">
                    <button onclick="confirmStartCamera()" id="btn-activate-camera"
                        class="w-full py-3 bg-gradient-to-r from-[#ff5500] to-orange-400 hover:from-[#e64d00] hover:to-[#ff5500] text-white text-sm font-extrabold rounded-2xl shadow-lg shadow-orange-500/25 transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z"/>
                        </svg>
                        Ya, Aktifkan Kamera
                    </button>
                    <button onclick="dismissCamera()"
                        class="w-full py-2.5 bg-white/10 hover:bg-white/15 text-slate-300 hover:text-white text-xs font-semibold rounded-2xl border border-white/15 transition-all">
                        Tidak, Gunakan Input Manual
                    </button>
                </div>
            </div>

            {{-- Placeholder saat Kamera Gagal / Ditolak (hidden by default) --}}
            <div id="camera-placeholder" class="hidden absolute inset-0 z-20 flex flex-col items-center justify-center p-6 text-center bg-slate-900/95 text-white space-y-3 rounded-2xl">
                <svg class="w-10 h-10 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <p class="text-sm font-bold text-white">Kamera Tidak Dapat Diakses</p>
                <p class="text-xs text-slate-400 max-w-xs">Periksa izin kamera di browser Anda, atau gunakan Opsi 2 untuk input manual.</p>
                <button onclick="showCameraPrompt()"
                    class="px-4 py-2 bg-[#ff5500] hover:bg-[#e64d00] text-white text-xs font-extrabold rounded-xl shadow-md transition-all">
                    Coba Lagi
                </button>
            </div>

        </div>
    </div>

    {{-- OPSI 2: INPUT MANUAL / BARCODE GUN --}}
    <div class="glass-card bg-slate-50 dark:bg-[#0b1329]/50 p-5 rounded-3xl border border-slate-200 dark:border-white/15 space-y-3">
        <span class="text-xs font-extrabold text-slate-600 dark:text-slate-300 uppercase tracking-wider block">
            Opsi 2: Ketik Manual / Barcode Scanner Gun
        </span>

        <div class="flex gap-2">
            <input type="text" id="qr-input"
                class="flex-1 px-4 py-3 bg-white dark:bg-white/10 border border-slate-300 dark:border-white/20 rounded-2xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 text-sm focus:ring-2 focus:ring-[#ff5500] outline-none font-mono font-bold"
                placeholder="BIB (5K-0001), Kode TRX, atau QR Token..."
                autofocus>
            <button onclick="scanQR()" id="scan-btn"
                class="btn-brand-orange text-white font-extrabold px-5 py-3 rounded-2xl text-xs uppercase tracking-wider transition-all shadow-md flex-shrink-0">
                VERIFIKASI
            </button>
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Bisa input: <span class="font-bold text-slate-700 dark:text-slate-300">Nomor BIB</span> (5K-0001) · <span class="font-bold text-slate-700 dark:text-slate-300">Kode TRX</span> (TRX-...) · <span class="font-bold text-slate-700 dark:text-slate-300">QR Token</span> — lalu tekan Enter atau klik Verifikasi.</p>
    </div>

    {{-- High-Contrast Result Display --}}
    <div id="scan-result" class="hidden">
        {{-- Filled dynamically by JS --}}
    </div>

    {{-- MODAL KONFIRMASI SERAH TERIMA RACEPACK --}}
    <div id="confirm-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeConfirmModal()"></div>
        
        {{-- Modal Content --}}
        <div class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto border border-slate-200 dark:border-white/15">
            {{-- Modal Header --}}
            <div class="bg-emerald-600 text-white px-5 py-4 rounded-t-3xl">
                <h3 class="font-black text-sm uppercase tracking-wider flex items-center gap-2">
                    ✅ Konfirmasi Serah Terima Racepack
                </h3>
            </div>

            {{-- Modal Body --}}
            <div class="p-5 space-y-4">
                {{-- Info Peserta (diisi via JS) --}}
                <div id="modal-peserta-info" class="space-y-3"></div>

                {{-- Toggle Diwakilkan --}}
                <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 rounded-2xl p-4 space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" id="modal-diwakilkan" onchange="toggleDelegationForm()" 
                            class="w-5 h-5 rounded-lg border-2 border-amber-400 text-amber-600 focus:ring-amber-500 cursor-pointer">
                        <span class="text-sm font-bold text-amber-900 dark:text-amber-200">Pengambilan Diwakilkan (Surat Kuasa)</span>
                    </label>
                    
                    <div id="delegation-form" class="hidden space-y-3 pt-2 border-t border-amber-200 dark:border-amber-500/30">
                        <div>
                            <label class="text-xs font-bold text-amber-900 dark:text-amber-300 uppercase tracking-wider block mb-1">Nama Pengambil (Sesuai KTP)</label>
                            <input type="text" id="modal-nama-pengambil" 
                                class="w-full px-4 py-2.5 bg-white dark:bg-white/10 border border-amber-300 dark:border-amber-500/30 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-amber-500 outline-none font-semibold"
                                placeholder="Masukkan nama perwakilan...">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-amber-900 dark:text-amber-300 uppercase tracking-wider block mb-1">NIK Pengambil (16 Digit)</label>
                            <input type="text" id="modal-nik-pengambil" maxlength="16" 
                                class="w-full px-4 py-2.5 bg-white dark:bg-white/10 border border-amber-300 dark:border-amber-500/30 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-amber-500 outline-none font-mono font-bold tracking-wider"
                                placeholder="3201234567890001">
                        </div>
                    </div>
                </div>

                {{-- Catatan RPC (Opsional) --}}
                <div>
                    <label class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider block mb-1">Catatan (Opsional)</label>
                    <textarea id="modal-catatan" rows="2" 
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/15 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 outline-none resize-none"
                        placeholder="Catatan tambahan untuk Marshal..."></textarea>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="px-5 pb-5 flex gap-3">
                <button onclick="closeConfirmModal()" 
                    class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 dark:bg-white/10 dark:hover:bg-white/15 text-slate-700 dark:text-slate-300 font-bold text-xs uppercase tracking-wider rounded-2xl border border-slate-200 dark:border-white/15 transition-all">
                    Batal
                </button>
                <button onclick="submitConfirmation()" id="btn-confirm-handover"
                    class="flex-1 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-emerald-500/25 transition-all flex items-center justify-center gap-2">
                    ✓ Konfirmasi Serahkan
                </button>
            </div>

            {{-- Hidden field --}}
            <input type="hidden" id="modal-id-pendaftaran" value="">
        </div>
    </div>

    {{-- History Log --}}
    <div id="scan-history" class="glass-card bg-white dark:bg-[#0b1329]/80 rounded-3xl border border-slate-200 dark:border-white/15 p-5 space-y-3">
        <h3 class="text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Riwayat Hasil Scan</h3>
        <div id="history-list" class="space-y-2 text-sm">
            <p class="text-xs text-slate-400 italic">Belum ada riwayat scan pada sesi ini.</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Library Html5Qrcode untuk Kamera Live --}}
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
    const qrInput = document.getElementById('qr-input');
    const scanResult = document.getElementById('scan-result');
    const historyList = document.getElementById('history-list');
    const cameraPlaceholder = document.getElementById('camera-placeholder');
    const cameraStatusBadge = document.getElementById('camera-status-badge');

    let html5QrCode = null;
    let lastScannedToken = "";

    function onScanSuccess(decodedText, decodedResult) {
        // Mencegah scan ganda berturut-turut untuk token yang sama dalam waktu singkat
        if (decodedText === lastScannedToken) return;
        
        lastScannedToken = decodedText;
        qrInput.value = decodedText;

        // Reset debounce token setelah 3 detik
        setTimeout(() => { lastScannedToken = ""; }, 3000);

        // Verifikasi token ke server
        scanQR();
    }

    function startCamera() {
        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("reader");
        }

        const config = { 
            fps: 15, 
            qrbox: { width: 220, height: 220 },
            aspectRatio: 1.0
        };

        const cameraPrompt = document.getElementById('camera-prompt');

        // Utamakan kamera belakang (environment)
        html5QrCode.start(
            { facingMode: "environment" },
            config,
            onScanSuccess
        ).then(() => {
            cameraPrompt.classList.add('hidden');
            cameraPlaceholder.classList.add('hidden');
            cameraStatusBadge.innerText = 'Kamera Aktif';
            cameraStatusBadge.className = 'text-[11px] font-bold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 px-2.5 py-1 rounded-xl border border-emerald-200 dark:border-emerald-500/30';
            showStopButton();
        }).catch(err => {
            console.warn("Kamera belakang tidak tersedia, mencoba kamera bawaan...", err);
            html5QrCode.start(
                { facingMode: "user" },
                config,
                onScanSuccess
            ).then(() => {
                cameraPrompt.classList.add('hidden');
                cameraPlaceholder.classList.add('hidden');
                cameraStatusBadge.innerText = 'Kamera Aktif';
                cameraStatusBadge.className = 'text-[11px] font-bold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 px-2.5 py-1 rounded-xl border border-emerald-200 dark:border-emerald-500/30';
                showStopButton();
            }).catch(err2 => {
                console.error("Gagal memulai kamera:", err2);
                cameraPrompt.classList.add('hidden');
                cameraPlaceholder.classList.remove('hidden');
                cameraStatusBadge.innerText = 'Kamera Gagal';
                cameraStatusBadge.className = 'text-[11px] font-bold bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 px-2.5 py-1 rounded-xl border border-rose-200 dark:border-rose-500/30';
            });
        });
    }

    // Konfirmasi Aktifkan Kamera (dipanggil dari tombol "Ya, Aktifkan Kamera")
    function confirmStartCamera() {
        const btn = document.getElementById('btn-activate-camera');
        btn.disabled = true;
        btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg> Memulai...`;
        startCamera();
    }

    // Tolak / Tutup prompt kamera, fokus ke input manual
    function dismissCamera() {
        const cameraPrompt = document.getElementById('camera-prompt');
        cameraPrompt.classList.add('hidden');
        cameraStatusBadge.innerText = 'Tidak Diaktifkan';
        cameraStatusBadge.className = 'text-[11px] font-bold bg-slate-100 dark:bg-white/10 text-slate-500 dark:text-slate-400 px-2.5 py-1 rounded-xl border border-slate-200 dark:border-white/20';
        hideStopButton();
        document.getElementById('qr-input').focus();
    }

    // Matikan kamera yang sedang aktif
    async function stopCamera() {
        const btnStop = document.getElementById('btn-stop-camera');
        btnStop.disabled = true;
        btnStop.innerText = 'Mematikan...';

        if (html5QrCode) {
            try {
                await html5QrCode.stop();
                html5QrCode = null;
            } catch (err) {
                console.warn('Gagal menghentikan kamera:', err);
                html5QrCode = null;
            }
        }

        // Sembunyikan tombol matikan & perbarui badge
        hideStopButton();
        cameraStatusBadge.innerText = 'Kamera Nonaktif';
        cameraStatusBadge.className = 'text-[11px] font-bold bg-slate-100 dark:bg-white/10 text-slate-500 dark:text-slate-400 px-2.5 py-1 rounded-xl border border-slate-200 dark:border-white/20';

        // Tampilkan kembali overlay konfirmasi
        showCameraPrompt();
    }

    // Helper: tampilkan tombol "Matikan Kamera"
    function showStopButton() {
        const btn = document.getElementById('btn-stop-camera');
        btn.disabled = false;
        btn.classList.remove('hidden');
        btn.classList.add('inline-flex');
    }

    // Helper: sembunyikan tombol "Matikan Kamera"
    function hideStopButton() {
        const btn = document.getElementById('btn-stop-camera');
        btn.classList.add('hidden');
        btn.classList.remove('inline-flex');
    }

    // Tampilkan ulang prompt kamera (dari tombol "Coba Lagi")
    function showCameraPrompt() {
        const cameraPlaceholder = document.getElementById('camera-placeholder');
        const cameraPrompt = document.getElementById('camera-prompt');
        const btn = document.getElementById('btn-activate-camera');
        cameraPlaceholder.classList.add('hidden');
        cameraPrompt.classList.remove('hidden');
        btn.disabled = false;
        btn.innerHTML = `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z"/></svg> Ya, Aktifkan Kamera`;
    }

    // ================================================================
    // OPSI 2: Logika Input Manual & Verifikasi API
    // ================================================================
    qrInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            scanQR();
        }
    });

    async function scanQR() {
        const token = qrInput.value.trim();
        if (!token) {
            showResult('merah', 'TIDAK VALID', 'Silakan masukkan atau scan token terlebih dahulu.', null);
            return;
        }

        const scanBtn = document.getElementById('scan-btn');
        scanBtn.disabled = true;
        scanBtn.innerText = 'MEMPROSES...';

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
            showResult(data.indicator, data.status, data.message, data.data);
            addToHistory(data);

        } catch (error) {
            showResult('merah', 'ERROR', 'Gagal terhubung ke server.', null);
        } finally {
            scanBtn.disabled = false;
            scanBtn.innerText = 'VERIFIKASI';
            qrInput.value = '';
            qrInput.focus();
        }
    }

    function showResult(indicator, status, message, data) {
        scanResult.classList.remove('hidden');

        const styles = {
            hijau: {
                headerBg: 'bg-emerald-600',
                bodyBg: 'bg-emerald-50 dark:bg-emerald-950/40 border-2 border-emerald-500',
                badgeText: 'TIKET VALID — SIAP KONFIRMASI',
                textColor: 'text-emerald-900 dark:text-emerald-200',
                icon: '✅',
            },
            kuning: {
                headerBg: 'bg-amber-500',
                bodyBg: 'bg-amber-50 dark:bg-amber-950/40 border-2 border-amber-500',
                badgeText: 'SUDAH DIAMBIL SEBELUMNYA',
                textColor: 'text-amber-900 dark:text-amber-200',
                icon: '⚠️',
            },
            merah: {
                headerBg: 'bg-rose-600',
                bodyBg: 'bg-rose-50 dark:bg-rose-950/40 border-2 border-rose-500',
                badgeText: 'TIDAK VALID / BELUM LUNAS',
                textColor: 'text-rose-900 dark:text-rose-200',
                icon: '❌',
            },
        };

        const s = styles[indicator] || styles.merah;

        let detailsHtml = '';
        if (data) {
            // Blok Identitas Peserta (Foto + NIK) — untuk verifikasi anti-joki
            let identityHtml = '';
            if (data.foto_identitas_url || data.nik) {
                identityHtml = `
                    <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border-2 border-blue-200 dark:border-blue-500/30 shadow-sm">
                        <p class="text-[11px] font-extrabold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z"/>
                            </svg>
                            VERIFIKASI IDENTITAS PESERTA
                        </p>
                        <div class="flex items-start gap-4">
                            ${data.foto_identitas_url ? `
                                <img src="${data.foto_identitas_url}" alt="Foto Identitas" 
                                    class="w-24 h-16 sm:w-28 sm:h-20 object-cover rounded-xl border-2 border-slate-300 dark:border-white/20 shadow-md flex-shrink-0 cursor-pointer"
                                    onclick="this.classList.toggle('w-24'); this.classList.toggle('h-16'); this.classList.toggle('sm:w-28'); this.classList.toggle('sm:h-20'); this.classList.toggle('w-full'); this.classList.toggle('h-auto');"
                                    title="Klik untuk perbesar/perkecil">
                            ` : `
                                <div class="w-24 h-16 rounded-xl border-2 border-dashed border-slate-300 dark:border-white/20 bg-slate-100 dark:bg-white/5 flex items-center justify-center flex-shrink-0">
                                    <span class="text-[10px] text-slate-400 text-center leading-tight">Foto<br>Belum<br>Diunggah</span>
                                </div>
                            `}
                            <div class="flex-1 space-y-1.5">
                                <div>
                                    <span class="text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase block">NAMA PESERTA</span>
                                    <strong class="text-sm text-slate-900 dark:text-white">${data.nama_runner || '-'}</strong>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase block">NIK / NISN</span>
                                    <strong class="text-sm text-slate-900 dark:text-white font-mono tracking-wider">${data.nik || '<span class=&quot;text-slate-400 italic font-normal&quot;>Belum diisi</span>'}</strong>
                                </div>
                            </div>
                        </div>
                        <p class="mt-3 text-[11px] text-blue-600 dark:text-blue-400 font-semibold bg-blue-50 dark:bg-blue-500/10 px-3 py-1.5 rounded-lg text-center">
                            ⚠️ Cocokkan wajah & KTP fisik peserta sebelum menyerahkan racepack
                        </p>
                    </div>
                `;
            }

            detailsHtml = `
                <div class="mt-4 pt-4 border-t border-slate-300 dark:border-white/20 space-y-3">
                    ${identityHtml}
                    ${data.bib_number ? `
                        <div class="bg-white dark:bg-slate-900 p-3 rounded-xl border border-slate-300 dark:border-white/20 text-center shadow-sm">
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-extrabold uppercase block">NOMOR BIB PESERTA</span>
                            <span class="text-4xl font-black text-slate-900 dark:text-white font-mono tracking-tight">${data.bib_number}</span>
                        </div>
                    ` : ''}
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        ${data.ukuran_jersey ? `<div class="bg-white dark:bg-slate-900 p-2.5 rounded-xl border border-slate-200 dark:border-white/10"><span class="text-slate-500 dark:text-slate-400 font-bold block">UKURAN JERSEY</span><strong class="text-[#ff5500] text-base font-extrabold">${data.ukuran_jersey}</strong></div>` : ''}
                        ${data.kategori ? `<div class="bg-white dark:bg-slate-900 p-2.5 rounded-xl border border-slate-200 dark:border-white/10"><span class="text-slate-500 dark:text-slate-400 font-bold block">KATEGORI</span><strong class="text-slate-900 dark:text-white">${data.kategori}</strong></div>` : ''}
                        ${data.nama_event ? `<div class="bg-white dark:bg-slate-900 p-2.5 rounded-xl border border-slate-200 dark:border-white/10 col-span-2"><span class="text-slate-500 dark:text-slate-400 font-bold block">EVENT</span><strong class="text-slate-900 dark:text-white">${data.nama_event}</strong></div>` : ''}
                    </div>
                    ${indicator === 'hijau' ? `
                        <button onclick="openConfirmModal(${data.id_pendaftaran}, '${(data.nama_runner || '').replace(/'/g, "\\'")}', '${data.bib_number || ''}', '${data.ukuran_jersey || ''}', '${(data.kategori || '').replace(/'/g, "\\'")}')"
                            class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-sm uppercase tracking-wider rounded-2xl shadow-lg shadow-emerald-500/25 transition-all flex items-center justify-center gap-2 mt-2">
                            ✓ Konfirmasi Serah Terima Racepack
                        </button>
                    ` : ''}
                </div>
            `;
        }

        scanResult.innerHTML = `
            <div class="rounded-2xl overflow-hidden shadow-lg ${s.bodyBg}">
                <div class="${s.headerBg} text-white px-4 py-3 font-black text-sm uppercase tracking-wider flex items-center justify-between">
                    <span>${s.icon} ${s.badgeText}</span>
                </div>
                <div class="p-5">
                    <p class="font-bold text-sm ${s.textColor}">${message}</p>
                    ${detailsHtml}
                </div>
            </div>
        `;

        scanResult.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // ================================================================
    // MODAL KONFIRMASI SERAH TERIMA
    // ================================================================

    function openConfirmModal(idPendaftaran, namaRunner, bibNumber, ukuranJersey, kategori) {
        document.getElementById('modal-id-pendaftaran').value = idPendaftaran;
        
        document.getElementById('modal-peserta-info').innerHTML = `
            <div class="bg-slate-50 dark:bg-white/5 p-3 rounded-xl border border-slate-200 dark:border-white/15 text-center space-y-1">
                <span class="text-3xl font-black text-slate-900 dark:text-white font-mono">${bibNumber}</span>
                <p class="text-xs text-slate-600 dark:text-slate-400"><strong>${namaRunner}</strong> · Jersey: <strong class="text-[#ff5500]">${ukuranJersey}</strong> · ${kategori}</p>
            </div>
        `;

        // Reset form
        document.getElementById('modal-diwakilkan').checked = false;
        document.getElementById('delegation-form').classList.add('hidden');
        document.getElementById('modal-nama-pengambil').value = '';
        document.getElementById('modal-nik-pengambil').value = '';
        document.getElementById('modal-catatan').value = '';

        document.getElementById('confirm-modal').classList.remove('hidden');
    }

    function closeConfirmModal() {
        document.getElementById('confirm-modal').classList.add('hidden');
    }

    function toggleDelegationForm() {
        const isChecked = document.getElementById('modal-diwakilkan').checked;
        document.getElementById('delegation-form').classList.toggle('hidden', !isChecked);
    }

    async function submitConfirmation() {
        const btn = document.getElementById('btn-confirm-handover');
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg> Memproses...';

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

            closeConfirmModal();
            showResult(data.indicator || (response.ok ? 'hijau' : 'merah'), data.status, data.message, data.data);
            addToHistory(data);

        } catch (error) {
            alert('Gagal terhubung ke server: ' + error.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = '✓ Konfirmasi Serahkan';
        }
    }

    function addToHistory(data) {
        const time = new Date().toLocaleTimeString('id-ID');
        const placeholder = historyList.querySelector('p.italic');
        if (placeholder) placeholder.remove();

        const badgeColors = {
            hijau: 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-300 border-emerald-300 dark:border-emerald-500/30',
            kuning: 'bg-amber-50 dark:bg-amber-500/10 text-amber-800 dark:text-amber-300 border-amber-300 dark:border-amber-500/30',
            merah: 'bg-rose-50 dark:bg-rose-500/10 text-rose-800 dark:text-rose-300 border-rose-300 dark:border-rose-500/30',
        };

        const entry = document.createElement('div');
        entry.className = `p-3 rounded-xl border ${badgeColors[data.indicator] || badgeColors.merah} flex items-center justify-between font-bold text-xs`;
        entry.innerHTML = `
            <div>
                <strong class="font-mono text-sm">BIB: ${data.data?.bib_number || 'N/A'}</strong>
                <span class="ml-2 opacity-80">(${data.data?.nama_runner || '-'})</span>
                ${data.data?.is_diwakilkan ? '<span class="ml-1 text-amber-600 dark:text-amber-400">[Diwakilkan]</span>' : ''}
            </div>
            <span class="opacity-70 font-mono">${time}</span>
        `;

        historyList.insertBefore(entry, historyList.firstChild);
    }
</script>
@endpush
