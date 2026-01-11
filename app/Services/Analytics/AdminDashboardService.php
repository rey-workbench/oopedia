<?php

namespace App\Services\Analytics;

use App\Repositories\UserRepository;
use App\Repositories\MaterialRepository;
use App\Repositories\ProgressRepository;
use App\Repositories\QuestionRepository;
use Carbon\Carbon;

class AdminDashboardService
{
    protected $userRepo;
    protected $materialRepo;
    protected $progressRepo;
    protected $questionRepo;

    public function __construct(
        UserRepository $userRepo,
        MaterialRepository $materialRepo,
        ProgressRepository $progressRepo,
        QuestionRepository $questionRepo
    ) {
        $this->userRepo = $userRepo;
        $this->materialRepo = $materialRepo;
        $this->progressRepo = $progressRepo;
        $this->questionRepo = $questionRepo;
    }

    public function getDashboardStats()
    {
        return [
            'totalStudents' => $this->userRepo->countByRole(3),
            'totalMaterials' => $this->materialRepo->countAll(),
            'totalQuestions' => $this->questionRepo->countAll(),
            'activeStudents' => $this->userRepo->getActiveStudentsCount(7)
        ];
    }

    public function getRecentProgress($limit = 10)
    {
        return $this->progressRepo->getRecentSystemProgress($limit);
    }

    public function getStudentProgressOverview($limit = 5)
    {
        $students = $this->userRepo->getStudentProgressOverview($limit);
        
        $materials = $this->materialRepo->getAllWithQuestionsAndConfigs();
        
        $totalConfiguredQuestions = 0;
        foreach ($materials as $material) {
            $totalConfiguredQuestions += $material->questions->count();
        }

        return $students->map(function($student) use ($totalConfiguredQuestions) {
            
            $uniqueCorrectQuestions = $student->quizAttempts->where('is_correct', true)->pluck('question_id')->unique()->count();
            
            $student->materials_progress = $totalConfiguredQuestions > 0 
                ? round(($uniqueCorrectQuestions / $totalConfiguredQuestions) * 100) 
                : 0;
            
            // Add last active timestamp
            $lastActivity = $student->quizAttempts->max('created_at');
            $student->last_active = $lastActivity ? Carbon::parse($lastActivity) : null;
            
            return $student;
        });
    }

    public function getMaterialStatistics()
    {
        $materials = $this->materialRepo->getAllWithQuestionsAndConfigs();
        $progressData = $this->progressRepo->getMaterialPerformanceStats();
        
        return $materials->map(function($material) use ($progressData) {
            // Use all available questions
            $totalConfiguredQuestions = $material->questions->count();
            
            // Filter progress data for this material
            // progressData contains objects with material_id (from join in repo)
            $materialProgress = $progressData->where('material_id', $material->id);
            
            // Count unique users who have answered questions in this material
            $activeStudents = $materialProgress->pluck('user_id')->unique()->count();
            
            // Count unique correctly answered questions (progressData is already filtered by is_correct=true in repo)
            $correctlyAnsweredQuestions = $materialProgress->pluck('question_id')->unique()->count();
            
            // Calculate completion rate
            $completionRate = $totalConfiguredQuestions > 0 
                ? round(($correctlyAnsweredQuestions / $totalConfiguredQuestions) * 100, 1)
                : 0;
            
            return (object)[
                'id' => $material->id,
                'title' => $material->title,
                'questions_count' => $totalConfiguredQuestions,
                'active_students' => $activeStudents,
                'completion_rate' => $completionRate
            ];
        });
    }

    public function getPopularMaterials($limit = 5)
    {
        return $this->progressRepo->getPopularMaterials($limit);
    }

    public function getStudentAnalytics()
    {
        $allStudents = $this->userRepo->getUsersByRoleAndApproval(3, true, null, null);
        $materials = $this->materialRepo->getAllWithQuestionsAndConfigs();
        $totalConfiguredQuestions = $materials->sum(fn($m) => $m->questions->count());

        $distribution = [
            '0%' => 0,
            '1-25%' => 0,
            '26-50%' => 0,
            '51-75%' => 0,
            '76-100%' => 0
        ];

        foreach ($allStudents as $student) {
            $correctCount = $student->quizAttempts->where('is_correct', true)->pluck('question_id')->unique()->count();
            $progress = $totalConfiguredQuestions > 0 ? ($correctCount / $totalConfiguredQuestions) * 100 : 0;

            if ($progress == 0) $distribution['0%']++;
            elseif ($progress <= 25) $distribution['1-25%']++;
            elseif ($progress <= 50) $distribution['26-50%']++;
            elseif ($progress <= 75) $distribution['51-75%']++;
            else $distribution['76-100%']++;
        }

        $moduleStats = $this->getMaterialStatistics();

        return [
            'distribution' => $distribution,
            'modulePerformance' => [
                'labels' => $moduleStats->pluck('title'),
                'data' => $moduleStats->pluck('completion_rate')
            ]
        ];
    }
}
