<?php

namespace App\Services;

use App\Repositories\MaterialRepository;
use App\Repositories\ProgressRepository;
use App\Models\QuestionBankConfig;

class DashboardService
{
    protected $materialRepo;
    protected $progressRepo;

    public function __construct(
        MaterialRepository $materialRepo,
        ProgressRepository $progressRepo
    ) {
        $this->materialRepo = $materialRepo;
        $this->progressRepo = $progressRepo;
    }

    public function getAllMaterials()
    {
        return $this->materialRepo->getAllWithQuestions();
    }

    public function getDashboardIndexData($userId, $isGuest)
    {
        // Get all materials
        $allMaterials = $this->materialRepo->getAllWithQuestions();
        $totalMaterials = $allMaterials->count();

        // Variables to store configured question counts
        $configuredTotalQuestions = 0;
        $configuredEasyQuestions = 0;
        $configuredMediumQuestions = 0;
        $configuredHardQuestions = 0;

        // Calculate configured question counts
        foreach ($allMaterials as $material) {
            $counts = $this->calculateConfiguredQuestions($material, $isGuest);
            $configuredEasyQuestions += $counts['easy'];
            $configuredMediumQuestions += $counts['medium'];
            $configuredHardQuestions += $counts['hard'];
        }

        $configuredTotalQuestions = $configuredEasyQuestions + $configuredMediumQuestions + $configuredHardQuestions;

        // Get progress statistics
        $progressStats = $this->progressRepo->getUserProgressStats($userId);

        // Calculate material statistics
        $completedMaterials = 0;
        $inProgressMaterials = 0;
        $totalMaterialProgress = 0;

        // Calculate question statistics
        $totalAnsweredQuestions = 0;
        $totalCorrectQuestions = 0;

        $materials = $this->materialRepo->getAllWithQuestionsAndConfigs()->map(function ($material) use ($progressStats) {
            // Get active configuration
            $config = $material->questionBankConfigs->where('is_active', true)->first();

            // Calculate total configured questions
            if ($config) {
                $totalQuestions = $config->beginner_count + $config->medium_count + $config->hard_count;
            } else {
                $totalQuestions = $material->questions->count();
            }

            // progressStats is a Collection of objects with material_id, correct_answers
            $materialProgress = $progressStats->firstWhere('material_id', $material->id);
            $correctAnswers = $materialProgress ? $materialProgress->correct_answers : 0;

            $progressPercentage = $totalQuestions > 0
                ? min(100, round(($correctAnswers / $totalQuestions) * 100))
                : 0;

            return (object)[
                'id' => $material->id,
                'title' => $material->title,
                'description' => $material->description,
                'progress_percentage' => $progressPercentage,
                'total_questions' => $totalQuestions,
                'completed_questions' => $correctAnswers
            ];
        });

        // Calculate overall progress percentages
        $totalCorrectQuestions = $progressStats->sum('correct_answers');

        $materialProgressPercentage = $totalMaterials > 0 ? round(($completedMaterials / $totalMaterials) * 100) : 0;
        $questionProgressPercentage = $configuredTotalQuestions > 0 ? round(($totalCorrectQuestions / $configuredTotalQuestions) * 100) : 0;

        // Get recent activities
        $recentActivities = $this->progressRepo->getRecentActivities($userId, 5);

        // Transform recent activities to add type
        $recentActivities = $recentActivities->map(function ($activity) {
            $activity->type = $this->determineActivityType($activity);
            return $activity;
        });

        return [
            'totalMaterials' => $totalMaterials,
            'totalQuestions' => $configuredTotalQuestions,
            'easyQuestions' => $configuredEasyQuestions,
            'mediumQuestions' => $configuredMediumQuestions,
            'hardQuestions' => $configuredHardQuestions,
            'materialProgressPercentage' => $materialProgressPercentage,
            'questionProgressPercentage' => $questionProgressPercentage,
            'completedMaterials' => $completedMaterials,
            'inProgressMaterials' => $inProgressMaterials,
            'totalMaterialProgress' => $totalMaterialProgress,
            'totalAnsweredQuestions' => $totalAnsweredQuestions,
            'totalCorrectQuestions' => $totalCorrectQuestions,
            'recentActivities' => $recentActivities,
            'allMaterials' => $materials
        ];
    }

    public function getInProgressData($userId, $isGuest)
    {
        $progressStats = $this->progressRepo->getDetailedUserProgress($userId);
        $materialProgress = $this->progressRepo->getUserMaterialProgress($userId);

        $materials = $this->materialRepo->getAllWithQuestions()
            ->filter(function ($material) use ($materialProgress) {
                $progress = $materialProgress->firstWhere('material_id', $material->id);
                $totalQuestions = $material->questions->count();

                if ($progress && $totalQuestions > 0) {
                    $correctAnswers = $progress->correct_answers;
                    return $correctAnswers > 0 && $correctAnswers < $totalQuestions;
                }

                return false;
            });

        return $this->processMaterialsWithStats($materials, $progressStats, $isGuest);
    }

    public function getCompletedData($userId, $isGuest)
    {
        $progressStats = $this->progressRepo->getDetailedUserProgress($userId);
        $materialProgress = $this->progressRepo->getUserMaterialProgress($userId);

        $materials = $this->materialRepo->getAllWithQuestions()
            ->filter(function ($material) use ($materialProgress, $isGuest) {
                // Ensure materialProgress objects have material_id and correct_answers
                $progress = $materialProgress->firstWhere('material_id', $material->id);

                if ($progress) {
                    $correctAnswers = $progress->correct_answers;
                    $configuredTotalQuestions = $this->calculateConfiguredTotalQuestions($material, $isGuest);
                    return $correctAnswers >= $configuredTotalQuestions;
                }

                return false;
            });

        // For completed, we force 100% stats as per original controller
        return $materials->map(function ($material) use ($isGuest) {
             $counts = $this->calculateConfiguredQuestions($material, $isGuest);
             // Logic from original 'complete' method
             return [
                'material' => $material,
                'stats' => [
                    'overall' => [
                        'correct' => $counts['total'],
                        'total' => $counts['total'],
                        'percentage' => 100
                    ],
                    'beginner' => [
                        'correct' => $counts['easy'],
                        'total' => $material->questions->where('difficulty', 'beginner')->count(),
                        'configured_total' => $counts['easy'],
                        'percentage' => 100
                    ],
                    'medium' => [
                        'correct' => $counts['medium'],
                        'total' => $material->questions->where('difficulty', 'medium')->count(),
                        'configured_total' => $counts['medium'],
                        'percentage' => 100
                    ],
                    'hard' => [
                        'correct' => $counts['hard'],
                        'total' => $material->questions->where('difficulty', 'hard')->count(),
                        'configured_total' => $counts['hard'],
                        'percentage' => 100
                    ]
                ]
            ];
        });
    }

    protected function calculateConfiguredQuestions($material, $isGuest)
    {
        $easy = 0;
        $medium = 0;
        $hard = 0;

        if ($isGuest) {
            $easy = min(3, $material->questions->where('difficulty', 'beginner')->count());
            $medium = min(3, $material->questions->where('difficulty', 'medium')->count());
            $hard = min(3, $material->questions->where('difficulty', 'hard')->count());
        } else {
            $config = QuestionBankConfig::where('material_id', $material->id)
                ->where('is_active', true)
                ->first();

            if ($config) {
                $easy = $config->beginner_count;
                $medium = $config->medium_count;
                $hard = $config->hard_count;
            } else {
                $easy = $material->questions->where('difficulty', 'beginner')->count();
                $medium = $material->questions->where('difficulty', 'medium')->count();
                $hard = $material->questions->where('difficulty', 'hard')->count();
            }
        }

        return [
            'easy' => $easy,
            'medium' => $medium,
            'hard' => $hard,
            'total' => $easy + $medium + $hard
        ];
    }

    protected function calculateConfiguredTotalQuestions($material, $isGuest)
    {
        $counts = $this->calculateConfiguredQuestions($material, $isGuest);
        return $counts['total'];
    }

    protected function processMaterialsWithStats($materials, $progressStats, $isGuest)
    {
        return $materials->map(function ($material) use ($progressStats, $isGuest) {
            $counts = $this->calculateConfiguredQuestions($material, $isGuest);

            $beginnerQuestions = $material->questions->where('difficulty', 'beginner');
            $mediumQuestions = $material->questions->where('difficulty', 'medium');
            $hardQuestions = $material->questions->where('difficulty', 'hard');

            // Stats
            $beginnerStats = $progressStats->where('material_id', $material->id)->where('difficulty', 'beginner')->first();
            $beginnerCorrect = $beginnerStats ? $beginnerStats->correct_answers : 0;
            $beginnerPercentage = $counts['easy'] > 0 ? round(($beginnerCorrect / $counts['easy']) * 100) : 0;

            $mediumStats = $progressStats->where('material_id', $material->id)->where('difficulty', 'medium')->first();
            $mediumCorrect = $mediumStats ? $mediumStats->correct_answers : 0;
            $mediumPercentage = $counts['medium'] > 0 ? round(($mediumCorrect / $counts['medium']) * 100) : 0;

            $hardStats = $progressStats->where('material_id', $material->id)->where('difficulty', 'hard')->first();
            $hardCorrect = $hardStats ? $hardStats->correct_answers : 0;
            $hardPercentage = $counts['hard'] > 0 ? round(($hardCorrect / $counts['hard']) * 100) : 0;

            $totalCorrect = $beginnerCorrect + $mediumCorrect + $hardCorrect;
            $overallPercentage = $counts['total'] > 0 ? round(($totalCorrect / $counts['total']) * 100) : 0;

            return [
                'material' => $material,
                'stats' => [
                    'overall' => [
                        'correct' => $totalCorrect,
                        'total' => $counts['total'],
                        'percentage' => $overallPercentage
                    ],
                    'beginner' => [
                        'correct' => $beginnerCorrect,
                        'total' => $beginnerQuestions->count(),
                        'configured_total' => $counts['easy'],
                        'percentage' => $beginnerPercentage
                    ],
                    'medium' => [
                        'correct' => $mediumCorrect,
                        'total' => $mediumQuestions->count(),
                        'configured_total' => $counts['medium'],
                        'percentage' => $mediumPercentage
                    ],
                    'hard' => [
                        'correct' => $hardCorrect,
                        'total' => $hardQuestions->count(),
                        'configured_total' => $counts['hard'],
                        'percentage' => $hardPercentage
                    ]
                ]
            ];
        });
    }

    protected function determineActivityType($activity)
    {
        if ($activity->total_correct >= 5) {
            return 'achievement';
        } elseif ($activity->difficulty === 'hard' && $activity->is_correct) {
            return 'milestone';
        } else {
            return 'progress';
        }
    }
}
