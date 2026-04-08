<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\AdminDashboardServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(protected AdminDashboardServiceInterface $adminDashboardService)
    {
    }

    public function index(): Response
    {
        $user     = Auth::user();
        $userName = $user->name;
        $userRole = $user->role->role_name;

        $stats = $this->adminDashboardService->getDashboardStats();

        $totalStudents  = $stats['totalStudents'];
        $totalMaterials = $stats['totalMaterials'];
        $totalQuestions = $stats['totalQuestions'];
        $activeStudents = $stats['activeStudents'];

        $recentProgress   = $this->adminDashboardService->getRecentProgress(10);
        $studentProgress  = $this->adminDashboardService->getStudentProgressOverview(5);
        $materialStats    = $this->adminDashboardService->getMaterialStatistics();
        $popularMaterials = $this->adminDashboardService->getPopularMaterials(5);
        $studentAnalytics = $this->adminDashboardService->getStudentAnalytics();

        return $this->render('Admin/Dashboard/Index', compact(
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
            'studentAnalytics',
        ));
    }
}
