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
                    <h2 class="font-bold text-gray-900 dark:text-white text-lg mb-5 flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Tugaskan Marshal Baru
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
                                <strong>Tidak ada Marshal aktif.</strong> Buat akun Marshal baru melalui registrasi lalu ubah role-nya menjadi Marshal.
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
@endsection
