@extends('layouts.app')

@section('title', 'Pengaturan Akun')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Page Header --}}
    <div class="mb-8 border-b border-slate-200 pb-6">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Pengaturan Akun & Profil</h1>
        <p class="text-sm text-slate-600 mt-1">Kelola informasi profil, keamanan akun, dan pengaturan pembayaran Anda.</p>
    </div>

    {{-- ================================================================
         SECTION 1: PROFIL & DATA DIRI
         ================================================================ --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 sm:p-8 mb-6">
        <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-200">
            <div class="w-10 h-10 rounded-lg bg-blue-50 border border-blue-200 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-base font-bold text-slate-900 uppercase tracking-wider">Profil & Data Diri</h2>
                <p class="text-xs text-slate-500">
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

            {{-- Avatar Display --}}
            <div class="flex items-center space-x-4 mb-2">
                <div class="w-16 h-16 rounded-full bg-slate-200 text-slate-800 font-extrabold text-2xl flex items-center justify-center border-2 border-slate-300">
                    {{ strtoupper(substr($user->nama, 0, 1)) }}
                </div>
                <div>
                    <p class="font-bold text-slate-900">{{ $user->email }}</p>
                    <div class="flex items-center space-x-2 mt-0.5">
                        <span class="px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                            {{ $user->role }}
                        </span>
                        @if($user->google_id)
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200 flex items-center space-x-1">
                                <svg class="w-3 h-3" viewBox="0 0 24 24"><path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                                <span>Google</span>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Nama Lengkap --}}
            <div>
                <label for="nama" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap <span class="text-rose-500">*</span></label>
                <input type="text" id="nama" name="nama" value="{{ old('nama', $user->nama) }}" required
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors">
                @error('nama') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
            </div>

            {{-- Nomor HP (semua role) --}}
            <div>
                <label for="no_hp" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nomor HP</label>
                <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors"
                    placeholder="08xxxxxxxxxx">
                @error('no_hp') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
            </div>

            {{-- ===== RUNNER-ONLY FIELDS ===== --}}
            @if($user->isRunner())
                {{-- Golongan Darah --}}
                <div>
                    <label for="golongan_darah" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Golongan Darah</label>
                    <select id="golongan_darah" name="golongan_darah"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors">
                        <option value="">Pilih Golongan Darah</option>
                        @foreach(['A', 'B', 'AB', 'O'] as $gd)
                            <option value="{{ $gd }}" {{ old('golongan_darah', $user->golongan_darah) == $gd ? 'selected' : '' }}>{{ $gd }}</option>
                        @endforeach
                    </select>
                    @error('golongan_darah') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- Kontak Darurat --}}
                <div>
                    <label for="kontak_darurat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kontak Darurat</label>
                    <input type="text" id="kontak_darurat" name="kontak_darurat" value="{{ old('kontak_darurat', $user->kontak_darurat) }}"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors"
                        placeholder="Contoh: Ibu Sari — 081234567890">
                    @error('kontak_darurat') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- ===== IDENTITAS DIRI (NIK & FOTO) ===== --}}
                <div class="pt-4 border-t border-slate-100">
                    <h3 class="text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z"/>
                        </svg>
                        Validasi Identitas (KTP / Kartu Pelajar)
                    </h3>

                    {{-- NIK / NISN --}}
                    <div class="mb-4">
                        <label for="nik" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">NIK / NISN <span class="text-slate-400 normal-case font-normal">(16 digit)</span></label>
                        <input type="text" id="nik" name="nik" value="{{ old('nik', $user->nik) }}"
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm font-mono tracking-wider transition-colors"
                            placeholder="3201XXXXXXXXXXXX"
                            maxlength="16"
                            inputmode="numeric">
                        <p class="mt-1 text-[11px] text-slate-400">Nomor Induk Kependudukan (KTP) atau NISN (Kartu Pelajar). Digunakan untuk verifikasi identitas di venue.</p>
                        @error('nik') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Upload Foto Identitas --}}
                    <div>
                        <label for="foto_identitas" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Foto Identitas (KTP / KIA / Kartu Pelajar)</label>
                        
                        {{-- Preview Foto Saat Ini --}}
                        @if($user->foto_identitas)
                            <div class="mb-3 p-3 rounded-xl border border-slate-200 bg-slate-50 flex items-center gap-4">
                                <img src="{{ Storage::url($user->foto_identitas) }}" alt="Foto Identitas" 
                                    class="w-20 h-14 object-cover rounded-lg border border-slate-300 shadow-sm">
                                <div>
                                    <p class="text-xs font-bold text-emerald-700 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                        </svg>
                                        Foto Identitas Tersimpan
                                    </p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Unggah file baru di bawah untuk mengganti.</p>
                                </div>
                            </div>
                        @endif

                        <input type="file" id="foto_identitas" name="foto_identitas" accept="image/jpeg,image/png,image/webp"
                            class="w-full px-3 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors cursor-pointer">
                        <p class="mt-1 text-[11px] text-slate-400">Format: JPG, PNG, atau WebP. Maks 2MB. Foto akan ditampilkan ke petugas Marshal saat pengambilan racepack.</p>
                        @error('foto_identitas') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Ukuran Jersey Default --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5">Ukuran Jersey Default</label>
                    <div class="flex flex-wrap gap-3">
                        @foreach(['S', 'M', 'L', 'XL', 'XXL'] as $size)
                            <label class="cursor-pointer">
                                <input type="radio" name="ukuran_jersey_default" value="{{ $size }}" class="sr-only peer"
                                    {{ old('ukuran_jersey_default', $user->ukuran_jersey_default) == $size ? 'checked' : '' }}>
                                <div class="w-14 h-14 rounded-lg border border-slate-300 bg-white flex items-center justify-center font-extrabold text-slate-700 text-sm
                                    peer-checked:bg-blue-600 peer-checked:border-blue-600 peer-checked:text-white
                                    hover:bg-slate-50 transition-colors">
                                    {{ $size }}
                                </div>
                            </label>
                        @endforeach
                        {{-- Opsi kosongkan --}}
                        <label class="cursor-pointer">
                            <input type="radio" name="ukuran_jersey_default" value="" class="sr-only peer"
                                {{ old('ukuran_jersey_default', $user->ukuran_jersey_default) == null ? 'checked' : '' }}>
                            <div class="h-14 px-3 rounded-lg border border-dashed border-slate-300 bg-white flex items-center justify-center font-semibold text-slate-500 text-xs
                                peer-checked:bg-slate-200 peer-checked:border-slate-400 peer-checked:text-slate-800
                                hover:bg-slate-50 transition-colors">
                                Belum Set
                            </div>
                        </label>
                    </div>
                    @error('ukuran_jersey_default') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>
            @endif

            <div class="pt-4 border-t border-slate-100">
                <button type="submit"
                    class="px-6 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider shadow-sm transition-colors">
                    Simpan Profil
                </button>
            </div>
        </form>
    </div>

    {{-- ================================================================
         SECTION 2: KEAMANAN & KATA SANDI
         ================================================================ --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 sm:p-8 mb-6">
        <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-200">
            <div class="w-10 h-10 rounded-lg bg-amber-50 border border-amber-200 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-base font-bold text-slate-900 uppercase tracking-wider">Keamanan & Kata Sandi</h2>
                <p class="text-xs text-slate-500">Kelola kredensial login Anda.</p>
            </div>
        </div>

        @if($user->isOAuthUser())
            {{-- OAuth User: Proteksi akun terintegrasi Google --}}
            <div class="p-5 rounded-lg bg-slate-50 border border-slate-200">
                <div class="flex items-start space-x-3">
                    <div class="w-10 h-10 rounded-full bg-white border border-slate-300 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">Akun Terhubung dengan Google</h3>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                            Akun Anda masuk menggunakan autentikasi Google OAuth. Kata sandi dan keamanan akun dikelola langsung melalui
                            <a href="https://myaccount.google.com/security" target="_blank" class="text-blue-600 font-semibold hover:underline">Pengaturan Keamanan Google</a>.
                        </p>
                        <div class="mt-3 p-3 rounded bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 font-medium flex items-center space-x-2">
                            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>Proteksi akun aktif — dikelola oleh Google</span>
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
                    <label for="current_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kata Sandi Saat Ini <span class="text-rose-500">*</span></label>
                    <input type="password" id="current_password" name="current_password" required
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors"
                        placeholder="Masukkan kata sandi lama">
                    @error('current_password') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kata Sandi Baru <span class="text-rose-500">*</span></label>
                        <input type="password" id="password" name="password" required
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors"
                            placeholder="Minimal 8 karakter">
                        @error('password') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Konfirmasi Sandi Baru <span class="text-rose-500">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors"
                            placeholder="Ulangi kata sandi baru">
                    </div>
                </div>

                <div class="p-3 rounded-lg bg-amber-50 border border-amber-200 text-xs text-amber-800 font-medium flex items-start space-x-2">
                    <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span>Setelah mengubah kata sandi, Anda akan tetap terlogin. Gunakan kata sandi baru saat login berikutnya.</span>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <button type="submit"
                        class="px-6 py-2.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs uppercase tracking-wider shadow-sm transition-colors">
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
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-200">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-base font-bold text-slate-900 uppercase tracking-wider">Rekening Payout Organizer</h2>
                <p class="text-xs text-slate-500">Informasi rekening untuk penarikan dana penjualan tiket event Anda.</p>
            </div>
        </div>

        {{-- Daftar Rekening Tersimpan --}}
        @if($rekening->count() > 0)
            <div class="space-y-3 mb-6">
                @foreach($rekening as $rek)
                    <div class="p-4 rounded-lg border {{ $rek->is_primary ? 'border-emerald-300 bg-emerald-50' : 'border-slate-200 bg-slate-50' }} flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 rounded-lg {{ $rek->is_primary ? 'bg-emerald-600 text-white' : 'bg-white border border-slate-300 text-slate-600' }} flex items-center justify-center flex-shrink-0">
                                @if($rek->tipe_rekening === 'E-Wallet')
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21"/></svg>
                                @endif
                            </div>
                            <div>
                                <div class="flex items-center space-x-2">
                                    <h4 class="font-bold text-slate-900 text-sm">{{ $rek->nama_bank }}</h4>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $rek->tipe_rekening === 'E-Wallet' ? 'bg-violet-100 text-violet-700 border border-violet-200' : 'bg-sky-100 text-sky-700 border border-sky-200' }}">
                                        {{ $rek->tipe_rekening }}
                                    </span>
                                    @if($rek->is_primary)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider bg-emerald-600 text-white">
                                            ★ UTAMA
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-slate-800 font-mono mt-0.5">{{ $rek->nomor_rekening }}</p>
                                <p class="text-xs text-slate-500 font-medium">a.n. {{ $rek->nama_pemilik }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 flex-shrink-0">
                            @if(!$rek->is_primary)
                                <form method="POST" action="{{ route('account.rekening.set-primary', $rek->id_rekening) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1.5 rounded border border-emerald-300 bg-white text-emerald-700 font-bold text-xs hover:bg-emerald-50 transition-colors">
                                        Jadikan Utama
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('account.rekening.destroy', $rek->id_rekening) }}" onsubmit="return confirm('Yakin ingin menghapus rekening ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 rounded border border-rose-300 bg-white text-rose-600 font-bold text-xs hover:bg-rose-50 transition-colors">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-6 rounded-lg bg-slate-50 border border-dashed border-slate-300 text-center mb-6">
                <svg class="w-10 h-10 text-slate-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/>
                </svg>
                <p class="text-sm font-bold text-slate-700">Belum Ada Rekening Tersimpan</p>
                <p class="text-xs text-slate-500 mt-0.5">Tambahkan rekening bank atau e-wallet untuk menerima pembayaran tiket.</p>
            </div>
        @endif

        {{-- Form Tambah Rekening Baru --}}
        <div class="p-5 rounded-lg bg-slate-50 border border-slate-200">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-4">Tambah Rekening Baru</h3>
            <form method="POST" action="{{ route('account.rekening.store') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="tipe_rekening" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tipe Rekening <span class="text-rose-500">*</span></label>
                        <select id="tipe_rekening" name="tipe_rekening" required
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors">
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="E-Wallet">E-Wallet</option>
                        </select>
                    </div>
                    <div>
                        <label for="nama_bank" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Bank / E-Wallet <span class="text-rose-500">*</span></label>
                        <input type="text" id="nama_bank" name="nama_bank" required
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors"
                            placeholder="Contoh: BCA / GoPay / OVO">
                        @error('nama_bank') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="nomor_rekening" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nomor Rekening <span class="text-rose-500">*</span></label>
                        <input type="text" id="nomor_rekening" name="nomor_rekening" required
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm font-mono transition-colors"
                            placeholder="1234567890">
                        @error('nomor_rekening') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="nama_pemilik" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Pemilik Rekening <span class="text-rose-500">*</span></label>
                        <input type="text" id="nama_pemilik" name="nama_pemilik" required
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm transition-colors"
                            placeholder="Nama sesuai rekening">
                        @error('nama_pemilik') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <button type="submit"
                    class="px-6 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider shadow-sm transition-colors">
                    + Tambah Rekening
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
