<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminDashboardService;
use App\Models\AdaptiveRule;
use App\Models\Formula;
use App\Models\AttributeDefinition;

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

        // Adaptive System Stats
        $totalRules = AdaptiveRule::count();
        $activeRules = AdaptiveRule::where('is_active', true)->count();
        $totalFormulas = Formula::count();
        $activeFormulas = Formula::where('is_active', true)->count();
        $totalAttributes = AttributeDefinition::count();

        // Recent Student Progress
        $recentProgress = $this->adminDashboardService->getRecentProgress(10);

        // Student Progress Overview
        $studentProgress = $this->adminDashboardService->getStudentProgressOverview(5);

        // Material Statistics for Chart
        $materialStats = $this->adminDashboardService->getMaterialStatistics();

        // Popular Materials
        $popularMaterials = $this->adminDashboardService->getPopularMaterials(5);

        return view('admin.dashboard.index', compact(
            'userName',
            'userRole',
            'totalStudents',
            'totalMaterials',
            'totalQuestions',
            'activeStudents',
            'totalRules',
            'activeRules',
            'totalFormulas',
            'activeFormulas',
            'totalAttributes',
            'recentProgress',
            'studentProgress',
            'materialStats',
            'popularMaterials'
        ));
    }
}