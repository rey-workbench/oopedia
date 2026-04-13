<?php

use App\Http\Controllers\Admin\AdminStudentController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MaterialController as AdminMaterialController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MslqController;
use App\Http\Controllers\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\Admin\SubMaterialController;
use App\Http\Controllers\Admin\UeqSurveyController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('admin/pending-approval', [AdminUserController::class, 'pendingApproval'])
        ->name('admin.pending-approval');

    Route::middleware(['access:superadmin|dosen,true'])->name('admin.')->prefix('admin')->group(function () {
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::post('media/upload', [MediaController::class, 'upload'])->name('media.upload');
        Route::delete('media', [MediaController::class, 'delete'])->name('media.delete');

        Route::resource('materials', AdminMaterialController::class);
        Route::get('materials/{material}/submaterials/json', [SubMaterialController::class, 'getJson'])->name('materials.submaterials.json');
        Route::resource('materials.submaterials', SubMaterialController::class);

        Route::resource('questions', AdminQuestionController::class);

        Route::get('students/import', [AdminStudentController::class, 'showImportForm'])->name('students.import');
        Route::post('students/import', [AdminStudentController::class, 'processImport'])->name('students.process-import');
        Route::get('students/download-template', [AdminStudentController::class, 'downloadTemplate'])->name('students.download-template');
        Route::resource('students', AdminStudentController::class)->only(['index', 'store', 'show', 'destroy']);

        Route::middleware(['access:superadmin'])->group(function () {
            Route::get('/pending-admins', [AdminUserController::class, 'pendingAdmins'])->name('pending-admins');
            Route::post('/users/{user}/approve', [AdminUserController::class, 'approveAdmin'])->name('users.approve');
            Route::post('/users/{user}/reject', [AdminUserController::class, 'rejectAdmin'])->name('users.reject');

            Route::get('/users/import', [AdminUserController::class, 'showImportForm'])->name('users.import');
            Route::post('/users/import', [AdminUserController::class, 'processImport'])->name('users.process-import');
            Route::get('/users/download-template', [AdminUserController::class, 'downloadTemplate'])->name('users.download-template');

            Route::resource('users', AdminUserController::class)->except(['show']);

            Route::get('ueq-survey/export', [UeqSurveyController::class, 'export'])->name('ueq-survey.export');
            Route::resource('ueq-survey', UeqSurveyController::class)->only(['index', 'show']);

            Route::get('mslq/export', [MslqController::class, 'export'])->name('mslq.export');
            Route::resource('mslq', MslqController::class)->only(['index', 'show']);
        });

        Route::get('/media/delete/{id}', [AdminMaterialController::class, 'deleteMedia'])
            ->name('media.delete');
    });
});
