<?php

namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\MaterialRepositoryInterface;
use App\Repositories\Interfaces\ProgressRepositoryInterface;
use App\Models\User;
use App\Models\Material;
use App\Models\Question;
use Carbon\Carbon;

class AdminDashboardService extends BaseService
{
    protected $userRepo;
    protected $materialRepo;
    protected $progressRepo;

    public function __construct(
        UserRepositoryInterface $userRepo,
        MaterialRepositoryInterface $materialRepo,
        ProgressRepositoryInterface $progressRepo
    ) {
        $this->userRepo = $userRepo;
        $this->materialRepo = $materialRepo;
        $this->progressRepo = $progressRepo;
    }

    public function getDashboardStats()
    {
        return [
            'totalStudents' => User::where('role_id', 3)->count(),
            'totalMaterials' => Material::count(),
            'totalQuestions' => Question::count(),
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
        
        // Get all materials/config once to avoid N+1 inside loop if possible, 
        // but calculation below is complex per student.
        // Optimization: Fetch all materials with active config
        $materials = $this->materialRepo->getAllWithQuestionsAndConfigs();
        
        return $students->map(function($student) use ($materials) {
            // Calculate total configured questions
            $totalConfiguredQuestions = 0;
            foreach ($materials as $material) {
                $config = $material->questionBankConfigs->where('is_active', true)->first();
                if ($config) {
                    $totalConfiguredQuestions += $config->beginner_count + $config->medium_count + $config->hard_count;
                } else {
                    $totalConfiguredQuestions += $material->questions->count();
                }
            }
            
            // Calculate progress percentage based on configured questions
            $correctAnswers = $student->progress->where('is_correct', true)->count(); // This relies on eager loaded progress from repo
            
            $student->materials_progress = $totalConfiguredQuestions > 0 
                ? round(($correctAnswers / $totalConfiguredQuestions) * 100) 
                : 0;
            
            // Add last active timestamp
            $lastActivity = $student->progress->max('created_at');
            $student->last_active = $lastActivity ? Carbon::parse($lastActivity) : null;
            
            return $student;
        });
    }

    public function getMaterialStatistics()
    {
        $materials = $this->materialRepo->getAllWithQuestionsAndConfigs();
        $progressData = $this->progressRepo->getMaterialPerformanceStats();
        
        return $materials->map(function($material) use ($progressData) {
            // Get active configuration for this material
            $config = $material->questionBankConfigs->where('is_active', true)->first();
            
            // Calculate total configured questions
            $totalConfiguredQuestions = 0;
            if ($config) {
                $totalConfiguredQuestions = $config->beginner_count + $config->medium_count + $config->hard_count;
            } else {
                $totalConfiguredQuestions = $material->questions->count();
            }
            
            // Filter progress data for this material
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
}
