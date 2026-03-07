<?php

namespace App\Services\Analytics;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Contracts\Services\DashboardServiceInterface;
use Illuminate\Support\Facades\Cache;
use App\Helpers\ProgressHelper;

class DashboardService implements DashboardServiceInterface
{
    public function __construct(protected
        MaterialRepositoryInterface $materialRepo, protected
        ProgressRepositoryInterface $progressRepo, protected
        QuestionRepositoryInterface $questionRepo,
        )
    {
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Material> */
    public function getAllMaterials(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->materialRepo->getAllWithQuestions();
    }

    /** @return array<string, mixed> */
    public function getDashboardIndexData(string $userId, bool $isGuest): array
    {
        // Cache user specific dashboard data for 5 minutes (300 seconds)
        return Cache::remember("dashboard_index_{$userId}_{$isGuest}", 300, function () use ($userId, $isGuest) {
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
                $counts = ProgressHelper::calculateMaterialQuestionCounts($material, $isGuest);
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

            $materials = $allMaterials->map(function ($material) use ($progressStats, $isGuest) {
                    // Calculate total questions (all available for logged-in, limited for guests)
                    $totalQuestions = ProgressHelper::calculateMaterialQuestionCounts($material, $isGuest)['total'];

                    $materialProgress = $progressStats->firstWhere('material_id', $material->id);
                    $correctAnswers = $materialProgress ? $materialProgress->correct_answers : 0;

                    $progressPercentage = ProgressHelper::calculateProgressPercentage($correctAnswers, $totalQuestions);

                    return (object)[
                    'id' => $material->id,
                    'title' => $material->title,
                    'description' => $material->description ?? '',
                    'progress_percentage' => $progressPercentage,
                    'total_questions' => $totalQuestions,
                    'completed_questions' => $correctAnswers,
                    ];
                }
                );

                // Calculate overall progress percentages
                $totalCorrectQuestions = $progressStats->sum('correct_answers');

                $materialProgressPercentage = ProgressHelper::calculateProgressPercentage($completedMaterials, $totalMaterials);
                $questionProgressPercentage = ProgressHelper::calculateProgressPercentage($totalCorrectQuestions, $configuredTotalQuestions);

                // Get recent activities
                $recentActivities = $this->progressRepo->getRecentActivities($userId, 10);

                // Transform recent activities to add type
                $recentActivities = $recentActivities->map(function ($activity) {
                    $activity->type = $this->determineActivityType($activity);

                    return $activity;
                }
                );

                // Deduplicate milestones and achievements by material to prevent spam
                $seenMilestones = [];
                $seenAchievements = [];
                $recentActivities = $recentActivities->filter(function ($activity) use (&$seenMilestones, &$seenAchievements) {
                    if ($activity->type === 'milestone') {
                        $key = $activity->material_id . '_milestone';
                        if (in_array($key, $seenMilestones)) {
                            return false; // Skip duplicate milestone for same material
                        }
                        $seenMilestones[] = $key;
                    }
                    elseif ($activity->type === 'achievement') {
                        $key = $activity->material_id . '_achievement';
                        if (in_array($key, $seenAchievements)) {
                            return false; // Skip duplicate achievement for same material
                        }
                        $seenAchievements[] = $key;
                    }

                    return true;
                }
                )->take(5)->values(); // Take only 5 after deduplication
    
                // Get student state for certifications and badges
                $studentState = \App\Models\StudentState::find($userId, ['*']);
                $certifications = $studentState ? ($studentState->learning_profile['certifications'] ?? []) : [];

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
                    'allMaterials' => $materials,
                    'certifications' => $certifications,
                ];
            });
    }

