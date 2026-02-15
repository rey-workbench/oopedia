<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    DashboardController as AdminDashboardController,
    MaterialController as AdminMaterialController,
    AdminStudentController,
    QuestionController as AdminQuestionController,
    AdminUserController,
    PendingApprovalController,
    UeqSurveyController,
    SubMaterialController
};

Route::middleware('auth')->group(function () {
    // Pending Approval Route - accessible by any authenticated user
    Route::get('admin/pending-approval', [PendingApprovalController::class, 'index'])
        ->name('admin.pending-approval');
        
    Route::middleware(['role:1|2', 'admin.approved'])->name('admin.')->prefix('admin')->group(function () {
        // Dashboard
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Materials & Questions
        Route::resource('materials', AdminMaterialController::class);
        Route::get('materials/{material}/submaterials/json', [SubMaterialController::class, 'getJson'])->name('materials.submaterials.json');
        Route::resource('materials.submaterials', SubMaterialController::class);
        
        Route::resource('questions', AdminQuestionController::class);
        
        // Students management
        Route::controller(AdminStudentController::class)->group(function () {
            Route::get('students', 'index')->name('students.index');
            Route::post('students', 'store')->name('students.store');
            Route::get('students/{student}/progress', 'progress')->name('students.progress');
            Route::delete('students/{student}', 'destroy')->name('students.destroy');
            Route::get('students/import', 'showImportForm')->name('students.import');
            Route::post('students/import', 'processImport')->name('students.process-import');
            Route::get('students/download-template', 'downloadTemplate')->name('students.download-template');
        });

        // Admin management routes (only for superadmin - using 'role:1')
        Route::middleware(['role:1'])->group(function () {
            // Admin approval
            Route::get('/pending-admins', [AdminUserController::class, 'pendingAdmins'])->name('pending-admins');
            Route::post('/users/{user}/approve', [AdminUserController::class, 'approveAdmin'])->name('users.approve');
            Route::post('/users/{user}/reject', [AdminUserController::class, 'rejectAdmin'])->name('users.reject');
            
            // User import
            Route::get('/users/import', [AdminUserController::class, 'showImportForm'])->name('users.import');
            Route::post('/users/import', [AdminUserController::class, 'processImport'])->name('users.process-import');
            Route::get('/users/download-template', [AdminUserController::class, 'downloadTemplate'])->name('users.download-template');
            
            // User management
            Route::resource('users', AdminUserController::class)->except(['show']);

            // Admin UEQ Survey routes
            Route::get('/ueq-survey', [UeqSurveyController::class, 'index'])->name('ueq.index');
            Route::get('/ueq-survey/export', [UeqSurveyController::class, 'export'])->name('ueq.export');
            Route::get('/ueq/{user}/detail', [UeqSurveyController::class, 'detail'])->name('ueq.detail');
        });

        // Media routes
        Route::get('/media/delete/{id}', [AdminMaterialController::class, 'deleteMedia'])
            ->name('media.delete');
    });
});
