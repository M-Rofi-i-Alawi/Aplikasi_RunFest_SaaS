@extends('layouts.app')

@section('title', 'Daftar Akun Baru - RunFest SaaS')

@section('content')
<div class="min-h-[85vh] flex items-center justify-center px-4 py-12 relative overflow-hidden">
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
    </div>
</div>
@endsection