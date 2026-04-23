<?php

use App\Http\Controllers\Mahasiswa\CertificateController as MahasiswaCertificateController;
use App\Http\Controllers\Mahasiswa\DashboardController as MahasiswaDashboardController;
use App\Http\Controllers\Mahasiswa\MaterialController as MahasiswaMaterialController;
use App\Http\Controllers\Mahasiswa\MaterialQuestionController;
use App\Http\Controllers\Mahasiswa\MslqController;
use App\Http\Controllers\Mahasiswa\ProfileController as MahasiswaProfileController;
use App\Http\Controllers\Mahasiswa\SusSurveyController;
use App\Http\Controllers\Mahasiswa\UeqSurveyController as MahasiswaUeqSurveyController;
use App\Http\Middleware\BlockQuestionParameter;
use Illuminate\Support\Facades\Route;

Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {

    // --- Private Mahasiswa Routes (Role 3 Only) ---
    Route::middleware(['auth', 'access:mahasiswa'])->group(function () {
        // Dashboard
        Route::controller(MahasiswaDashboardController::class)->group(function () {
            Route::get('dashboard', 'index')->name('dashboard');
            Route::get('dashboard/in-progress', 'inProgress')->name('dashboard.in-progress');
            Route::get('dashboard/completed', 'complete')->name('dashboard.completed');
            Route::get('leaderboard', 'leaderboard')->name('leaderboard');
        });

        // Profile & Certificates
        Route::singleton('profile', MahasiswaProfileController::class)->only(['show', 'update']);
        Route::get('certificates', [MahasiswaCertificateController::class, 'index'])->name('certificates.index');

        // Surveys (UEQ, MSLQ, SUS)
        Route::prefix('surveys')->name('surveys.')->group(function () {
            Route::prefix('ueq')->name('ueq.')->controller(MahasiswaUeqSurveyController::class)->group(function () {
                Route::get('thankyou', 'show')->name('thankyou');
            });
            Route::resource('ueq', MahasiswaUeqSurveyController::class)->only(['create', 'store']);

            Route::prefix('mslq')->name('mslq.')->controller(MslqController::class)->group(function () {
                Route::get('thankyou', 'show')->name('thankyou');
            });
            Route::resource('mslq', MslqController::class)->only(['create', 'store']);

            Route::prefix('sus')->name('sus.')->controller(SusSurveyController::class)->group(function () {
                Route::get('thankyou', 'show')->name('thankyou');
            });
            Route::resource('sus', SusSurveyController::class)->only(['create', 'store']);
        });
    });

    // --- Shared/Guest Routes (Role 3 & 4) ---
    Route::middleware(['access:guest'])->group(function () {

        // Materials & Progress
        Route::controller(MahasiswaMaterialController::class)->group(function () {
            Route::resource('materials', MahasiswaMaterialController::class)->only(['index', 'show']);
            Route::get('materials/{material}/submaterials/{submaterial}', 'showSubMaterial')->name('submaterials.show');
            Route::post('materials/{material}/reset', 'reset')->name('materials.reset');
        });

        // Questions & Adaptive System
        Route::controller(MaterialQuestionController::class)->prefix('materials')->group(function () {
            Route::get('questions', 'index')->name('materials.questions.index');
            Route::get('{material}/questions/levels', 'levels')->name('materials.questions.levels');
            Route::get('{material}/questions/review/{difficulty?}', 'review')->name('materials.questions.review');
            Route::post('{material}/questions/{question}/check', 'checkAnswer')->name('materials.questions.check');
            Route::get('{material}/adaptive/target-difficulty', 'getTargetDifficulty')->name('adaptive.target-difficulty');
            Route::get('{material}/questions/{question}/attempts', 'getAttempts')->name('materials.questions.attempts');
            Route::get('{material}/questions/{sub_material?}', 'show')
                ->middleware(BlockQuestionParameter::class)
                ->name('materials.questions.show');
        });
    });
});