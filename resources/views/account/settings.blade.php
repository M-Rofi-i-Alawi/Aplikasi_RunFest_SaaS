@extends('layouts.app')

@section('title', 'Pengaturan Akun & Profil — RunFest SaaS')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- Page Header --}}
    <div class="border-b border-slate-200 dark:border-white/10 pb-6">
        <span class="inline-block px-3 py-1 text-[10px] font-black uppercase tracking-widest text-[#F05423] bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/30 rounded-xl mb-2">
            ⚙️ PENGATURAN PENGGUNA
        </span>
        <h1 class="text-2xl sm:text-3xl font-black italic uppercase tracking-tight text-slate-900 dark:text-white">
            Pengaturan Akun & Profil
        </h1>
        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1 font-medium">
            Kelola informasi data diri, validasi identitas, kredensial login, dan data rekening penarikan dana.
        </p>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-emerald-50 dark:bg-emerald-500/10 border-2 border-emerald-500 text-emerald-800 dark:text-emerald-300 rounded-2xl px-5 py-4 shadow-sm animate-fade-in">
            <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0 font-black text-sm">✓</div>
            <div class="flex-1 text-sm font-bold">{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-3 bg-rose-50 dark:bg-rose-500/10 border-2 border-rose-500 text-rose-800 dark:text-rose-300 rounded-2xl px-5 py-4 shadow-sm animate-fade-in">
            <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center shrink-0 font-black text-sm">✕</div>
            <div class="flex-1 text-sm font-bold">{{ session('error') }}</div>
        </div>
    @endif

    {{-- ================================================================
         SECTION 1: PROFIL & DATA DIRI
         ================================================================ --}}
    <div class="glass-card bg-white dark:bg-[#0f2137] rounded-3xl border border-slate-200 dark:border-white/10 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="flex items-center space-x-3.5 pb-4 border-b border-slate-200 dark:border-white/10">
            <div class="w-11 h-11 rounded-2xl bg-blue-50 dark:bg-blue-500/15 border border-blue-200 dark:border-blue-500/30 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-base font-black italic uppercase tracking-wider text-slate-900 dark:text-white">Profil & Data Diri</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    @if($user->isRunner())
                        Informasi pribadi dan data medis pelari.
                    @elseif($user->isOrganizer())
                        Informasi pribadi penyelenggara event.
                    @elseif($user->isMarshal())
                        Informasi pribadi petugas marshal.
                    @else
                        Informasi akun administrator.
                    @endif
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('account.update-profile') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Avatar & Identity Badge --}}
            <div class="flex items-center space-x-4 p-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10">
                <div class="w-16 h-16 rounded-2xl btn-brand-orange text-white font-black text-2xl flex items-center justify-center shrink-0 shadow-md shadow-orange-500/25">
                    {{ strtoupper(substr($user->nama, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-extrabold text-slate-900 dark:text-white text-base truncate">{{ $user->nama }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $user->email }}</p>
                    <div class="flex flex-wrap items-center gap-2 mt-1.5">
                        @php
                            $roleClass = [
                                'Runner' => 'bg-blue-50 dark:bg-blue-500/15 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-500/30',
                                'Organizer' => 'bg-purple-50 dark:bg-purple-500/15 text-purple-700 dark:text-purple-300 border-purple-200 dark:border-purple-500/30',
                                'Marshal' => 'bg-emerald-50 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-500/30',
                                'SuperAdmin' => 'bg-orange-50 dark:bg-orange-500/15 text-[#F05423] border-orange-200 dark:border-orange-500/30',
                            ][$user->role] ?? 'bg-slate-100 text-slate-700';
                        @endphp
                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider border {{ $roleClass }}">
                            {{ $user->role }}
                        </span>
                        @if($user->google_id)
                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-rose-50 dark:bg-rose-500/15 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-500/30 flex items-center gap-1">
                                <svg class="w-3 h-3" viewBox="0 0 24 24"><path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                                <span>Google Auth</span>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Nama Lengkap --}}
            <div>
                <label for="nama" class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider mb-1.5">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="nama" name="nama" value="{{ old('nama', $user->nama) }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all">
                @error('nama') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 font-medium">{{ $message }}</p> @enderror
            </div>

            {{-- Nomor HP (semua role) --}}
            <div>
                <label for="no_hp" class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider mb-1.5">
                    Nomor WhatsApp / HP
                </label>
                <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all"
                    placeholder="Contoh: 081234567890">
                @error('no_hp') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 font-medium">{{ $message }}</p> @enderror
            </div>

            {{-- ===== RUNNER-ONLY FIELDS ===== --}}
            @if($user->isRunner())
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Golongan Darah --}}
                    <div>
                        <label for="golongan_darah" class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider mb-1.5">
                            Golongan Darah
                        </label>
                        <select id="golongan_darah" name="golongan_darah"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all">
                            <option value="">Pilih Golongan Darah</option>
                            @foreach(['A', 'B', 'AB', 'O'] as $gd)
                                <option value="{{ $gd }}" {{ old('golongan_darah', $user->golongan_darah) == $gd ? 'selected' : '' }}>{{ $gd }}</option>
                            @endforeach
                        </select>
                        @error('golongan_darah') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Kontak Darurat --}}
                    <div>
                        <label for="kontak_darurat" class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider mb-1.5">
                            Kontak Darurat (Nama & No. HP)
                        </label>
                        <input type="text" id="kontak_darurat" name="kontak_darurat" value="{{ old('kontak_darurat', $user->kontak_darurat) }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all"
                            placeholder="Contoh: Ibu Sari — 081234567890">
                        @error('kontak_darurat') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- ===== IDENTITAS DIRI (NIK & FOTO KTP) ===== --}}
                <div class="pt-5 border-t border-slate-200 dark:border-white/10 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="text-base">🪪</span>
                        <h3 class="text-xs font-black text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                            Validasi Identitas Resmi (KTP / KIA / Kartu Pelajar)
                        </h3>
                    </div>

                    {{-- NIK / NISN --}}
                    <div>
                        <label for="nik" class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider mb-1.5">
                            NIK / NISN <span class="text-slate-400 normal-case font-normal">(16 digit angka)</span>
                        </label>
                        <input type="text" id="nik" name="nik" value="{{ old('nik', $user->nik) }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-mono tracking-wider font-bold transition-all"
                            placeholder="3201XXXXXXXXXXXX"
                            maxlength="16"
                            inputmode="numeric">
                        <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">Nomor Induk Kependudukan (KTP) atau NISN. Digunakan untuk verifikasi anti-joki di venue lomba.</p>
                        @error('nik') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Upload Foto Identitas --}}
                    <div>
                        <label for="foto_identitas" class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider mb-1.5">
                            Foto Fisik KTP / KIA / Kartu Pelajar
                        </label>
                        
                        {{-- Preview Foto Saat Ini --}}
                        @if($user->foto_identitas)
                            <div class="mb-3 p-3.5 rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 flex items-center gap-4">
                                <img src="{{ Storage::url($user->foto_identitas) }}" alt="Foto Identitas" 
                                    class="w-24 h-16 object-cover rounded-xl border border-slate-300 dark:border-white/20 shadow-sm cursor-pointer hover:opacity-90"
                                    onclick="window.open('{{ Storage::url($user->foto_identitas) }}', '_blank')">
                                <div class="min-w-0">
                                    <p class="text-xs font-black text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                        </svg>
                                        Foto Identitas Tersimpan & Aktif
                                    </p>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Klik thumbnail untuk melihat ukuran penuh, atau unggah file baru di bawah untuk mengganti.</p>
                                </div>
                            </div>
                        @endif

                        <input type="file" id="foto_identitas" name="foto_identitas" accept="image/jpeg,image/png,image/webp"
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-700 dark:text-slate-300 text-sm file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:tracking-wider file:bg-blue-50 dark:file:bg-blue-500/20 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100 transition-all cursor-pointer">
                        <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">Format: JPG, PNG, atau WebP. Maksimal 2MB. Foto akan ditampilkan ke petugas Marshal saat pengambilan racepack.</p>
                        @error('foto_identitas') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Ukuran Jersey Default --}}
                <div class="pt-4 border-t border-slate-200 dark:border-white/10">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider mb-2.5">
                        Ukuran Jersey Default Anda
                    </label>
                    <div class="flex flex-wrap gap-3">
                        @foreach(['S', 'M', 'L', 'XL', 'XXL'] as $size)
                            <label class="cursor-pointer">
                                <input type="radio" name="ukuran_jersey_default" value="{{ $size }}" class="sr-only peer"
                                    {{ old('ukuran_jersey_default', $user->ukuran_jersey_default) == $size ? 'checked' : '' }}>
                                <div class="w-14 h-14 rounded-2xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] flex items-center justify-center font-black text-slate-800 dark:text-slate-200 text-sm
                                    peer-checked:bg-[#F05423] peer-checked:border-[#F05423] peer-checked:text-white
                                    hover:border-[#F05423] transition-all shadow-sm">
                                    {{ $size }}
                                </div>
                            </label>
                        @endforeach
                        <label class="cursor-pointer">
                            <input type="radio" name="ukuran_jersey_default" value="" class="sr-only peer"
                                {{ old('ukuran_jersey_default', $user->ukuran_jersey_default) == null ? 'checked' : '' }}>
                            <div class="h-14 px-4 rounded-2xl border border-dashed border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] flex items-center justify-center font-bold text-slate-500 dark:text-slate-400 text-xs
                                peer-checked:bg-slate-200 dark:peer-checked:bg-white/20 peer-checked:text-slate-900 dark:peer-checked:text-white
                                hover:border-slate-400 transition-all">
                                Belum Set
                            </div>
                        </label>
                    </div>
                    @error('ukuran_jersey_default') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 font-medium">{{ $message }}</p> @enderror
                </div>
            @endif

            <div class="pt-4 border-t border-slate-200 dark:border-white/10">
                <button type="submit"
                    class="px-6 py-3 rounded-2xl btn-brand-orange text-white font-extrabold text-xs uppercase tracking-wider shadow-md transition-all hover:scale-105">
                    ✓ Simpan Perubahan Profil
                </button>
            </div>
        </form>
    </div>

    {{-- ================================================================
         SECTION 2: KEAMANAN & KATA SANDI
         ================================================================ --}}
    <div class="glass-card bg-white dark:bg-[#0f2137] rounded-3xl border border-slate-200 dark:border-white/10 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="flex items-center space-x-3.5 pb-4 border-b border-slate-200 dark:border-white/10">
            <div class="w-11 h-11 rounded-2xl bg-amber-50 dark:bg-amber-500/15 border border-amber-200 dark:border-amber-500/30 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-base font-black italic uppercase tracking-wider text-slate-900 dark:text-white">Keamanan & Kata Sandi</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Kelola kredensial login dan keamanan akun Anda.</p>
            </div>
        </div>

        @if($user->isOAuthUser())
            {{-- OAuth User: Proteksi akun terintegrasi Google --}}
            <div class="p-5 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10">
                <div class="flex items-start space-x-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-white dark:bg-white/10 border border-slate-300 dark:border-white/20 flex items-center justify-center shrink-0 mt-0.5 shadow-sm">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-sm">Akun Terhubung via Google OAuth</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Akun Anda terautentikasi melalui akun Google resmi. Sandi dan verifikasi 2 langkah dikelola langsung melalui
                            <a href="https://myaccount.google.com/security" target="_blank" rel="noopener noreferrer" class="text-blue-600 dark:text-blue-400 font-bold hover:underline inline-flex items-center gap-1">
                                Pusat Keamanan Google
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                            </a>.
                        </p>
                        <div class="mt-2.5 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-xs text-emerald-800 dark:text-emerald-300 font-bold flex items-center space-x-2">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>Proteksi akun aktif & aman via Google OAuth</span>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- Manual User: Form ubah password --}}
            <form method="POST" action="{{ route('account.update-password') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider mb-1.5">
                        Kata Sandi Saat Ini <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" id="current_password" name="current_password" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm transition-all"
                        placeholder="Masukkan kata sandi lama">
                    @error('current_password') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider mb-1.5">
                            Kata Sandi Baru <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" id="password" name="password" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm transition-all"
                            placeholder="Minimal 8 karakter">
                        @error('password') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider mb-1.5">
                            Konfirmasi Sandi Baru <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm transition-all"
                            placeholder="Ulangi kata sandi baru">
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 text-xs text-amber-800 dark:text-amber-300 font-medium flex items-start space-x-2.5">
                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span>Setelah mengubah kata sandi, sesi Anda akan tetap aktif. Gunakan kata sandi baru ini pada login berikutnya.</span>
                </div>

                <div class="pt-4 border-t border-slate-200 dark:border-white/10">
                    <button type="submit"
                        class="px-6 py-3 rounded-2xl bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs uppercase tracking-wider shadow-md transition-all hover:scale-105">
                        Ubah Kata Sandi
                    </button>
                </div>
            </form>
        @endif
    </div>

    {{-- ================================================================
         SECTION 3: REKENING ORGANIZER (Hanya untuk Organizer & SuperAdmin)
         ================================================================ --}}
    @if($user->isOrganizer() || $user->isSuperAdmin())
    <div class="glass-card bg-white dark:bg-[#0f2137] rounded-3xl border border-slate-200 dark:border-white/10 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="flex items-center space-x-3.5 pb-4 border-b border-slate-200 dark:border-white/10">
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/30 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-base font-black italic uppercase tracking-wider text-slate-900 dark:text-white">Rekening Payout Organizer</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Informasi rekening bank atau e-wallet untuk penarikan hasil penjualan tiket event Anda.</p>
            </div>
        </div>

        {{-- Daftar Rekening Tersimpan --}}
        @if($rekening->count() > 0)
            <div class="space-y-3">
                @foreach($rekening as $rek)
                    <div class="p-4 rounded-2xl border {{ $rek->is_primary ? 'border-emerald-500/50 bg-emerald-50 dark:bg-emerald-500/10' : 'border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5' }} flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-start space-x-3.5">
                            <div class="w-11 h-11 rounded-2xl {{ $rek->is_primary ? 'bg-emerald-600 text-white shadow-emerald-500/25' : 'bg-white dark:bg-white/10 border border-slate-300 dark:border-white/20 text-slate-700 dark:text-slate-200' }} flex items-center justify-center shrink-0 shadow-sm font-black text-sm">
                                @if($rek->tipe_rekening === 'E-Wallet')
                                    📱
                                @else
                                    🏦
                                @endif
                            </div>
                            <div>
                                <div class="flex items-center space-x-2">
                                    <h4 class="font-extrabold text-slate-900 dark:text-white text-sm">{{ $rek->nama_bank }}</h4>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider {{ $rek->tipe_rekening === 'E-Wallet' ? 'bg-violet-100 dark:bg-violet-500/20 text-violet-700 dark:text-violet-300 border border-violet-200 dark:border-violet-500/30' : 'bg-sky-100 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-500/30' }}">
                                        {{ $rek->tipe_rekening }}
                                    </span>
                                    @if($rek->is_primary)
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-emerald-600 text-white shadow-sm">
                                            ★ UTAMA
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-slate-800 dark:text-slate-200 font-mono font-bold mt-0.5">{{ $rek->nomor_rekening }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">a.n. {{ $rek->nama_pemilik }}</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2 shrink-0">
                            @if(!$rek->is_primary)
                                <form method="POST" action="{{ route('account.rekening.set-primary', $rek->id_rekening) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3.5 py-1.5 rounded-xl border border-emerald-300 dark:border-emerald-500/30 bg-white dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 font-extrabold text-xs hover:bg-emerald-50 dark:hover:bg-emerald-500/25 transition-all">
                                        Jadikan Utama
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('account.rekening.destroy', $rek->id_rekening) }}" onsubmit="return confirm('Yakin ingin menghapus rekening {{ addslashes($rek->nama_bank) }} - {{ $rek->nomor_rekening }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3.5 py-1.5 rounded-xl border border-rose-300 dark:border-rose-500/30 bg-white dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 font-extrabold text-xs hover:bg-rose-50 dark:hover:bg-rose-500/20 transition-all">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-6 rounded-2xl bg-slate-50 dark:bg-white/5 border border-dashed border-slate-300 dark:border-white/20 text-center">
                <div class="text-3xl mb-2">🏦</div>
                <p class="text-sm font-bold text-slate-800 dark:text-white">Belum Ada Rekening Payout Tersimpan</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Tambahkan rekening bank atau e-wallet resmi untuk pencairan dana tiket event.</p>
            </div>
        @endif

        {{-- Form Tambah Rekening Baru --}}
        <div class="p-5 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 space-y-4">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                <span>➕</span> Tambah Rekening Payout Baru
            </h3>

            <form method="POST" action="{{ route('account.rekening.store') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="tipe_rekening" class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider mb-1.5">
                            Tipe Rekening <span class="text-rose-500">*</span>
                        </label>
                        <select id="tipe_rekening" name="tipe_rekening" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all">
                            <option value="Bank Transfer">Bank Transfer (BCA, Mandiri, BNI, BRI, dll)</option>
                            <option value="E-Wallet">E-Wallet (GoPay, OVO, Dana, ShopeePay)</option>
                        </select>
                    </div>
                    <div>
                        <label for="nama_bank" class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider mb-1.5">
                            Nama Bank / Platform <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="nama_bank" name="nama_bank" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all"
                            placeholder="Contoh: BCA / Mandiri / GoPay">
                        @error('nama_bank') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="nomor_rekening" class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider mb-1.5">
                            Nomor Rekening / Nomor HP E-Wallet <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="nomor_rekening" name="nomor_rekening" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-mono font-bold tracking-wider transition-all"
                            placeholder="1234567890">
                        @error('nomor_rekening') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="nama_pemilik" class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider mb-1.5">
                            Nama Pemilik Rekening (Sesuai Buku Tabungan / Akun) <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="nama_pemilik" name="nama_pemilik" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-white/15 bg-white dark:bg-[#081624] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-[#F05423] focus:ring-2 focus:ring-[#F05423]/20 text-sm font-medium transition-all"
                            placeholder="Nama lengkap pemilik rekening">
                        @error('nama_pemilik') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <button type="submit"
                    class="px-6 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs uppercase tracking-wider shadow-md transition-all hover:scale-105">
                    + Tambahkan Rekening Payout
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection