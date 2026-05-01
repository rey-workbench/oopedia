<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to login page (Controller handles internal logic)
Route::get('/', [LoginController::class, 'landing'])->name('landing');
Route::get('/home', [LoginController::class, 'home'])->name('home');

// Load separated logic modules
require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/mahasiswa.php';

// Fallback route for 404 errors (Handles UI consistency)
Route::fallback([LoginController::class, 'home']);
