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

// Private Mahasiswa Routes (authenticated role 3 only - using 'role.mahasiswa' alias)
Route::middleware(['auth', 'role.mahasiswa'])->name('mahasiswa.')->prefix('mahasiswa')->group(function () {
    // Dashboard
    Route::get('dashboard', [MahasiswaDashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/in-progress', [MahasiswaDashboardController::class, 'inProgress'])->name('dashboard.in-progress');
    Route::get('dashboard/completed', [MahasiswaDashboardController::class, 'complete'])->name('dashboard.completed');
    
    // Profile
    Route::get('profile', [MahasiswaProfileController::class, 'show'])->name('profile');
    Route::put('profile', [MahasiswaProfileController::class, 'update'])->name('profile.update');
    
    // Leaderboard
    Route::get('/leaderboard', [MahasiswaController::class, 'leaderboard'])->name('leaderboard');

    // UEQ Survey routes
    Route::get('/ueq-survey', [MahasiswaUeqSurveyController::class, 'create'])->name('ueq.create');
    Route::post('/ueq-survey', [MahasiswaUeqSurveyController::class, 'store'])->name('ueq.store');
    Route::get('/ueq-survey/thankyou', [MahasiswaUeqSurveyController::class, 'thankyou'])->name('ueq.thankyou');
});

// Features accessible by Guests (role 4) and Authenticated Students (role 3)
// We use 'guest.access' to manage these permissions
Route::middleware(['guest.access'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    // Materials Index & Show
    Route::get('materials', [MahasiswaMaterialController::class, 'index'])->name('materials.index');
    Route::get('materials/{material}', [MahasiswaMaterialController::class, 'show'])->name('materials.show');
    
    // Sub-Material Detail
    Route::get('materials/{material}/submaterials/{submaterial}', [MahasiswaMaterialController::class, 'showSubMaterial'])->name('submaterials.show');
    
    // Questions Features
    Route::get('materials/questions', [MaterialQuestionController::class, 'index'])->name('materials.questions.index');
    
    Route::get('materials/{material}/questions', [MaterialQuestionController::class, 'show'])
        ->middleware(\App\Http\Middleware\BlockQuestionParameter::class)
        ->name('materials.questions.show');
    
    Route::get('materials/{material}/questions/levels', [MaterialQuestionController::class, 'levels'])->name('materials.questions.levels');
    Route::get('materials/{material}/questions/review', [MaterialQuestionController::class, 'review'])->name('materials.questions.review');
    Route::post('materials/{material}/questions/{question}/check', [MaterialQuestionController::class, 'checkAnswer'])->name('materials.questions.check');
    Route::get('materials/{material}/questions/{question}/attempts', [MaterialQuestionController::class, 'getAttempts'])->name('materials.questions.attempts');

    // Reset Progress (Logic inside controller handles Auth vs Guest)
    Route::post('materials/{material}/reset', function($material) {
        $controller = app()->make(MaterialController::class);
        return auth()->check() ? $controller->reset($material) : $controller->guestReset($material);
    })->name('materials.reset');
});
