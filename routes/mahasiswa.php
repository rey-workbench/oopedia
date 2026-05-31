<?php

declare(strict_types=1);

use App\Http\Controllers\Mahasiswa\CertificateController as MahasiswaCertificateController;
use App\Http\Controllers\Mahasiswa\DashboardController as MahasiswaDashboardController;
use App\Http\Controllers\Mahasiswa\MaterialController as MahasiswaMaterialController;
use App\Http\Controllers\Mahasiswa\MaterialQuestionController;
use App\Http\Controllers\Mahasiswa\MslqController;
use App\Http\Controllers\Mahasiswa\ProfileController as MahasiswaProfileController;
use App\Http\Controllers\Mahasiswa\QuizInteractionController;
use App\Http\Controllers\Mahasiswa\SusSurveyController;
use App\Http\Controllers\Mahasiswa\UeqSurveyController as MahasiswaUeqSurveyController;
use App\Http\Middleware\BlockQuestionParameter;
use Illuminate\Support\Facades\Route;

Route::prefix('mahasiswa')->name('mahasiswa.')->group(function (): void {

    // --- Public Certificate Preview ---
    Route::get('certificates/preview/{material}/{user?}', [MahasiswaCertificateController::class, 'preview'])
        ->name('certificates.preview');

    // --- Private Mahasiswa Routes (Role 3 Only) ---
    Route::middleware(['auth', 'access:mahasiswa'])->group(function (): void {
        // Dashboard
        Route::controller(MahasiswaDashboardController::class)->group(function (): void {
            Route::get('dashboard', 'index')->name('dashboard');
            Route::get('dashboard/in-progress', 'inProgress')->name('dashboard.in-progress');
            Route::get('dashboard/completed', 'complete')->name('dashboard.completed');
            Route::get('leaderboard', 'leaderboard')->name('leaderboard');
        });

        // Profile & Certificates
        Route::singleton('profile', MahasiswaProfileController::class)->only(['show', 'update']);
        Route::prefix('certificates')->name('certificates.')->controller(MahasiswaCertificateController::class)->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::get('{material}/download', 'download')->name('download');
        });

        // Surveys (UEQ, MSLQ, SUS)
        Route::prefix('surveys')->name('surveys.')->group(function (): void {
            Route::prefix('ueq')->name('ueq.')->controller(MahasiswaUeqSurveyController::class)->group(function (): void {
                Route::get('thankyou', 'show')->name('thankyou');
            });
            Route::resource('ueq', MahasiswaUeqSurveyController::class)->only(['create', 'store']);

            Route::prefix('mslq')->name('mslq.')->controller(MslqController::class)->group(function (): void {
                Route::get('thankyou', 'show')->name('thankyou');
            });
            Route::resource('mslq', MslqController::class)->only(['create', 'store']);

            Route::prefix('sus')->name('sus.')->controller(SusSurveyController::class)->group(function (): void {
                Route::get('thankyou', 'show')->name('thankyou');
            });
            Route::resource('sus', SusSurveyController::class)->only(['create', 'store']);
        });
    });

    // --- Shared/Guest Routes (Role 3 & 4) ---
    Route::middleware(['access:guest'])->group(function (): void {

        // Questions & Adaptive System (Page Rendering)
        Route::controller(MaterialQuestionController::class)->prefix('materials')->group(function (): void {
            Route::get('questions', 'index')->name('materials.questions.index');
            Route::get('{material}/questions/levels', 'levels')->name('materials.questions.levels');
            Route::get('{material}/questions/review/{difficulty?}', 'review')->name('materials.questions.review');
            Route::get('{material}/questions', 'show')
                ->middleware(BlockQuestionParameter::class)
                ->name('materials.questions.show');
        });

        // Quiz Interactions (API/Action Logic)
        Route::controller(QuizInteractionController::class)->prefix('materials')->group(function (): void {
            Route::post('{material}/questions/{question}/check', 'submit')
                ->name('materials.questions.check')
                ->middleware('throttle:15,1');
            Route::post('{material}/questions/{question}/hint', 'useHint')->name('materials.questions.hint');
        });

        // Materials & Progress
        Route::controller(MahasiswaMaterialController::class)->group(function (): void {
            Route::resource('materials', MahasiswaMaterialController::class)->only(['index', 'show']);
            Route::post('materials/{material}/reset', 'reset')->name('materials.reset');
        });
    });
});
