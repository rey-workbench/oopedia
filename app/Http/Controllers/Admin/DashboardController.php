<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\AdminDashboardServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

final class DashboardController extends Controller
{
    public function __construct(private readonly AdminDashboardServiceInterface $adminDashboardService) {}

    public function index(): Response
    {
        $user     = Auth::user();
        $userName = $user->name;
        $userRole = $user->role->role_name;

        $stats = $this->adminDashboardService->getDashboardStats();

        $total_students  = $stats['total_students'];
        $total_materials = $stats['total_materials'];
        $total_questions = $stats['total_questions'];
        $active_students = $stats['active_students'];

        $recent_progress                = $this->adminDashboardService->getRecentProgress(10);
        $student_progress               = $this->adminDashboardService->getStudentProgressOverview(5);
        $materialStatistics             = $this->adminDashboardService->getMaterialStatistics();
        $popular_materials              = $this->adminDashboardService->getPopularMaterials(5);
        $student_analytics              = $this->adminDashboardService->getStudentAnalytics();
        $studentsNeedingAttention       = $this->adminDashboardService->getStudentsNeedingAttention();

        return $this->render('Admin/Dashboard/Index', [
            'user_name'                  => $userName,
            'user_role'                  => $userRole,
            'total_students'             => $total_students,
            'total_materials'            => $total_materials,
            'total_questions'            => $total_questions,
            'active_students'            => $active_students,
            'recent_progress'            => $recent_progress,
            'student_progress'           => $student_progress,
            'material_stats'             => $materialStatistics,
            'popular_materials'          => $popular_materials,
            'student_analytics'          => $student_analytics,
            'students_needing_attention' => $studentsNeedingAttention,
        ]);
    }
}
