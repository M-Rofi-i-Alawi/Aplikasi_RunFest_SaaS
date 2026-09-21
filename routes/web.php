<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RacePackController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — RunFest SaaS
|--------------------------------------------------------------------------
*/

// ========================================================================
// HALAMAN PUBLIK
// ========================================================================

Route::get('/', [EventController::class, 'index'])->name('home');

// Katalog Event (Public)
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{slug}', [EventController::class, 'show'])->name('events.show');

// ========================================================================
// AUTENTIKASI (Guest Only)
// ========================================================================

Route::middleware('guest')->group(function () {
    // Login Manual
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');

    // Register Manual
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.process');

    // Google OAuth
    Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

// Logout (Auth Only)
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ========================================================================
// PENGATURAN AKUN & PROFIL (Semua User Terautentikasi)
// ========================================================================

Route::middleware('auth')->prefix('account')->group(function () {
    Route::get('/settings', [AccountController::class, 'index'])->name('account.settings');
    Route::put('/profile', [AccountController::class, 'updateProfile'])->name('account.update-profile');
    Route::put('/password', [AccountController::class, 'updatePassword'])->name('account.update-password');

    // Rekening Organizer
    Route::post('/rekening', [AccountController::class, 'storeRekening'])->name('account.rekening.store');
    Route::patch('/rekening/{id}/primary', [AccountController::class, 'setPrimaryRekening'])->name('account.rekening.set-primary');
    Route::delete('/rekening/{id}', [AccountController::class, 'destroyRekening'])->name('account.rekening.destroy');
});

// ========================================================================
// RUNNER ROUTES
// ========================================================================

Route::middleware(['auth', 'role:Runner,SuperAdmin'])->prefix('runner')->group(function () {
    // Dashboard Runner
    Route::get('/dashboard', [DashboardController::class, 'runnerDashboard'])->name('runner.dashboard');

    // Pendaftaran Event
    Route::get('/register-event/{slug}', [OrderController::class, 'create'])->name('runner.register-event');
    Route::post('/register-event', [OrderController::class, 'store'])->name('runner.register-event.store');

    // Ambil / Generate ulang Snap Token Midtrans (jika token sebelumnya kosong)
    Route::match(['get', 'post'], '/payment/{id}/token', [OrderController::class, 'getPaymentToken'])->name('runner.payment-token');

    // Batalkan Pendaftaran (Hapus Tiket + Kembalikan Kuota)
    Route::delete('/cancel-registration/{id}', [OrderController::class, 'cancelRegistration'])->name('runner.cancel-registration');

    // Invoice / Bukti Pembayaran Resmi
    Route::get('/ticket/{id}/invoice', [OrderController::class, 'invoice'])->name('runner.ticket.invoice');
});

// ========================================================================
// ORGANIZER ROUTES
// ========================================================================

Route::middleware(['auth', 'role:Organizer,SuperAdmin'])->prefix('organizer')->group(function () {
    // Daftar event milik Organizer
    Route::get('/events', [EventController::class, 'myEvents'])->name('organizer.events');

    // CRUD Event
    Route::get('/events/create', [EventController::class, 'create'])->name('organizer.events.create');
    Route::post('/events', [EventController::class, 'store'])->name('organizer.events.store');
    Route::get('/events/{id}/edit', [EventController::class, 'edit'])->name('organizer.events.edit');
    Route::put('/events/{id}', [EventController::class, 'update'])->name('organizer.events.update');
    Route::delete('/events/{id}', [EventController::class, 'destroy'])->name('organizer.events.destroy');

    // Tambah Kategori ke Event
    Route::post('/events/{id}/kategori', [EventController::class, 'storeKategori'])->name('organizer.events.kategori.store');
});

// ========================================================================
// MARSHAL ROUTES
// ========================================================================

Route::middleware(['auth', 'role:Marshal,SuperAdmin'])->prefix('marshal')->group(function () {
    // Halaman pilih event yang ditugaskan
    Route::get('/scanner', [RacePackController::class, 'scanPage'])->name('marshal.scanner');

    // Scanner per event (dilindungi middleware marshal.assigned)
    Route::middleware('marshal.assigned')->group(function () {
        Route::get('/scanner/{id_event}', [RacePackController::class, 'eventScanPage'])->name('marshal.scanner.event');
        Route::post('/racepack/confirm', [RacePackController::class, 'confirmPickup'])->name('marshal.racepack.confirm');
        Route::get('/racepack/lookup', [RacePackController::class, 'lookup'])->name('marshal.racepack.lookup');
    });
});

// ========================================================================
// SUPERADMIN ROUTES
// ========================================================================

Route::middleware(['auth', 'role:SuperAdmin'])->prefix('admin')->group(function () {
    // Dashboard ringkasan sistem
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Manajemen Organizer (Verifikasi & Aktivasi EO)
    Route::get('/organizers', [AdminController::class, 'organizers'])->name('admin.organizers');
    Route::patch('/organizers/{id}/status', [AdminController::class, 'updateOrganizerStatus'])->name('admin.organizers.update-status');

    // Moderasi Event (Review & Publikasi)
    Route::get('/events', [AdminController::class, 'events'])->name('admin.events');
    Route::patch('/events/{id}/status', [AdminController::class, 'updateEventStatus'])->name('admin.events.update-status');

    // Manajemen Marshal (Penugasan & PIN Scanner)
    Route::get('/marshals', [AdminController::class, 'marshals'])->name('admin.marshals');
    Route::post('/marshals/assign', [AdminController::class, 'assignMarshal'])->name('admin.marshals.assign');
    Route::delete('/marshals/{id}', [AdminController::class, 'removeMarshal'])->name('admin.marshals.remove');
    Route::patch('/marshals/{id}/pin', [AdminController::class, 'updateMarshalPin'])->name('admin.marshals.update-pin');
});
