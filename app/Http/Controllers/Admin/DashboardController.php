<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Analytics\AdminDashboardService;
use Inertia\Inertia;

class DashboardController extends Controller
{
    protected $adminDashboardService;

    public function __construct(AdminDashboardService $adminDashboardService)
    {
        $this->adminDashboardService = $adminDashboardService;
    }

    public function index()
    {
        // Get authenticated user data
        $user = auth()->user();
        $userName = $user->name;
        $userRole = $user->role->role_name;

        // Get Statistics
        $stats = $this->adminDashboardService->getDashboardStats();
        
        $totalStudents = $stats['totalStudents'];
        $totalMaterials = $stats['totalMaterials'];
        $totalQuestions = $stats['totalQuestions'];
        $activeStudents = $stats['activeStudents'];

        // Recent Student Progress
        $recentProgress = $this->adminDashboardService->getRecentProgress(10);

        // Student Progress Overview
        $studentProgress = $this->adminDashboardService->getStudentProgressOverview(5);

        // Material Statistics for Chart
        $materialStats = $this->adminDashboardService->getMaterialStatistics();

        // Popular Materials
        $popularMaterials = $this->adminDashboardService->getPopularMaterials(5);

        // Student Analytics for Charts
        $studentAnalytics = $this->adminDashboardService->getStudentAnalytics();

        return Inertia::render('Admin/Dashboard/Index', compact(
            'userName',
            'userRole',
            'totalStudents',
            'totalMaterials',
            'totalQuestions',
            'activeStudents',
            'recentProgress',
            'studentProgress',
            'materialStats',
            'popularMaterials',
            'studentAnalytics'
        ));
    }
}