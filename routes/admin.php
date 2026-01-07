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
    QuestionBankController,
    AdaptiveRuleController,
    FormulaController,
    AttributeDefinitionController
};
use App\Http\Controllers\Auth\LogoutController;

Route::middleware('auth')->group(function () {
    // Pending Approval Route - accessible by any authenticated user
    Route::get('admin/pending-approval', [PendingApprovalController::class, 'index'])
        ->name('admin.pending-approval');
        
    // Admin Routes (role 1 = superadmin, role 2 = admin)
    Route::middleware(['role:1|2', 'admin.approved'])->name('admin.')->prefix('admin')->group(function () {
        // Dashboard
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Materials & Questions
        Route::resource('materials', AdminMaterialController::class);
        Route::resource('questions', AdminQuestionController::class);
        Route::resource('materials.questions', AdminQuestionController::class)->except(['show']);
        
        // Students management
        Route::controller(AdminStudentController::class)->group(function () {
            Route::get('students', 'index')->name('students.index');
            Route::get('students/{student}/progress', 'progress')->name('students.progress');
            Route::delete('students/{student}', 'destroy')->name('students.destroy');
            Route::get('students/import', 'showImportForm')->name('students.import');
            Route::post('students/import', 'processImport')->name('students.process-import');
            Route::get('students/download-template', 'downloadTemplate')->name('students.download-template');
        });

        // Admin management routes (only for superadmin - role 1)
        Route::middleware(['superadmin'])->group(function () {
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
        });

        // Question Banks routes
        Route::resource('question-banks', QuestionBankController::class);
        Route::get('question-banks/{questionBank}/manage-questions', [QuestionBankController::class, 'manageQuestions'])
            ->name('question-banks.manage-questions');
        Route::post('question-banks/{questionBank}/add-question/{question}', [QuestionBankController::class, 'addQuestion'])
            ->name('question-banks.add-question');
        Route::delete('question-banks/{questionBank}/remove-question/{question}', [QuestionBankController::class, 'removeQuestion'])
            ->name('question-banks.remove-question');
        Route::get('question-banks/{questionBank}/configure', [QuestionBankController::class, 'configureBank'])
            ->name('question-banks.configure');
        Route::post('question-banks/{questionBank}/configure', [QuestionBankController::class, 'storeConfig'])
            ->name('question-banks.store-config');
        Route::delete('question-bank-configs/{config}', [QuestionBankController::class, 'deleteConfig'])
            ->name('question-bank-configs.delete');

        // Adaptive Rules routes
        Route::resource('adaptive-rules', AdaptiveRuleController::class);
        Route::patch('adaptive-rules/{adaptiveRule}/toggle-status', [AdaptiveRuleController::class, 'toggleStatus'])
            ->name('adaptive-rules.toggle-status');
        
        Route::resource('formulas', FormulaController::class);
        Route::patch('formulas/{formula}/toggle-status', [FormulaController::class, 'toggleStatus'])
            ->name('formulas.toggle-status');
        
        // Attribute Definitions routes
        Route::get('attribute-definitions', [AttributeDefinitionController::class, 'index'])->name('attribute-definitions.index');
        Route::post('attribute-definitions', [AttributeDefinitionController::class, 'store']);

        // Media routes
        Route::get('/media/delete/{id}', [AdminMaterialController::class, 'deleteMedia'])
            ->name('media.delete');
            
        // Admin UEQ Survey routes
        Route::get('/ueq-survey', [UeqSurveyController::class, 'index'])->name('ueq.index');
        Route::get('/ueq-survey/export', [UeqSurveyController::class, 'export'])->name('ueq.export');
        Route::get('/ueq/{user}/detail', [UeqSurveyController::class, 'detail'])->name('ueq.detail');
    });
});