    /** @return array<int, array<string, mixed>> */
    public function getInProgressData(string $userId, bool $isGuest): array
    {
        return Cache::remember("dashboard_inprogress_{$userId}_{$isGuest}", 300, function () use ($userId, $isGuest) {
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
            }
            );

            return $this->processMaterialsWithStats($materials, $progressStats, $isGuest);
        });
    }

    /** @return array<int, array<string, mixed>> */
    public function getCompletedData(string $userId, bool $isGuest): array
    {
        return Cache::remember("dashboard_completed_{$userId}_{$isGuest}", 300, function () use ($userId, $isGuest) {
            $progressStats = $this->progressRepo->getDetailedUserProgress($userId);
            $materialProgress = $this->progressRepo->getUserMaterialProgress($userId);

            $materials = $this->materialRepo->getAllWithQuestions()
                ->filter(function ($material) use ($materialProgress, $isGuest) {
                $progress = $materialProgress->firstWhere('material_id', $material->id);

                if ($progress) {
                    $correctAnswers = $progress->correct_answers;
                    $configuredTotalQuestions = ProgressHelper::calculateMaterialQuestionCounts($material, $isGuest)['total'];

                    return $correctAnswers >= $configuredTotalQuestions;
                }

                return false;
            }
            );

            // For completed, we force 100% stats
            return $materials->map(function ($material) use ($isGuest) {
                    $counts = ProgressHelper::calculateMaterialQuestionCounts($material, $isGuest);
                    $beginnerTotal = $this->questionRepo->countByMaterialAndDifficulty($material->id, 'beginner');
                    $mediumTotal = $this->questionRepo->countByMaterialAndDifficulty($material->id, 'medium');
                    $hardTotal = $this->questionRepo->countByMaterialAndDifficulty($material->id, 'hard');

                    return [
                        'material' => $material,
                        'stats' => [
                            'overall' => [
                                'correct' => $counts['total'],
                                'total' => $counts['total'],
                                'percentage' => 100,
                            ],
                            'beginner' => [
                                'correct' => $counts['easy'],
                                'total' => $beginnerTotal,
                                'configured_total' => $counts['easy'],
                                'percentage' => 100,
                            ],
                            'medium' => [
                                'correct' => $counts['medium'],
                                'total' => $mediumTotal,
                                'configured_total' => $counts['medium'],
                                'percentage' => 100,
                            ],
                            'hard' => [
                                'correct' => $counts['hard'],
                                'total' => $hardTotal,
                                'configured_total' => $counts['hard'],
                                'percentage' => 100,
                            ],
                        ],
                    ];
                }
                )->values()->all();
            });
    }

    protected function processMaterialsWithStats($materials, $progressStats, $isGuest)
    {
        return $materials->map(function ($material) use ($progressStats, $isGuest) {
            $counts = ProgressHelper::calculateMaterialQuestionCounts($material, $isGuest);

            $beginnerTotal = $this->questionRepo->countByMaterialAndDifficulty($material->id, 'beginner');
            $mediumTotal = $this->questionRepo->countByMaterialAndDifficulty($material->id, 'medium');
            $hardTotal = $this->questionRepo->countByMaterialAndDifficulty($material->id, 'hard');

            // Stats
            $beginnerStats = $progressStats->where('material_id', $material->id)->where('difficulty', 'beginner')->first();
            $beginnerCorrect = $beginnerStats ? $beginnerStats->correct_answers : 0;
            $beginnerPercentage = ProgressHelper::calculateProgressPercentage($beginnerCorrect, $counts['easy']);

            $mediumStats = $progressStats->where('material_id', $material->id)->where('difficulty', 'medium')->first();
            $mediumCorrect = $mediumStats ? $mediumStats->correct_answers : 0;
            $mediumPercentage = ProgressHelper::calculateProgressPercentage($mediumCorrect, $counts['medium']);

            $hardStats = $progressStats->where('material_id', $material->id)->where('difficulty', 'hard')->first();
            $hardCorrect = $hardStats ? $hardStats->correct_answers : 0;
            $hardPercentage = ProgressHelper::calculateProgressPercentage($hardCorrect, $counts['hard']);

            $totalCorrect = $beginnerCorrect + $mediumCorrect + $hardCorrect;
            $overallPercentage = ProgressHelper::calculateProgressPercentage($totalCorrect, $counts['total']);

            return [
                'material' => $material,
                'stats' => [
                    'overall' => [
                        'correct' => $totalCorrect,
                        'total' => $counts['total'],
                        'percentage' => $overallPercentage,
                    ],
                    'beginner' => [
                        'correct' => $beginnerCorrect,
                        'total' => $beginnerTotal,
                        'configured_total' => $counts['easy'],
                        'percentage' => $beginnerPercentage,
                    ],
                    'medium' => [
                        'correct' => $mediumCorrect,
                        'total' => $mediumTotal,
                        'configured_total' => $counts['medium'],
                        'percentage' => $mediumPercentage,
                    ],
                    'hard' => [
                        'correct' => $hardCorrect,
                        'total' => $hardTotal,
                        'configured_total' => $counts['hard'],
                        'percentage' => $hardPercentage,
                    ],
                ],
            ];
        })->values()->all();
    }

    protected function determineActivityType($activity)
    {
        // Achievement: completing 5 or more questions in a material
        if ($activity->total_correct >= 5) {
            return 'achievement';
        }

        // Milestone: FIRST TIME completing a hard question in this material
        if ($activity->difficulty === 'hard' && $activity->is_correct && $activity->previous_hard_count === 0) {
            return 'milestone';
        }

        // Regular progress
        return 'progress';
    }
}
