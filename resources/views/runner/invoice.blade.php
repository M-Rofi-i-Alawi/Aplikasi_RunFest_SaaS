<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice-{{ Str::slug($pendaftaran->event->nama_event) }}-{{ $pendaftaran->bib_number }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }

        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        @media print {
            html, body {
                background: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
            .no-print {
                display: none !important;
            }
            .invoice-container {
                box-shadow: none !important;
                border: 1px solid #e2e8f0 !important;
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                border-radius: 8px !important;
            }
            .page-bg {
                background: white !important;
                padding: 0 !important;
                min-height: auto !important;
            }
            .avoid-break {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
    </style>
</head>
<body class="page-bg bg-gradient-to-br from-slate-100 via-slate-50 to-orange-50 min-h-screen py-6 px-4 print:py-0 print:px-0">

    {{-- Action Buttons (Hidden on print) --}}
    <div class="no-print max-w-3xl mx-auto mb-4 flex items-center justify-between">
        <a href="{{ route('runner.dashboard') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 font-bold text-xs shadow-sm hover:bg-slate-50 hover:border-slate-300 transition-all">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            Kembali ke Dashboard
        </a>
        <button onclick="window.print()" 
                class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-[#ff5500] hover:bg-[#e64d00] text-white font-extrabold text-xs shadow-md hover:shadow-orange-500/25 transition-all">
            🖨️ Cetak / Simpan PDF
        </button>
    </div>

    {{-- Invoice Card --}}
    <div class="invoice-container max-w-3xl mx-auto bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden print:rounded-lg print:border print:border-slate-200">

        {{-- ============================================================ --}}
        {{-- HEADER --}}
        {{-- ============================================================ --}}
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 px-6 py-4 print:px-5 print:py-3.5 flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2.5 mb-1">
                    <img src="{{ asset('images/logo-icon.png') }}" alt="RunFest Logo" class="h-8 w-auto object-contain flex-shrink-0">
                    <div>
                        <h1 class="text-white font-black text-lg tracking-tight leading-tight">Run<span class="text-orange-400">Fest</span> SaaS</h1>
                        <p class="text-slate-400 text-[9px] font-semibold uppercase tracking-widest leading-none">Sports Event & Ticketing Platform</p>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <span class="block text-orange-400 text-[9px] font-black uppercase tracking-widest mb-0.5">Official Payment Receipt</span>
                <span class="block text-white text-base font-black tracking-tight leading-tight">BUKTI PEMBAYARAN</span>
            </div>
        </div>

        {{-- Transaction Info Bar --}}
        <div class="bg-slate-50 border-b border-slate-200 px-6 py-2.5 print:px-5 print:py-2 flex flex-wrap items-center justify-between gap-2.5">
            <div class="flex flex-wrap items-center gap-5 text-xs">
                <div>
                    <span class="text-slate-400 font-semibold uppercase tracking-wider block text-[10px]">No. Transaksi</span>
                    <span class="text-slate-900 font-black font-mono text-xs">{{ $pendaftaran->pembayaran->kode_transaksi }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-semibold uppercase tracking-wider block text-[10px]">Waktu Bayar</span>
                    <span class="text-slate-900 font-bold text-xs">
                        {{ $pendaftaran->pembayaran->waktu_bayar 
                            ? \Carbon\Carbon::parse($pendaftaran->pembayaran->waktu_bayar)->translatedFormat('d F Y, H:i') . ' WIB'
                            : '-' }}
                    </span>
                </div>
            </div>
            {{-- Badge LUNAS --}}
            <div>
                <div class="px-3.5 py-1 rounded-lg bg-emerald-500 text-white font-black text-[11px] uppercase tracking-wider flex items-center gap-1.5 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    LUNAS / SETTLEMENT
                </div>
            </div>
        </div>

        <div class="px-6 py-4 print:px-5 print:py-3 space-y-3.5 print:space-y-2.5">

            {{-- ============================================================ --}}
            {{-- DATA PESERTA --}}
            {{-- ============================================================ --}}
            <div class="avoid-break">
                <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5">
                    <span class="w-3.5 h-[2px] bg-orange-400 rounded-full"></span>
                    Data Peserta
                </h2>
                <div class="bg-slate-50/80 rounded-lg border border-slate-200/70 p-3 print:p-2.5">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs print:text-[11px]">
                        <div class="sm:col-span-2">
                            <span class="text-slate-400 text-[10px] font-semibold block">Nama Lengkap</span>
                            <span class="text-slate-900 font-bold">{{ $pendaftaran->runner->nama }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] font-semibold block">NIK</span>
                            <span class="text-slate-900 font-bold font-mono">{{ $pendaftaran->runner->nik ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] font-semibold block">No. HP</span>
                            <span class="text-slate-900 font-bold">{{ $pendaftaran->runner->no_hp ?? '-' }}</span>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-slate-400 text-[10px] font-semibold block">Email</span>
                            <span class="text-slate-900 font-bold truncate block">{{ $pendaftaran->runner->email }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] font-semibold block">Golongan Darah</span>
                            <span class="text-slate-900 font-bold">{{ $pendaftaran->runner->golongan_darah ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- DETAIL EVENT & LOMBA --}}
            {{-- ============================================================ --}}
            <div class="avoid-break">
                <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5">
                    <span class="w-3.5 h-[2px] bg-orange-400 rounded-full"></span>
                    Detail Event & Lomba
                </h2>
                <div class="bg-slate-50/80 rounded-lg border border-slate-200/70 p-3 print:p-2.5">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs print:text-[11px] items-center">
                        <div class="sm:col-span-2">
                            <span class="text-slate-400 text-[10px] font-semibold block">Nama Event</span>
                            <span class="text-slate-900 font-black text-sm">{{ $pendaftaran->event->nama_event }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] font-semibold block">Tanggal Pelaksanaan</span>
                            <span class="text-slate-900 font-bold">{{ $pendaftaran->event->tanggal_event->translatedFormat('d F Y') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] font-semibold block">Lokasi Venue</span>
                            <span class="text-slate-900 font-bold truncate block">{{ $pendaftaran->event->lokasi_venue }}</span>
                            <a href="{{ $pendaftaran->event->maps_url }}" target="_blank" rel="noopener noreferrer"
                               class="no-print inline-flex items-center gap-1 text-[10px] text-blue-600 hover:text-blue-800 hover:underline font-bold mt-0.5">
                                <span>🗺️ Lihat Maps</span>
                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                            </a>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] font-semibold block">Kategori Lari</span>
                            <span class="text-slate-900 font-bold">{{ $pendaftaran->kategori->nama_kategori }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] font-semibold block">Ukuran Jersey</span>
                            <span class="inline-flex items-center justify-center px-2 py-0.5 rounded bg-blue-600 text-white font-black text-xs">{{ $pendaftaran->ukuran_jersey }}</span>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-slate-400 text-[10px] font-semibold block">Nomor BIB Peserta</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded bg-slate-900 text-white font-black text-xs font-mono tracking-widest">
                                {{ $pendaftaran->bib_number }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- RINCIAN PEMBAYARAN --}}
            {{-- ============================================================ --}}
            <div class="avoid-break">
                <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5">
                    <span class="w-3.5 h-[2px] bg-orange-400 rounded-full"></span>
                    Rincian Pembayaran
                </h2>
                <div class="rounded-lg border border-slate-200 overflow-hidden">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-slate-800 text-white">
                                <th class="text-left px-3.5 py-2 font-bold text-[11px] uppercase tracking-wider">Item</th>
                                <th class="text-center px-2 py-2 font-bold text-[11px] uppercase tracking-wider w-12">Qty</th>
                                <th class="text-right px-3.5 py-2 font-bold text-[11px] uppercase tracking-wider">Harga</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr class="bg-white">
                                <td class="px-3.5 py-2.5">
                                    <span class="font-bold text-slate-900 block">Pendaftaran {{ $pendaftaran->kategori->nama_kategori }}</span>
                                    <span class="text-[10px] text-slate-500">{{ $pendaftaran->event->nama_event }}</span>
                                </td>
                                <td class="px-2 py-2.5 text-center font-bold text-slate-700">1</td>
                                <td class="px-3.5 py-2.5 text-right font-bold text-slate-900">Rp {{ number_format($pendaftaran->pembayaran->total_bayar, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="px-3.5 py-2">
                                    <span class="font-semibold text-slate-700">Ukuran Jersey: {{ $pendaftaran->ukuran_jersey }}</span>
                                </td>
                                <td class="px-2 py-2 text-center text-slate-500">—</td>
                                <td class="px-3.5 py-2 text-right text-slate-500 text-[11px]">Termasuk</td>
                            </tr>
                            <tr class="bg-slate-50/60">
                                <td class="px-3.5 py-2" colspan="2">
                                    <span class="text-slate-500 text-[11px]">Biaya Layanan / Payment Gateway</span>
                                </td>
                                <td class="px-3.5 py-2 text-right font-semibold text-emerald-600">Rp 0</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="bg-gradient-to-r from-slate-900 to-slate-800 text-white">
                                <td class="px-3.5 py-2.5 font-black uppercase tracking-wider" colspan="2">Total Bayar</td>
                                <td class="px-3.5 py-2.5 text-right">
                                    <span class="text-orange-400 font-black text-base">Rp {{ number_format($pendaftaran->pembayaran->total_bayar, 0, ',', '.') }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    {{-- Metode Pembayaran --}}
                    <div class="bg-emerald-50/80 border-t border-emerald-200/60 px-3.5 py-2 flex items-center justify-between">
                        <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Metode Pembayaran</span>
                        <span class="font-black text-emerald-800 text-xs">
                            {{ ucwords(str_replace('_', ' ', $pendaftaran->pembayaran->metode_pembayaran ?? $pendaftaran->pembayaran->payment_type ?? 'Midtrans Gateway')) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- STEMPEL DIGITAL & VERIFIKASI --}}
            {{-- ============================================================ --}}
            <div class="pt-3 border-t border-dashed border-slate-200 avoid-break">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-start gap-2.5">
                        <div class="mt-0.5 w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-700 leading-tight">Dokumen Terverifikasi Sah</p>
                            <p class="text-[10px] text-slate-500 leading-tight">
                                Struk ini adalah bukti pembayaran resmi yang diterbitkan secara otomatis oleh sistem RunFest SaaS dan tidak memerlukan tanda tangan basah.
                            </p>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-[9px] text-slate-400 font-semibold leading-tight">Dicetak pada</p>
                        <p class="text-[11px] text-slate-600 font-bold leading-tight">{{ now()->translatedFormat('d M Y, H:i') }} WIB</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="bg-slate-50 border-t border-slate-200 px-6 py-2.5 print:px-5 print:py-2 text-center">
            <p class="text-[9px] text-slate-400 font-semibold">
                © {{ date('Y') }} RunFest SaaS — Sports Event & Racepack Ticketing Platform. All rights reserved.
            </p>
        </div>
    </div>

    {{-- Bottom spacer for screen view (hidden on print) --}}
    <div class="h-6 no-print"></div>
</body>
</html>
