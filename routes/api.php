<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Payments\PaymentController;
use App\Http\Controllers\Payments\WebhookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::get('me', [AuthController::class, 'me'])->middleware('auth:sanctum')->name('me');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('payments/{id}', [PaymentController::class, 'show'])->name('payments.show');
});

Route::post('providers/{provider}/webhook', [WebhookController::class, 'handle']);