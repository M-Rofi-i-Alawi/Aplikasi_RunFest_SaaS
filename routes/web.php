<?php

use App\Http\Controllers\AccountController;
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

    // Konfirmasi Pembayaran (Simulasi)
    Route::post('/confirm-payment/{id}', [OrderController::class, 'confirmPayment'])->name('runner.confirm-payment');

    // Batalkan Pendaftaran (Hapus Tiket + Kembalikan Kuota)
    Route::delete('/cancel-registration/{id}', [OrderController::class, 'cancelRegistration'])->name('runner.cancel-registration');
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

    // Tambah Kategori ke Event
    Route::post('/events/{id}/kategori', [EventController::class, 'storeKategori'])->name('organizer.events.kategori.store');
});

// ========================================================================
// MARSHAL ROUTES
// ========================================================================

Route::middleware(['auth', 'role:Marshal,SuperAdmin'])->prefix('marshal')->group(function () {
    Route::get('/scanner', [RacePackController::class, 'scanPage'])->name('marshal.scanner');
});
