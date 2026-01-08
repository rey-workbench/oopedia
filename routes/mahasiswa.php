<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mahasiswa\{
    DashboardController as MahasiswaDashboardController,
    MaterialController as MahasiswaMaterialController,
    ProfileController as MahasiswaProfileController,
    MahasiswaController,
    MaterialQuestionController,
    UeqSurveyController as MahasiswaUeqSurveyController,
    MaterialController
};

// Mahasiswa Routes (role 3)
Route::middleware(['auth', 'role:3'])->name('mahasiswa.')->prefix('mahasiswa')->group(function () {
    // Dashboard
    Route::get('dashboard', [MahasiswaDashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/in-progress', [MahasiswaDashboardController::class, 'inProgress'])->name('dashboard.in-progress');
    Route::get('dashboard/completed', [MahasiswaDashboardController::class, 'complete'])->name('dashboard.completed');
    
    // Profile
    Route::get('profile', [MahasiswaProfileController::class, 'show'])->name('profile');
    Route::put('profile', [MahasiswaProfileController::class, 'update'])->name('profile.update');
    
    // Leaderboard
    Route::get('/leaderboard', [MahasiswaController::class, 'leaderboard'])->name('leaderboard');
});

// Publicly accessible material routes (for Guests and Mahasiswa)
Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    // Questions Routes
    Route::get('materials/questions', [MaterialQuestionController::class, 'index'])->name('materials.questions.index');

    // Materials Index & Show
    Route::get('materials', [MahasiswaMaterialController::class, 'index'])->name('materials.index');
    Route::get('materials/{material}', [MahasiswaMaterialController::class, 'show'])->name('materials.show');
    
    Route::get('materials/{material}/questions', [MaterialQuestionController::class, 'show'])
        ->middleware(\App\Http\Middleware\BlockQuestionParameter::class)
        ->name('materials.questions.show');

    
    Route::get('materials/{material}/questions/levels', [MaterialQuestionController::class, 'showLevels'])
        ->name('materials.questions.levels');
    
    Route::get('materials/{material}/questions/review', [MaterialQuestionController::class, 'review'])
        ->name('materials.questions.review');

    Route::post('materials/{material}/questions/{question}/check', [MaterialQuestionController::class, 'checkAnswer'])
        ->name('materials.questions.check');
        
    Route::get('materials/{material}/questions/{question}/attempts', [MaterialQuestionController::class, 'getAttempts'])
        ->name('materials.questions.attempts');

    // Reset Progress (Accessible by both Auth & Guest)
    Route::post('materials/{material}/reset', function($material) {
        $controller = app()->make(MaterialController::class);
        
        if (auth()->check()) {
            return $controller->reset($material);
        } else {
            return $controller->guestReset($material);
        }
    })->name('materials.reset');
});

// UEQ Survey routes for mahasiswa
Route::prefix('mahasiswa')->name('mahasiswa.')->middleware(['auth'])->group(function () {
    Route::get('/ueq-survey', [MahasiswaUeqSurveyController::class, 'create'])->name('ueq.create');
    Route::post('/ueq-survey', [MahasiswaUeqSurveyController::class, 'store'])->name('ueq.store');
    Route::get('/ueq-survey/thankyou', [MahasiswaUeqSurveyController::class, 'thankyou'])->name('ueq.thankyou');
});
