@extends('layouts.app')

@section('title', 'Daftar Event - ' . $event->nama_event . ' — RunFest SaaS')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <a href="{{ route('events.show', $event->slug) }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-[#F05423] dark:hover:text-[#F05423] transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
        <span>Kembali ke Detail Event</span>
    </a>

    <div class="glass-card bg-white dark:bg-[#0f2137] rounded-3xl border border-slate-200 dark:border-white/10 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="border-b border-slate-200 dark:border-white/10 pb-6">
            <span class="text-[10px] font-black uppercase tracking-widest text-[#F05423] bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/30 px-2.5 py-1 rounded-lg">
                FORM PENDAFTARAN PESERTA
            </span>
            <h1 class="text-2xl sm:text-3xl font-black italic uppercase tracking-tight text-slate-900 dark:text-white mt-2">{{ $event->nama_event }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                📅 {{ $event->tanggal_event->format('d F Y') }} · 📍 {{ $event->lokasi_venue }}
            </p>
        </div>

        <form method="POST" action="{{ route('runner.register-event.store') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="id_event" value="{{ $event->id_event }}">

            {{-- Opsi Kategori --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider mb-3">
                    Pilih Kategori Lari <span class="text-rose-500">*</span>
                </label>
                <div class="space-y-3">
                    @foreach($event->kategori as $kat)
                        @php $isChecked = request('kategori') ? (request('kategori') == $kat->id_kategori) : $loop->first; @endphp
                        <label class="flex items-center p-4 rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 hover:border-[#F05423] dark:hover:border-[#F05423] cursor-pointer transition-all group
                            {{ !$kat->is_tersedia ? 'opacity-50 cursor-not-allowed' : '' }}">
                            <input type="radio" name="id_kategori" value="{{ $kat->id_kategori }}" 
                                class="w-4 h-4 text-[#F05423] border-slate-300 dark:border-white/20 focus:ring-[#F05423]"
                                {{ !$kat->is_tersedia ? 'disabled' : '' }}
                                {{ $isChecked ? 'checked' : '' }}
                                required>
                            <div class="ml-3 flex-1 flex items-center justify-between gap-3">
                                <div>
                                    <span class="font-extrabold text-slate-900 dark:text-white group-hover:text-[#F05423] transition-colors block text-sm sm:text-base">{{ $kat->nama_kategori }}</span>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                                        Sisa {{ $kat->sisa_kuota }} slot ({{ $kat->terisi }}/{{ $kat->kuota_peserta }} terisi)
                                        @if(!$kat->is_tersedia)
                                            <span class="text-rose-600 dark:text-rose-400 font-bold"> — KUOTA PENUH</span>
                                        @endif
                                    </p>
                                </div>
                                <span class="text-base sm:text-lg font-black text-[#F05423] shrink-0">Rp {{ number_format($kat->harga, 0, ',', '.') }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('id_kategori') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 font-medium">{{ $message }}</p> @enderror
            </div>

            {{-- Ukuran Jersey --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider mb-3">
                    Pilih Ukuran Jersey Lomba <span class="text-rose-500">*</span>
                </label>
                <div class="flex flex-wrap gap-3">
                    @foreach(['S', 'M', 'L', 'XL', 'XXL'] as $size)
                        <label class="cursor-pointer">
                            <input type="radio" name="ukuran_jersey" value="{{ $size }}" class="sr-only peer" required
                                {{ old('ukuran_jersey', auth()->user()->ukuran_jersey_default ?? 'M') == $size ? 'checked' : '' }}>
                            <div class="w-14 h-14 rounded-2xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] flex items-center justify-center font-black text-slate-700 dark:text-slate-200 text-sm
                                peer-checked:bg-[#F05423] peer-checked:border-[#F05423] peer-checked:text-white
                                hover:border-[#F05423] transition-all shadow-sm">
                                {{ $size }}
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('ukuran_jersey') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 font-medium">{{ $message }}</p> @enderror
            </div>

            {{-- Midtrans Snap Notice --}}
            <div class="p-4 rounded-2xl bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/30 flex items-start gap-3 text-xs text-orange-900 dark:text-orange-200">
                <span class="text-lg shrink-0 mt-0.5">💳</span>
                <span class="leading-relaxed"><strong>Metode Pembayaran Resmi:</strong> Pilihan pembayaran (QRIS, GoPay, ShopeePay, Virtual Account BCA/Mandiri/BRI, dll.) akan langsung muncul melalui pop-up Midtrans Snap setelah Anda klik tombol di bawah.</span>
            </div>

            {{-- Participant Summary --}}
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 space-y-2">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 block mb-1">Ringkasan Data Peserta</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                    <div><span class="text-slate-500 dark:text-slate-400">Nama:</span> <strong class="text-slate-900 dark:text-white">{{ auth()->user()->nama }}</strong></div>
                    <div><span class="text-slate-500 dark:text-slate-400">Email:</span> <strong class="text-slate-900 dark:text-white">{{ auth()->user()->email }}</strong></div>
                    @if(auth()->user()->no_hp)
                        <div><span class="text-slate-500 dark:text-slate-400">No HP:</span> <strong class="text-slate-900 dark:text-white">{{ auth()->user()->no_hp }}</strong></div>
                    @endif
                    @if(auth()->user()->golongan_darah)
                        <div><span class="text-slate-500 dark:text-slate-400">Gol. Darah:</span> <strong class="text-slate-900 dark:text-white">{{ auth()->user()->golongan_darah }}</strong></div>
                    @endif
                    @if(auth()->user()->nik)
                        <div><span class="text-slate-500 dark:text-slate-400">NIK:</span> <strong class="text-slate-900 dark:text-white font-mono">{{ auth()->user()->nik }}</strong></div>
                    @endif
                </div>
            </div>

            <button type="submit"
                class="w-full py-4 rounded-2xl btn-brand-orange text-white font-black text-xs sm:text-sm uppercase tracking-wider shadow-lg shadow-orange-500/25 transition-all hover:scale-[1.01]">
                LANJUT PENDAFTARAN & BAYAR →
            </button>
        </form>
    </div>
</div>
@endsection
