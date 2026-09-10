@extends('layouts.app')

@section('title', 'Daftar Event - ' . $event->nama_event)

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <a href="{{ route('events.show', $event->slug) }}" class="inline-flex items-center space-x-2 text-sm font-semibold text-slate-600 hover:text-blue-600 mb-6 transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
        <span>Kembali ke Detail Event</span>
    </a>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="border-b border-slate-200 pb-6 mb-6">
            <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Form Pendaftaran Peserta</span>
            <h1 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $event->nama_event }}</h1>
            <p class="text-sm text-slate-500 mt-0.5">Tanggal Event: {{ $event->tanggal_event->format('d F Y') }} | Venue: {{ $event->lokasi_venue }}</p>
        </div>

        <form method="POST" action="{{ route('runner.register-event.store') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="id_event" value="{{ $event->id_event }}">

            {{-- Opsi Kategori --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Pilih Kategori Lari <span class="text-rose-500">*</span></label>
                <div class="space-y-3">
                    @foreach($event->kategori as $kat)
                        @php $isChecked = request('kategori') ? (request('kategori') == $kat->id_kategori) : $loop->first; @endphp
                        <label class="flex items-center p-4 rounded-lg border border-slate-200 bg-slate-50 hover:bg-white hover:border-blue-500 cursor-pointer transition-colors group
                            {{ !$kat->is_tersedia ? 'opacity-50 cursor-not-allowed' : '' }}">
                            <input type="radio" name="id_kategori" value="{{ $kat->id_kategori }}" 
                                class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-500"
                                {{ !$kat->is_tersedia ? 'disabled' : '' }}
                                {{ $isChecked ? 'checked' : '' }}
                                required>
                            <div class="ml-3 flex-1 flex items-center justify-between">
                                <div>
                                    <span class="font-bold text-slate-900 group-hover:text-blue-600 transition-colors">{{ $kat->nama_kategori }}</span>
                                    <p class="text-xs text-slate-500 font-medium mt-0.5">
                                        Sisa {{ $kat->sisa_kuota }} slot ({{ $kat->terisi }}/{{ $kat->kuota_peserta }} terisi)
                                        @if(!$kat->is_tersedia)
                                            <span class="text-rose-600 font-bold"> — PENUH</span>
                                        @endif
                                    </p>
                                </div>
                                <span class="text-base font-extrabold text-blue-600">Rp {{ number_format($kat->harga, 0, ',', '.') }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('id_kategori') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
            </div>

            {{-- Ukuran Jersey --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Pilih Ukuran Jersey <span class="text-rose-500">*</span></label>
                <div class="flex flex-wrap gap-3">
                    @foreach(['S', 'M', 'L', 'XL', 'XXL'] as $size)
                        <label class="cursor-pointer">
                            <input type="radio" name="ukuran_jersey" value="{{ $size }}" class="sr-only peer" required
                                {{ old('ukuran_jersey', 'M') == $size ? 'checked' : '' }}>
                            <div class="w-14 h-14 rounded-lg border border-slate-300 bg-white flex items-center justify-center font-extrabold text-slate-700 text-sm
                                peer-checked:bg-blue-600 peer-checked:border-blue-600 peer-checked:text-white
                                hover:bg-slate-50 transition-colors">
                                {{ $size }}
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('ukuran_jersey') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
            </div>

            {{-- HAPUS / HILANGKAN BLOK DROPDOWN INI KARENA SUDAH DI-HANDLE POP-UP MIDTRANS SNAP --}}
            <div class="p-3.5 rounded-xl bg-orange-50 border border-orange-200 flex items-center gap-3 text-xs text-orange-800">
                <span class="text-base">💳</span>
                <span><strong>Metode Pembayaran Otomatis:</strong> Pilihan pembayaran (QRIS, Virtual Account, GoPay, dll.) akan langsung muncul melalui pop-up Midtrans Snap setelah Anda klik tombol di bawah.</span>
            </div>

            {{-- Participant Summary --}}
            <div class="p-4 rounded-lg bg-slate-50 border border-slate-200">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-2">Ringkasan Data Peserta</span>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div><span class="text-slate-500">Nama:</span> <strong class="text-slate-900">{{ auth()->user()->nama }}</strong></div>
                    <div><span class="text-slate-500">Email:</span> <strong class="text-slate-900">{{ auth()->user()->email }}</strong></div>
                    @if(auth()->user()->no_hp)
                        <div><span class="text-slate-500">No HP:</span> <strong class="text-slate-900">{{ auth()->user()->no_hp }}</strong></div>
                    @endif
                    @if(auth()->user()->golongan_darah)
                        <div><span class="text-slate-500">Gol. Darah:</span> <strong class="text-slate-900">{{ auth()->user()->golongan_darah }}</strong></div>
                    @endif
                </div>
            </div>

            <button type="submit"
                class="w-full py-4 rounded-xl btn-brand-orange text-white font-extrabold text-sm uppercase tracking-wider shadow-md hover:shadow-orange-500/25 transition-all">
                LANJUT PENDAFTARAN & BAYAR →
            </button>
        </form>
    </div>
</div>
@endsection
