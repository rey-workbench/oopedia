<?php

use App\Http\Controllers\Mahasiswa\CertificateController as MahasiswaCertificateController;
use App\Http\Controllers\Mahasiswa\DashboardController as MahasiswaDashboardController;
use App\Http\Controllers\Mahasiswa\MaterialController as MahasiswaMaterialController;
use App\Http\Controllers\Mahasiswa\MaterialQuestionController;
use App\Http\Controllers\Mahasiswa\MslqController;
use App\Http\Controllers\Mahasiswa\ProfileController as MahasiswaProfileController;
use App\Http\Controllers\Mahasiswa\UeqSurveyController as MahasiswaUeqSurveyController;
use App\Http\Middleware\BlockQuestionParameter;
use Illuminate\Support\Facades\Route;

// Private Mahasiswa Routes (authenticated role 3 only)
Route::middleware(['auth', 'access:mahasiswa'])->name('mahasiswa.')->prefix('mahasiswa')->group(function () {
    // Dashboard
    Route::get('dashboard', [MahasiswaDashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/in-progress', [MahasiswaDashboardController::class, 'inProgress'])->name('dashboard.in-progress');
    Route::get('dashboard/completed', [MahasiswaDashboardController::class, 'complete'])->name('dashboard.completed');
    Route::get('leaderboard', [MahasiswaDashboardController::class, 'leaderboard'])->name('leaderboard');

    // Profile (singleton: no ID needed, single user)
    Route::singleton('profile', MahasiswaProfileController::class)->only(['show', 'update']);

    Route::get('certificates', [MahasiswaCertificateController::class, 'index'])->name('certificates.index');

    // UEQ Survey (resource)
    Route::resource('ueq-survey', MahasiswaUeqSurveyController::class)->only(['create', 'store', 'show']);

    // MSLQ Survey
    Route::get('mslq/thankyou', [MslqController::class, 'show'])->name('mslq.thankyou');
    Route::resource('mslq', MslqController::class)->only(['create', 'store']);
});

// Features accessible by Guests (role 4) and Authenticated Students (role 3)
// Using access middleware - accepts guest, mahasiswa roles
Route::middleware(['access:guest'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    // Questions (must be before resource to avoid {material} catching 'questions')
    Route::get('materials/questions', [MaterialQuestionController::class, 'index'])->name('materials.questions.index');

    // Materials (resource: index + show only)
    Route::resource('materials', MahasiswaMaterialController::class)->only(['index', 'show']);

    // Sub-Material Detail
    Route::get('materials/{material}/submaterials/{submaterial}', [MahasiswaMaterialController::class, 'showSubMaterial'])->name('submaterials.show');

    // Reset Progress (Controller method handles Auth vs Guest internally)
    Route::post('materials/{material}/reset', [MahasiswaMaterialController::class, 'reset'])->name('materials.reset');

    // Questions (nested under material)
    Route::get('materials/{material}/questions/levels', [MaterialQuestionController::class, 'levels'])->name('materials.questions.levels');
    Route::get('materials/{material}/questions/review/{difficulty?}', [MaterialQuestionController::class, 'review'])->name('materials.questions.review');
    Route::post('materials/{material}/questions/{question}/check', [MaterialQuestionController::class, 'checkAnswer'])->name('materials.questions.check');
    Route::get('materials/{material}/adaptive/target-difficulty', [MaterialQuestionController::class, 'getTargetDifficulty'])->name('adaptive.target-difficulty');
    Route::get('materials/{material}/questions/{question}/attempts', [MaterialQuestionController::class, 'getAttempts'])->name('materials.questions.attempts');
    Route::get('materials/{material}/questions/{sub_material?}', [MaterialQuestionController::class, 'show'])
        ->middleware(BlockQuestionParameter::class)
        ->name('materials.questions.show');
});
