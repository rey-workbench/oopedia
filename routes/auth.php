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
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout');
