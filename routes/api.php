<?php

use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\RacePackController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — RunFest SaaS
|--------------------------------------------------------------------------
|
| Endpoint untuk Race Pack Collection (RPC) Scanner & Payment Webhook.
| Route API di Laravel 11 sudah stateless & bebas CSRF secara default.
|
*/

// ========================================================================
// RACE PACK SCAN & CONFIRM API
// ========================================================================

Route::post('/racepack/scan', [RacePackController::class, 'scan'])->name('api.racepack.scan');
Route::post('/racepack/confirm', [RacePackController::class, 'confirmHandover'])->name('api.racepack.confirm');

// ========================================================================
// MIDTRANS WEBHOOK CALLBACK
// ========================================================================

Route::post('/payment/webhook', [PaymentCallbackController::class, 'handle'])->name('api.payment.webhook');
