@extends('layouts.app')

@section('title', 'Dashboard Ticket Runner')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    {{-- Welcome Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-white/10 pb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Dashboard Tiket Pelari
            </h1>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                Selamat datang, <span class="font-bold text-slate-900 dark:text-white">{{ auth()->user()->nama }}</span> ({{ auth()->user()->email }})
            </p>
        </div>
        <a href="{{ route('events.index') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl btn-brand-orange text-white font-extrabold text-sm shadow-md transition-all">
            + Daftar Event Baru
        </a>
    </div>

    {{-- Warning Banner jika Profil Identitas (NIK / Foto KTP) Belum Lengkap atau Akun Manual --}}
    @if(auth()->user()->isRunner() && (empty(auth()->user()->nik) || strlen(auth()->user()->nik) !== 16 || empty(auth()->user()->foto_identitas) || !auth()->user()->google_id))
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-950/40 dark:to-orange-950/30 border-2 border-amber-300 dark:border-amber-500/40 rounded-3xl p-6 shadow-sm flex flex-col lg:flex-row items-start lg:items-center justify-between gap-5">
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center font-black text-xl flex-shrink-0 shadow-lg shadow-amber-500/30">
                    ⚠️
                </div>
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="text-base font-black text-amber-950 dark:text-amber-200 uppercase tracking-tight">Peringatan: Verifikasi Identitas &amp; Akun Diperlukan</h3>
                        @if(!auth()->user()->google_id)
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300 border border-rose-200 dark:border-rose-500/30">
                                Akun Manual (Belum Terverifikasi Google)
                            </span>
                        @endif
                        @if(empty(auth()->user()->nik) || empty(auth()->user()->foto_identitas))
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30">
                                Berkas KTP Belum Diunggah
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-amber-900/80 dark:text-amber-300/90 leading-relaxed max-w-2xl">
                        Untuk mencegah <strong>akun fiktif &amp; joki</strong> di lokasi penukaran Racepack (RPC), Anda <strong>wajib mengisi NIK/NISN 16 digit dan mengunggah Foto KTP/Kartu Pelajar Asli</strong>. Jika sebelumnya mendaftar dengan email tidak resmi, disarankan untuk melengkapi data atau mendaftar kembali via Google.
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
                <a href="{{ route('account.settings') }}" class="flex-1 lg:flex-initial text-center px-5 py-3 rounded-2xl bg-[#ff5500] hover:bg-[#e64d00] text-white font-black text-xs uppercase tracking-wider transition-all shadow-md hover:scale-[1.02]">
                    Lengkapi NIK &amp; KTP Sekarang →
                </a>
            </div>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        {{-- Card 1: Total Tiket --}}
        <div class="glass-card rounded-2xl p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-white/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-slate-500 dark:text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z"/>
                </svg>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide block leading-tight">Total Tiket</span>
                <h4 class="text-3xl font-bold text-slate-800 dark:text-white leading-tight mt-0.5">{{ $pendaftaran->whereNotIn('status_pembayaran', ['Gagal'])->count() }}</h4>
                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">Pendaftaran Event</span>
            </div>
        </div>

        {{-- Card 2: Tiket Lunas --}}
        <div class="glass-card rounded-2xl p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-500/15 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
            </div>
            <div>
                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wide block leading-tight">Tiket Lunas</span>
                <h4 class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 leading-tight mt-0.5">{{ $pendaftaran->where('status_pembayaran', 'Lunas')->count() }}</h4>
                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">Pembayaran Terverifikasi</span>
            </div>
        </div>

        {{-- Card 3: Racepack Diambil --}}
        <div class="glass-card rounded-2xl p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-orange-50 dark:bg-orange-500/15 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-[#ff5500] dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/>
                </svg>
            </div>
            <div>
                <span class="text-xs font-semibold text-[#ff5500] dark:text-orange-400 uppercase tracking-wide block leading-tight">Racepack Diambil</span>
                <h4 class="text-3xl font-bold text-[#ff5500] dark:text-orange-400 leading-tight mt-0.5">{{ $pendaftaran->where('status_racepack', 'Sudah Diambil')->count() }}</h4>
                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">Sudah Konfirmasi Marshal</span>
            </div>
        </div>

    </div>


    {{-- E-Tickets List --}}
    <h2 class="text-xl font-extrabold text-slate-900 dark:text-white pt-2">Tiket & Racepack Saya</h2>

    @if($pendaftaran->count() > 0)
        <div class="space-y-6">
            @foreach($pendaftaran as $daftar)
                {{-- Clean High-Contrast Ticket Card (Dual Mode Adaptif) --}}
                <div class="glass-card rounded-3xl overflow-hidden shadow-lg border border-slate-200 dark:border-white/15">
                    
                    {{-- Ticket Top Header Bar --}}
                    <div class="bg-slate-900 dark:bg-[#0b1329] px-6 py-4 text-white flex flex-wrap items-center justify-between gap-3 border-b border-white/10">
                        <div>
                            <span class="text-xs text-orange-400 font-extrabold uppercase tracking-wider">Nama Event</span>
                            <h3 class="text-lg font-black leading-tight">{{ $daftar->event->nama_event }}</h3>
                        </div>
                        <div class="flex items-center space-x-2">
                            {{-- Payment Status Badge --}}
                            <span class="px-3 py-1 rounded-xl text-xs font-black uppercase tracking-wider
                                {{ $daftar->status_pembayaran === 'Lunas' ? 'bg-emerald-500 text-white' : ($daftar->status_pembayaran === 'Pending' ? 'bg-amber-500 text-white' : 'bg-rose-500 text-white') }}">
                                {{ $daftar->status_pembayaran }}
                            </span>
                            {{-- Racepack Status Badge --}}
                            <span class="px-3 py-1 rounded-xl text-xs font-black uppercase tracking-wider border
                                {{ $daftar->status_racepack === 'Sudah Diambil' ? 'bg-[#ff5500] text-white border-[#ff5500]' : 'bg-slate-800 text-slate-300 border-slate-700' }}">
                                RP: {{ $daftar->status_racepack }}
                            </span>
                        </div>
                    </div>

                    {{-- Ticket Content Body --}}
                    <div class="p-6 flex flex-col md:flex-row gap-6 items-center justify-between">
                        {{-- Left Details --}}
                        <div class="flex-1 space-y-4">
                            {{-- Huge High-Contrast BIB Display --}}
                            <div class="bg-slate-100 dark:bg-white/10 p-4 rounded-2xl border border-slate-200 dark:border-white/15 inline-block w-full sm:w-auto text-center sm:text-left">
                                <span class="text-xs text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider block">Nomor BIB Peserta</span>
                                <span class="text-4xl sm:text-5xl font-black text-slate-900 dark:text-white tracking-tight font-mono">{{ $daftar->bib_number }}</span>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm pt-2">
                                <div>
                                    <span class="text-xs text-slate-500 dark:text-slate-400 font-bold uppercase block">Kategori</span>
                                    <span class="font-extrabold text-slate-900 dark:text-white">{{ $daftar->kategori->nama_kategori }}</span>
                                </div>
                                <div>
                                    <span class="text-xs text-slate-500 dark:text-slate-400 font-bold uppercase block">Ukuran Jersey</span>
                                    <span class="font-extrabold text-[#ff5500] dark:text-orange-400 text-lg">{{ $daftar->ukuran_jersey }}</span>
                                </div>
                                <div>
                                    <span class="text-xs text-slate-500 dark:text-slate-400 font-bold uppercase block">Tanggal Lomba</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $daftar->event->tanggal_event->format('d M Y') }}</span>
                                </div>
                                <div>
                                    <span class="text-xs text-slate-500 dark:text-slate-400 font-bold uppercase block">Lokasi Venue</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $daftar->event->lokasi_venue }}</span>
                                    @if($daftar->event->google_maps_url)
                                        <a href="{{ $daftar->event->google_maps_url }}" target="_blank" rel="noopener" class="block text-[10px] text-blue-600 dark:text-blue-400 hover:underline font-semibold mt-0.5">
                                            📍 Buka di Google Maps →
                                        </a>
                                    @endif
                                </div>
                            </div>

                            @if($daftar->pembayaran)
                                <div class="pt-3 border-t border-slate-100 dark:border-white/10 text-xs text-slate-600 dark:text-slate-400 flex flex-wrap gap-4 font-medium">
                                    <span>Kode TRX: <strong class="font-mono text-slate-800 dark:text-slate-200">{{ $daftar->pembayaran->kode_transaksi }}</strong></span>
                                    <span>Metode: <strong class="text-slate-800 dark:text-slate-200">{{ $daftar->pembayaran->metode_pembayaran }}</strong></span>
                                    <span>Total: <strong class="text-[#ff5500] dark:text-orange-400">Rp {{ number_format($daftar->pembayaran->total_bayar, 0, ',', '.') }}</strong></span>
                                </div>
                            @endif

                            {{-- Payment Action: Hanya tampil jika status Pending --}}
                            @if($daftar->status_pembayaran === 'Pending')
                                <div class="pt-2 flex flex-wrap items-center gap-3">
                                    @if(!empty($daftar->pembayaran->snap_token))
                                        <button 
                                            type="button" 
                                            id="pay-btn-{{ $daftar->id_pendaftaran }}"
                                            onclick="bayarMidtrans('{{ $daftar->pembayaran->snap_token }}')"
                                            class="pay-button px-5 py-2.5 rounded-xl bg-[#ff5500] hover:bg-[#e64d00] text-white font-extrabold text-xs uppercase tracking-wider shadow-lg hover:shadow-orange-500/25 transition duration-200">
                                            💳 Bayar Sekarang
                                        </button>
                                    @else
                                        {{-- Tombol Generate / Lanjut Bayar jika token belum tersimpan --}}
                                        <form method="POST" action="{{ route('runner.payment-token', $daftar->id_pendaftaran) }}">
                                            @csrf
                                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#ff5500] hover:bg-[#e64d00] text-white font-extrabold text-xs uppercase tracking-wider shadow-lg hover:shadow-orange-500/25 transition duration-200">
                                                💳 Lanjut ke Pembayaran
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('runner.cancel-registration', $daftar->id_pendaftaran) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pendaftaran ini? Kuota akan dikembalikan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-4 py-2.5 rounded-xl bg-rose-100 hover:bg-rose-200 dark:bg-rose-500/15 dark:hover:bg-rose-500/25 text-rose-700 dark:text-rose-300 font-extrabold text-xs uppercase tracking-wider transition-colors border border-rose-200 dark:border-rose-500/30">
                                            ✕ Batalkan Pendaftaran
                                        </button>
                                    </form>
                                </div>
                            @endif

                            {{-- Status Gagal / Dibatalkan --}}
                            @if($daftar->status_pembayaran === 'Gagal')
                                <div class="pt-2">
                                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 font-bold text-xs border border-rose-200 dark:border-rose-500/30">
                                        ✕ Pendaftaran Dibatalkan
                                    </span>
                                </div>
                            @endif

                            {{-- Checklist Dokumen RPC (untuk tiket Lunas) --}}
                            @if($daftar->status_pembayaran === 'Lunas' && $daftar->status_racepack === 'Belum Diambil')
                                <div class="pt-3 border-t border-slate-100 dark:border-white/10 mt-2">
                                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">📋 Checklist Pengambilan Racepack</p>
                                    <ul class="text-xs text-slate-600 dark:text-slate-400 space-y-1 list-none">
                                        <li>☑ Bawa KTP/KIA/Kartu Pelajar Asli</li>
                                        <li>☑ Tunjukkan QR Code E-Ticket (dari dashboard ini)</li>
                                        <li class="text-amber-600 dark:text-amber-400 font-semibold">⚠ Jika diwakilkan: bawa Surat Kuasa + KTP Asli Perwakilan</li>
                                    </ul>
                                </div>
                            @endif
                        </div>

                        {{-- Right: High Contrast QR Code --}}
                        @if($daftar->status_pembayaran === 'Lunas')
                            <div class="w-full md:w-auto p-4 bg-slate-50 dark:bg-white/10 rounded-2xl border border-slate-200 dark:border-white/15 flex flex-col items-center justify-center text-center">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">QR Code E-Ticket</span>
                                <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm mb-2">
                                    {!! QrCode::size(140)->generate($daftar->qr_code_token) !!}
                                </div>
                                <p class="text-[10px] text-slate-400 dark:text-slate-500 font-mono break-all select-all leading-relaxed mt-1">{{ $daftar->qr_code_token }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="glass-card rounded-3xl p-12 text-center shadow-sm">
            <svg class="w-12 h-12 text-slate-400 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z"/>
            </svg>
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Belum Ada Tiket Event</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 mb-4">Daftarkan diri Anda ke salah satu event lari pilihan.</p>
            <a href="{{ route('events.index') }}" class="px-5 py-2.5 rounded-xl btn-brand-orange text-white font-extrabold text-xs uppercase tracking-wider shadow-md">
                Lihat Katalog Event
            </a>
        </div>
    @endif
</div>

{{-- Midtrans Snap JS CDN --}}
<script 
    type="text/javascript"
    src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ config('midtrans.client_key', env('MIDTRANS_CLIENT_KEY')) }}">
</script>

<script>
    function bayarMidtrans(token) {
        if (typeof window.snap === 'undefined') {
            alert('Sistem pembayaran Midtrans Snap belum siap. Pastikan koneksi internet stabil.');
            return;
        }

        window.snap.pay(token, {
            onSuccess: function(result) {
                alert("Pembayaran berhasil!");
                location.reload();
            },
            onPending: function(result) {
                alert("Menunggu pembayaran...");
                location.reload();
            },
            onError: function(result) {
                alert("Pembayaran gagal!");
                location.reload();
            },
            onClose: function() {
                alert('Kamu menutup popup tanpa menyelesaikan pembayaran.');
            }
        });
    }

    @if(session('snap_token'))
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            bayarMidtrans('{{ session('snap_token') }}');
        }, 500);
    });
    @endif
</script>
@endsection
