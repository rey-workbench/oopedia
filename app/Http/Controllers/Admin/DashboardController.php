<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Question;
use App\Services\AdminDashboardService;

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

        return view('admin.dashboard.index', compact(
            'userName',
            'userRole',
            'totalStudents',
            'totalMaterials',
            'totalQuestions',
            'activeStudents',
            'recentProgress',
            'studentProgress',
            'materialStats',
            'popularMaterials'
        ));
    }

    public function dashboard()
    {
        // Get all students (assuming role_id 3 is for students)
        // Note: The original controller logic for this method seemed a bit disconnected from the index method
        // and calculated progress differently. Preserving basic logic using Eloquent for now or
        // we can delegate to service if needed. For now, we'll keep it simple as it seems to be an alternative view.
        
        $students = User::where('role_id', 3)
            ->with(['answers']) 
            ->get()
            ->map(function ($student) {
                // Calculate progress percentage
                $totalQuestions = Question::count();
                $answeredQuestions = $student->answers->unique('question_id')->count();
                
                $progress = $totalQuestions > 0 
                    ? min(100, round(($answeredQuestions / $totalQuestions) * 100)) 
                    : 0;

                $student->progress = $progress;
                return $student;
            });

        return view('admin.dashboard', [
            'students' => $students,
            'activePage' => 'dashboard',
            'userName' => auth()->user()->name,
            'userRole' => 'Admin'
        ]);
    }
}