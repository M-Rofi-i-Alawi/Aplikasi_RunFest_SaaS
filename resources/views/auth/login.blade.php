@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        {{-- Card --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8">
            <div class="text-center mb-8">
                <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center mx-auto mb-3 text-blue-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-slate-900">Selamat Datang Kembali</h1>
                <p class="text-sm text-slate-500 mt-1">Masuk ke akun RunFest Anda</p>
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
                <span>Masuk dengan Google</span>
            </a>

            {{-- Divider --}}
            <div class="relative flex py-2 items-center mb-6">
                <div class="flex-grow border-t border-slate-200"></div>
                <span class="flex-shrink mx-4 text-xs uppercase font-bold text-slate-400">atau manual</span>
                <div class="flex-grow border-t border-slate-200"></div>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('login.process') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors"
                        placeholder="nama@email.com">
                    @error('email')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors"
                        placeholder="••••••••">
                    @error('password')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-slate-600">Ingat saya</span>
                    </label>
                </div>

                <button type="submit"
                    class="w-full py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-sm transition-colors">
                    Masuk Akun
                </button>
            </form>

            <p class="text-center mt-6 text-sm text-slate-600">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="text-blue-600 hover:underline font-bold">Daftar Sekarang</a>
            </p>
        </div>
    </div>
</div>
@endsection
