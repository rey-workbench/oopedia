<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\{
    RegisterController,
    LoginController,
    GuestLoginController,
    LogoutController
};

Route::middleware('guest')->group(function () {
    // Authentication
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
    
    // Guest login
    Route::get('/guest-login', [GuestLoginController::class, 'login'])->name('guest.login');
});

// Logout routes (for all authenticated users)
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
Route::post('/mahasiswa/logout', [LogoutController::class, 'logout'])->name('mahasiswa.logout');
Route::post('/admin/logout', [LogoutController::class, 'logout'])->name('admin.logout');
Route::post('/guest-logout', [LogoutController::class, 'logout'])->name('guest.logout');


