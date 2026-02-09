<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Load separated route files
require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/mahasiswa.php';

use App\Http\Controllers\Auth\LoginController;

// Redirect root to login or materials page based on authentication
Route::get('/', [LoginController::class, 'index']);

Route::get('/test-inertia', function () {
    return inertia('Welcome', ['title' => 'Inertia World']);
});

// Fallback route for 404 errors
Route::fallback([LoginController::class, 'fallback']);

