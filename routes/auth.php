<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::controller(LoginController::class)->group(function (): void {
        Route::get('login', 'create')->name('login');
        Route::post('login', 'store');
    });

    Route::controller(RegisterController::class)->group(function (): void {
        Route::get('register', 'create')->name('register');
        Route::post('register', 'store');
    });

    Route::get('forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'store'])
        ->name('password.store');

    Route::get('auth/google', [\App\Http\Controllers\Auth\SocialController::class, 'redirectToGoogle'])
        ->name('auth.google');

    Route::get('auth/google/callback', [\App\Http\Controllers\Auth\SocialController::class, 'handleGoogleCallback'])
        ->name('auth.google.callback');
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout');
