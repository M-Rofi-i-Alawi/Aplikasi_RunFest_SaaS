@extends('layouts.app')

@section('title', 'Daftar Akun Baru - RunFest SaaS')

@section('content')
<div class="min-h-[85vh] flex flex-col items-center justify-center px-4 py-12 relative overflow-hidden">
    {{-- Decorative Background Elements --}}
    <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-[#F05423]/10 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-blue-600/10 blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-lg relative z-10">
        {{-- Card Container --}}
        <div class="bg-white dark:bg-[#0f2137] rounded-2xl border border-slate-200 dark:border-white/10 shadow-xl shadow-slate-200/50 dark:shadow-none p-6 sm:p-8 backdrop-blur-sm transition-colors duration-200">
            
            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center mb-4 p-3 rounded-2xl bg-orange-50 dark:bg-white/5 border border-orange-100 dark:border-white/10 shadow-sm">
                    <img src="{{ asset('images/logo-icon.png') }}" alt="RunFest Logo" class="h-12 w-auto object-contain drop-shadow-sm hover:scale-105 transition-transform duration-300">
                </div>
                <h1 class="text-2xl sm:text-3xl font-black italic uppercase tracking-tight text-slate-900 dark:text-white">
                    Buat Akun <span class="text-[#F05423]">RunFest</span>
                </h1>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mt-1">
                    Bergabung dengan komunitas pelari dan nikmati kemudahan pendaftaran event
                </p>
            </div>

            {{-- Google OAuth Button --}}
            <a href="{{ route('auth.google') }}" 
               class="w-full flex items-center justify-center gap-3 px-4 py-3 rounded-xl bg-white dark:bg-[#081624] border border-slate-300 dark:border-white/15 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-[#122538] hover:border-slate-400 dark:hover:border-white/30 font-bold text-sm shadow-sm transition-all duration-200 group mb-6">
                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" viewBox="0 0 24 24">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                <span>Daftar Cepat dengan Google</span>
            </a>

            {{-- Divider --}}
            <div class="relative flex py-2 items-center mb-6">
                <div class="flex-grow border-t border-slate-200 dark:border-white/10"></div>
                <span class="flex-shrink mx-4 text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">atau lengkapi formulir</span>
                <div class="flex-grow border-t border-slate-200 dark:border-white/10"></div>
            </div>

            {{-- Registration Form --}}
            <form method="POST" action="{{ route('register.process') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="nama" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Nama Lengkap <span class="text-[#F05423]">*</span>
                    </label>
                    <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required autofocus
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all"
                        placeholder="Nama lengkap Anda sesuai identitas">
                    @error('nama') 
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-bold">{{ $message }}</p> 
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Alamat Email <span class="text-[#F05423]">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all"
                        placeholder="nama@email.com">
                    @error('email') 
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-bold">{{ $message }}</p> 
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Password <span class="text-[#F05423]">*</span>
                        </label>
                        <input type="password" id="password" name="password" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all"
                            placeholder="Min. 8 karakter">
                        @error('password') 
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-bold">{{ $message }}</p> 
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Konfirmasi Password <span class="text-[#F05423]">*</span>
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all"
                            placeholder="Ulangi password">
                    </div>
                </div>

                <div>
                    <label for="no_hp" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Nomor Handphone / WhatsApp
                    </label>
                    <input type="tel" id="no_hp" name="no_hp" value="{{ old('no_hp') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all"
                        placeholder="Contoh: 081234567890">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="golongan_darah" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Golongan Darah
                        </label>
                        <select id="golongan_darah" name="golongan_darah"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all">
                            <option value="">Pilih Gol. Darah</option>
                            <option value="A" {{ old('golongan_darah') == 'A' ? 'selected' : '' }}>A</option>
                            <option value="B" {{ old('golongan_darah') == 'B' ? 'selected' : '' }}>B</option>
                            <option value="AB" {{ old('golongan_darah') == 'AB' ? 'selected' : '' }}>AB</option>
                            <option value="O" {{ old('golongan_darah') == 'O' ? 'selected' : '' }}>O</option>
                        </select>
                    </div>
                    <div>
                        <label for="kontak_darurat" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Kontak Darurat
                        </label>
                        <input type="text" id="kontak_darurat" name="kontak_darurat" value="{{ old('kontak_darurat') }}"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all"
                            placeholder="Nama & No. HP Darurat">
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-3 rounded-xl bg-[#F05423] hover:bg-[#d94416] text-white font-black italic uppercase tracking-wider text-sm shadow-lg shadow-orange-500/25 hover:shadow-orange-500/40 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                    Daftar Sekarang
                </button>
            </form>

            {{-- Footer Links --}}
            <p class="text-center mt-6 text-xs sm:text-sm font-medium text-slate-600 dark:text-slate-400">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="text-[#F05423] hover:underline font-extrabold ml-1">
                    Masuk di sini
                </a>
            </p>
        </div>

        {{-- ============================================================
             CTA BANNER: Gabung Sebagai Event Organizer
             ============================================================ --}}
        @php
            $adminWa = config('services.admin_wa', env('ADMIN_WA', '6287812822400'));
            $waText  = urlencode('Halo Admin RunFest, saya ingin mendaftar dan memverifikasi organisasi/EO saya untuk menyelenggarakan event lari di platform RunFest SaaS.');
            $waUrl   = "https://wa.me/{$adminWa}?text={$waText}";
        @endphp
        <div class="mt-5 relative overflow-hidden rounded-2xl border border-[#F05423]/30 bg-gradient-to-br from-[#F05423]/10 via-orange-50/80 to-amber-50/60 dark:from-[#F05423]/15 dark:via-[#1a1200]/80 dark:to-[#0f2137] p-5 shadow-lg shadow-orange-500/10">
            {{-- Decorative shape --}}
            <div class="absolute top-0 right-0 w-32 h-32 rounded-full bg-[#F05423]/10 -translate-y-8 translate-x-8 pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-20 h-20 rounded-full bg-orange-300/10 translate-y-6 -translate-x-6 pointer-events-none"></div>

            <div class="relative z-10 flex items-start gap-4">
                {{-- Icon --}}
                <div class="shrink-0 w-12 h-12 rounded-2xl bg-[#F05423] text-white flex items-center justify-center shadow-lg shadow-orange-500/30">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                    </svg>
                </div>
                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] font-extrabold uppercase tracking-widest text-[#F05423] mb-0.5">Untuk Event Organizer (EO)</p>
                    <h3 class="text-base font-black text-slate-900 dark:text-white leading-tight mb-1">
                        Ingin Menyelenggarakan<br>Event Lari?
                    </h3>
                    <p class="text-xs text-slate-600 dark:text-slate-400 font-medium leading-relaxed mb-3">
                        Bergabunglah sebagai Organizer resmi RunFest. Hubungi Admin via WhatsApp untuk proses verifikasi &amp; aktivasi akun EO Anda.
                    </p>
                    <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" id="eo-wa-cta"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-xs uppercase tracking-wider transition-all shadow-md shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:-translate-y-0.5 active:translate-y-0">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.114 1.524 5.843L.057 23.569a.75.75 0 00.974.974l5.726-1.467A11.952 11.952 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.891 0-3.667-.522-5.179-1.428l-.371-.22-3.838.983.999-3.712-.242-.384A9.953 9.953 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                        </svg>
                        Hubungi Admin via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection