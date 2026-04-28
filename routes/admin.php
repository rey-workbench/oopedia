<?php

use App\Http\Controllers\Admin\AdaptiveActionController;
use App\Http\Controllers\Admin\AdaptiveRuleController;
use App\Http\Controllers\Admin\AdminStudentController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MaterialController as AdminMaterialController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MslqController;
use App\Http\Controllers\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\Admin\SusSurveyController as AdminSusSurveyController;
use App\Http\Controllers\Admin\UeqSurveyController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    // Jalur cepat untuk pending approval
    Route::get('pending-approval', [AdminUserController::class, 'pendingApproval'])->name('pending-approval');

    // Group Utama Admin (Superadmin & Dosen)
    Route::middleware(['access:superadmin|dosen,true'])->group(function () {

        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('adaptive-rules', AdaptiveRuleController::class);
        Route::resource('adaptive-actions', AdaptiveActionController::class)->only(['store', 'update', 'destroy']);

        // Media Management
        Route::controller(MediaController::class)->prefix('media')->name('media.')->group(function () {
            Route::post('upload', 'upload')->name('upload');
            Route::delete('/', 'delete')->name('delete');
        });
        Route::get('media/delete/{id}', [AdminMaterialController::class, 'deleteMedia'])->name('media.delete_alt');

        // Materials & Sub-materials
        Route::resource('materials', AdminMaterialController::class);

        // Questions
        Route::resource('questions', AdminQuestionController::class);

        // Students Management
        Route::controller(AdminStudentController::class)->prefix('students')->name('students.')->group(function () {
            Route::get('import', 'showImportForm')->name('import');
            Route::post('import', 'processImport')->name('process-import');
            Route::get('download-template', 'downloadTemplate')->name('download-template');
        });
        Route::resource('students', AdminStudentController::class)->only(['index', 'store', 'show', 'destroy']);

        // --- KHUSUS SUPERADMIN ---
        Route::middleware(['access:superadmin'])->group(function () {

            // User & Admin Approval Management
            Route::controller(AdminUserController::class)->prefix('users')->name('users.')->group(function () {
                Route::post('{user}/approve', 'approveAdmin')->name('approve');
                Route::post('{user}/reject', 'rejectAdmin')->name('reject');
                Route::get('import', 'showImportForm')->name('import');
                Route::post('import', 'processImport')->name('process-import');
                Route::get('download-template', 'downloadTemplate')->name('download-template');
            });
            Route::get('pending-admins', [AdminUserController::class, 'pendingAdmins'])->name('pending-admins');
            Route::resource('users', AdminUserController::class)->except(['show']);

            // Survey Management (UEQ, MSLQ, SUS)
            Route::prefix('surveys')->name('surveys.')->group(function () {
                Route::prefix('ueq')->name('ueq.')->controller(UeqSurveyController::class)->group(function () {
                    Route::get('export', 'export')->name('export');
                });
                Route::resource('ueq', UeqSurveyController::class)->only(['index', 'show']);

                Route::prefix('mslq')->name('mslq.')->controller(MslqController::class)->group(function () {
                    Route::get('export', 'export')->name('export');
                });
                Route::resource('mslq', MslqController::class)->only(['index', 'show']);

                Route::prefix('sus')->name('sus.')->controller(AdminSusSurveyController::class)->group(function () {
                    Route::get('export', 'export')->name('export');
                });
                Route::resource('sus', AdminSusSurveyController::class)->only(['index', 'show']);
            });
        });
    });
});
