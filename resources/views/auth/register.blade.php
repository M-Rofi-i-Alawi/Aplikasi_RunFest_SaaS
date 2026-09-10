@extends('layouts.app')

@section('title', 'Daftar')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-lg">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8">
            <div class="text-center mb-8">
                <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center mx-auto mb-3 text-blue-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-slate-900">Buat Akun Baru</h1>
                <p class="text-sm text-slate-500 mt-1">Bergabung dengan komunitas pelari RunFest</p>
            </div>

            {{-- Google OAuth Button --}}
            <a href="{{ route('auth.google') }}" 
               class="w-full flex items-center justify-center space-x-3 px-4 py-3 rounded-lg bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold text-sm transition-colors mb-6 shadow-sm">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                <span>Daftar dengan Google</span>
            </a>

            {{-- Divider --}}
            <div class="relative flex py-2 items-center mb-6">
                <div class="flex-grow border-t border-slate-200"></div>
                <span class="flex-shrink mx-4 text-xs uppercase font-bold text-slate-400">atau manual</span>
                <div class="flex-grow border-t border-slate-200"></div>
            </div>

            <form method="POST" action="{{ route('register.process') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="nama" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required autofocus
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors"
                        placeholder="Nama lengkap Anda">
                    @error('nama') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email <span class="text-rose-500">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors"
                        placeholder="nama@email.com">
                    @error('email') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password <span class="text-rose-500">*</span></label>
                        <input type="password" id="password" name="password" required
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors"
                            placeholder="Min. 8 karakter">
                        @error('password') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Konfirmasi <span class="text-rose-500">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors"
                            placeholder="Ulangi password">
                    </div>
                </div>

                <div>
                    <label for="no_hp" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nomor HP</label>
                    <input type="tel" id="no_hp" name="no_hp" value="{{ old('no_hp') }}"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors"
                        placeholder="08xxxxxxxxxx">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="golongan_darah" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Gol. Darah</label>
                        <select id="golongan_darah" name="golongan_darah"
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors">
                            <option value="">Pilih</option>
                            <option value="A" {{ old('golongan_darah') == 'A' ? 'selected' : '' }}>A</option>
                            <option value="B" {{ old('golongan_darah') == 'B' ? 'selected' : '' }}>B</option>
                            <option value="AB" {{ old('golongan_darah') == 'AB' ? 'selected' : '' }}>AB</option>
                            <option value="O" {{ old('golongan_darah') == 'O' ? 'selected' : '' }}>O</option>
                        </select>
                    </div>
                    <div>
                        <label for="kontak_darurat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kontak Darurat</label>
                        <input type="text" id="kontak_darurat" name="kontak_darurat" value="{{ old('kontak_darurat') }}"
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors"
                            placeholder="Nama & No. HP">
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-sm transition-colors">
                    Daftar Sekarang
                </button>
            </form>

            <p class="text-center mt-6 text-sm text-slate-600">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-bold">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection
