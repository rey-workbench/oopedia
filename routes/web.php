<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to login page (Controller handles internal logic)
Route::get('/', [LoginController::class, 'index']);

// Load separated logic modules
require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/mahasiswa.php';

// Development/Testing routes
Route::get('/test-inertia', function () {
    return inertia('Welcome', ['title' => 'Inertia World']);
});

// Fallback route for 404 errors (Handles UI consistency)
Route::fallback([LoginController::class, 'fallback']);
