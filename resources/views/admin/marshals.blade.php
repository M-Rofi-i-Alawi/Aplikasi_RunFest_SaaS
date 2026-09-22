@extends('layouts.app')

@section('title', 'Manajemen Marshal — Admin RunFest')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-brand-navy">
    <!-- Header -->
    <div class="bg-gradient-to-r from-brand-navy to-brand-navy-light text-white py-10 px-4">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-black italic uppercase tracking-tight">Manajemen Marshal</h1>
            <p class="text-slate-300 mt-1 text-sm">Tugaskan Marshal ke event dan kelola PIN akses scanner lapangan.</p>
        </div>
    </div>

    <!-- Admin Nav -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-20 shadow-sm">
        <div class="max-w-7xl mx-auto px-4">
            <nav class="flex gap-1 overflow-x-auto py-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 whitespace-nowrap transition-colors">Dashboard</a>
                <a href="{{ route('admin.organizers') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 whitespace-nowrap transition-colors">Organizer</a>
                <a href="{{ route('admin.events') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 whitespace-nowrap transition-colors">Event</a>
                <a href="{{ route('admin.marshals') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold bg-brand-orange text-white whitespace-nowrap">Marshal</a>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-8">
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-xl text-green-800 dark:text-green-300 text-sm flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-xl text-red-800 dark:text-red-300 text-sm flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form Assign Marshal -->
            <div class="lg:col-span-1">
                <div class="glass-card p-6 sticky top-24">
                    <!-- Tombol Buat Akun Marshal Baru -->
                    <div class="mb-5 pb-4 border-b border-gray-100 dark:border-gray-700">
                        <button type="button" onclick="openMarshalModal()"
                                class="w-full btn-brand-orange text-white py-2.5 px-4 rounded-xl text-sm font-extrabold flex items-center justify-center gap-2 shadow-md hover:shadow-orange-500/25 transition-all">
                            <span>➕ Buat Akun Marshal Baru</span>
                        </button>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1.5 text-center">
                            Daftarkan akun scanner lapangan langsung aktif.
                        </p>
                    </div>

                    <h2 class="font-bold text-gray-900 dark:text-white text-lg mb-5 flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Tugaskan Marshal ke Event
                    </h2>
                    <form action="{{ route('admin.marshals.assign') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Pilih Event <span class="text-red-500">*</span></label>
                            <select name="id_event" required class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-4 py-2.5 text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-brand-orange focus:border-transparent outline-none">
                                <option value="">-- Pilih Event --</option>
                                @foreach($availableEvents as $event)
                                    <option value="{{ $event->id_event }}">{{ $event->nama_event }} ({{ $event->tanggal_event?->isoFormat('D MMM YYYY') }})</option>
                                @endforeach
                            </select>
                            @error('id_event')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Pilih Marshal <span class="text-red-500">*</span></label>
                            <select name="id_user_marshal" required class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-4 py-2.5 text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-brand-orange focus:border-transparent outline-none">
                                <option value="">-- Pilih Marshal --</option>
                                @forelse($availableMarshals as $marshal)
                                    <option value="{{ $marshal->id_user }}">{{ $marshal->nama }} ({{ $marshal->email }})</option>
                                @empty
                                    <option disabled>Tidak ada marshal aktif</option>
                                @endforelse
                            </select>
                            @error('id_user_marshal')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">PIN Scanner (Opsional)</label>
                            <input type="text" name="pin_akses_scanner" placeholder="Biarkan kosong = auto-generate" class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-4 py-2.5 text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-brand-orange focus:border-transparent outline-none" maxlength="20">
                            <p class="text-xs text-gray-400 mt-1">Minimal 4 karakter jika diisi manual.</p>
                            @error('pin_akses_scanner')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" class="btn-brand-orange text-white w-full py-3 rounded-xl text-sm font-bold">
                            Tugaskan Marshal
                        </button>
                    </form>

                    @if($availableMarshals->isEmpty())
                        <div class="mt-4 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                            <p class="text-xs text-amber-700 dark:text-amber-400">
                                <strong>Belum ada Marshal aktif.</strong> Klik tombol <strong>"➕ Buat Akun Marshal Baru"</strong> di atas untuk membuat akun sekarang.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Daftar Penugasan per Event -->
            <div class="lg:col-span-2 space-y-5">
                @forelse($events as $event)
                    <div class="glass-card p-5">
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">{{ $event->nama_event }}</h3>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $event->tanggal_event?->isoFormat('D MMMM YYYY') }} • {{ $event->lokasi_venue }}</p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400 flex-shrink-0">
                                {{ $event->status_event }}
                            </span>
                        </div>

                        @if($event->marshals->isEmpty())
                            <div class="text-center py-5 text-gray-400 border border-dashed border-gray-200 dark:border-gray-600 rounded-lg">
                                <svg class="w-8 h-8 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <p class="text-sm">Belum ada marshal yang ditugaskan.</p>
                            </div>
                        @else
                            <div class="space-y-2">
                                @foreach($event->marshals as $assignment)
                                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                            {{ strtoupper(substr($assignment->marshal->nama ?? 'M', 0, 1)) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-sm text-gray-900 dark:text-white truncate">{{ $assignment->marshal->nama ?? 'N/A' }}</p>
                                            <p class="text-xs text-gray-400 truncate">{{ $assignment->marshal->email ?? '' }}</p>
                                        </div>

                                        <!-- Edit PIN inline -->
                                        <form action="{{ route('admin.marshals.update-pin', $assignment->id_marshal_assignment) }}" method="POST" class="flex items-center gap-2 flex-shrink-0">
                                            @csrf @method('PATCH')
                                            <div class="relative">
                                                <input type="text" name="pin_akses_scanner"
                                                    value="{{ $assignment->pin_akses_scanner }}"
                                                    class="w-28 border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-1.5 text-xs dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-brand-orange focus:border-transparent outline-none font-mono"
                                                    placeholder="PIN Scanner">
                                            </div>
                                            <button type="submit" title="Simpan PIN" class="p-1.5 rounded-lg bg-brand-orange/10 text-brand-orange hover:bg-brand-orange/20 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.marshals.remove', $assignment->id_marshal_assignment) }}" method="POST" onsubmit="return confirm('Hapus penugasan ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Hapus Penugasan" class="p-1.5 rounded-lg text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="glass-card p-12 text-center text-gray-400">
                        <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <h3 class="font-semibold text-lg mb-2">Belum Ada Event Aktif</h3>
                        <p class="text-sm">Publikasikan event terlebih dahulu untuk dapat menugaskan Marshal.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- ======================================================================== -->
<!-- MODAL: BUAT AKUN MARSHAL BARU -->
<!-- ======================================================================== -->
<div id="add-marshal-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <!-- Backdrop Blur Overlay -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeMarshalModal()"></div>

    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative w-full max-w-lg rounded-3xl bg-white dark:bg-brand-navy p-6 sm:p-8 shadow-2xl border border-gray-100 dark:border-white/10 z-10 transition-all transform scale-100">
            <!-- Modal Header -->
            <div class="flex items-start justify-between pb-4 border-b border-gray-100 dark:border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-brand-orange to-orange-400 flex items-center justify-center text-white text-xl font-bold shadow-md shadow-orange-500/20">
                        📱
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">
                            Buat Akun Marshal Baru
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">
                            Daftarkan akun petugas scanner dengan status <strong class="text-emerald-600 dark:text-emerald-400">Langsung Aktif</strong>.
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeMarshalModal()"
                        class="p-1.5 rounded-xl text-gray-400 hover:text-gray-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Error Notification in Modal -->
            @if(isset($errors) && $errors->any())
                <div class="mt-4 p-3.5 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs">
                    <p class="font-bold mb-1 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        Periksa kembali isian form berikut:
                    </p>
                    <ul class="list-disc list-inside space-y-0.5 text-[11px] text-rose-700 dark:text-rose-300">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Modal Form -->
            <form action="{{ route('admin.marshals.store') }}" method="POST" class="mt-5 space-y-4">
                @csrf

                <!-- Nama Lengkap Marshal -->
                <div>
                    <label for="marshal-nama" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-slate-300 mb-1.5">
                        Nama Lengkap Marshal <span class="text-brand-orange">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none text-gray-400">
                            👤
                        </span>
                        <input type="text" id="marshal-nama" name="nama" value="{{ old('nama') }}" required maxlength="255"
                               placeholder="cth. Budi Setiawan"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-orange focus:border-transparent outline-none">
                    </div>
                </div>

                <!-- Email Marshal -->
                <div>
                    <label for="marshal-email" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-slate-300 mb-1.5">
                        Alamat Email Resmi <span class="text-brand-orange">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none text-gray-400">
                            ✉️
                        </span>
                        <input type="email" id="marshal-email" name="email" value="{{ old('email') }}" required maxlength="255"
                               placeholder="marshal@runfest.id"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-orange focus:border-transparent outline-none">
                    </div>
                    <p class="text-[11px] text-gray-400 mt-1">Digunakan untuk login ke halaman Scanner Racepack.</p>
                </div>

                <!-- No HP / WhatsApp (Nullable) -->
                <div>
                    <label for="marshal-no_hp" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-slate-300 mb-1.5">
                        Nomor HP / WhatsApp <span class="text-gray-400 font-normal lowercase">(opsional)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none text-gray-400">
                            📱
                        </span>
                        <input type="text" id="marshal-no_hp" name="no_hp" value="{{ old('no_hp') }}" maxlength="20"
                               placeholder="081234567890"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-orange focus:border-transparent outline-none">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="marshal-password" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-slate-300">
                            Password Akun <span class="text-brand-orange">*</span>
                        </label>
                        <button type="button" onclick="generateRandomMarshalPassword()"
                                class="text-[11px] font-bold text-brand-orange hover:underline">
                            ⚡ Acak Password
                        </button>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none text-gray-400">
                            🔒
                        </span>
                        <input type="password" id="marshal-password" name="password" required minlength="6"
                               placeholder="Minimal 6 karakter..."
                               class="w-full pl-10 pr-11 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-orange focus:border-transparent outline-none">
                        <button type="button" onclick="toggleMarshalPasswordVisibility()"
                                class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 text-xs font-bold">
                            <span id="marshal-eye-icon">👁️</span>
                        </button>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-1">Minimal 6 karakter untuk kemudahan akses petugas lapangan.</p>
                </div>

                <!-- Info Box -->
                <div class="p-3.5 rounded-2xl bg-brand-orange/10 border border-brand-orange/20 text-xs text-brand-navy dark:text-slate-200 flex items-start gap-2.5">
                    <span class="text-base leading-none">💡</span>
                    <p class="leading-relaxed text-[11px]">
                        Akun Marshal yang dibuat akan langsung berstatus <strong>Aktif</strong> dan otomatis muncul di daftar pilihan penugasan event di samping.
                    </p>
                </div>

                <!-- Footer Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-white/10">
                    <button type="button" onclick="closeMarshalModal()"
                            class="px-5 py-2.5 rounded-xl text-sm font-bold text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="btn-brand-orange text-white px-6 py-2.5 rounded-xl text-sm font-extrabold shadow-lg hover:shadow-orange-500/25 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <span>Simpan & Buat Akun Marshal</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openMarshalModal() {
        const modal = document.getElementById('add-marshal-modal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
                const namaInput = document.getElementById('marshal-nama');
                if (namaInput) namaInput.focus();
            }, 100);
        }
    }

    function closeMarshalModal() {
        const modal = document.getElementById('add-marshal-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }

    function toggleMarshalPasswordVisibility() {
        const input = document.getElementById('marshal-password');
        const icon = document.getElementById('marshal-eye-icon');
        if (input) {
            if (input.type === 'password') {
                input.type = 'text';
                if (icon) icon.innerText = '🙈';
            } else {
                input.type = 'password';
                if (icon) icon.innerText = '👁️';
            }
        }
    }

    function generateRandomMarshalPassword() {
        const chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        let pass = '';
        for (let i = 0; i < 8; i++) {
            pass += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        const input = document.getElementById('marshal-password');
        if (input) {
            input.type = 'text';
            input.value = pass;
            const icon = document.getElementById('marshal-eye-icon');
            if (icon) icon.innerText = '🙈';
        }
    }

    // Close on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeMarshalModal();
        }
    });

    // Auto-open modal if validation errors exist on submit
    @if(isset($errors) && ($errors->has('nama') || $errors->has('email') || $errors->has('password') || $errors->has('no_hp')))
        document.addEventListener('DOMContentLoaded', function() {
            openMarshalModal();
        });
    @endif
</script>
@endpush
@endsection
