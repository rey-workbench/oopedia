<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Authentication
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

// Guest login (accessible to everyone, including already authenticated users)
Route::get('/guest-login', [LoginController::class, 'guestLogin'])->name('guest.login');

// Logout route (for all authenticated users including guests)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
