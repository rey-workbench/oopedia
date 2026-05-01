<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SocialController;
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

    Route::get('forgot-password', [ForgotPasswordController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [ForgotPasswordController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [ResetPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [ResetPasswordController::class, 'store'])
        ->name('password.store');

    Route::get('auth/google', [SocialController::class, 'redirectToGoogle'])
        ->name('auth.google');

    Route::get('auth/google/callback', [SocialController::class, 'handleGoogleCallback'])
        ->name('auth.google.callback');

    Route::get('auth/google/choose-role', [SocialController::class, 'chooseRole'])
        ->name('auth.google.choose-role');

    Route::post('auth/google/register/{role}', [SocialController::class, 'register'])
        ->name('auth.google.register');
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout');
