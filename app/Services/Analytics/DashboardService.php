<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Contracts\Services\DashboardServiceInterface;
use App\Helpers\ProgressHelper;
use App\Models\Material;
use App\Models\StudentState;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

final class DashboardService implements DashboardServiceInterface
{
    public function __construct(
        public readonly MaterialRepositoryInterface $materialRepo,
        public readonly ProgressRepositoryInterface $progressRepo,
        public readonly QuestionRepositoryInterface $questionRepo,
    ) {}

    /** @return Collection<int, Material> */
    public function getAllMaterials(): Collection
    {
        return $this->materialRepo->getAllWithQuestions();
    }

    /** @return array<string, mixed> */
    public function getDashboardIndexData(string $userId, bool $isGuest): array
    {
        $cacheKey = "dashboard_index_{$userId}_" . ($isGuest ? '1' : '0');

        return Cache::remember(
            $cacheKey,
            300,
            function () use ($userId, $isGuest) {
                $allMaterials   = $this->materialRepo->getAllWithQuestions();
                $totalMaterials = $allMaterials->count();
                $progressStats  = $this->progressRepo->getUserProgressStats($userId);

                $configuredCounts = $this->calculateConfiguredCounts($allMaterials, $isGuest);

                $materials = $allMaterials->map(function ($material) use ($progressStats, $isGuest) {
                    $totalQuestions     = ProgressHelper::calculateMaterialQuestionCounts($material, $isGuest)['total'];
                    $materialProgress   = $progressStats->firstWhere('material_id', $material->id);
                    $correctAnswers     = $materialProgress ? $materialProgress->correct_answers : 0;
                    $progressPercentage = ProgressHelper::calculateProgressPercentage(
                        $correctAnswers,
                        $totalQuestions,
                    );

                    return (object) [
                        'id'                  => $material->id,
                        'title'               => $material->title,
                        'description'         => $material->description ?? '',
                        'progress_percentage' => $progressPercentage,
                        'total_questions'     => $totalQuestions,
                        'completed_questions' => $correctAnswers,
                    ];
                });

                $recentActivities = $this->progressRepo->getRecentActivities($userId, 10)
                    ->map(fn ($activity) => $this->addActivityType($activity))
                    ->pipe(fn ($activities) => $this->deduplicateActivities($activities, 5));

                $studentState   = StudentState::find($userId);
                $certifications = $studentState?->learning_profile['certifications'] ?? [];

                return [
                    'totalMaterials'             => $totalMaterials,
                    'totalQuestions'             => $configuredCounts['total'],
                    'easyQuestions'              => $configuredCounts['easy'],
                    'mediumQuestions'            => $configuredCounts['medium'],
                    'hardQuestions'              => $configuredCounts['hard'],
                    'materialProgressPercentage' => 0,
                    'questionProgressPercentage' => ProgressHelper::calculateProgressPercentage(
                        $progressStats->sum('correct_answers'),
                        $configuredCounts['total'],
                    ),
                    'completedMaterials'     => 0,
                    'inProgressMaterials'    => 0,
                    'totalMaterialProgress'  => 0,
                    'totalAnsweredQuestions' => 0,
                    'totalCorrectQuestions'  => $progressStats->sum('correct_answers'),
                    'recentActivities'       => $recentActivities,
                    'allMaterials'           => $materials,
                    'certifications'         => $certifications,
                ];
            },
        );
    }

    /** @return array<string, int> */
    protected function calculateConfiguredCounts(Collection $materials, bool $isGuest): array
    {
        $counts = [
            'easy'   => 0,
            'medium' => 0,
            'hard'   => 0,
            'total'  => 0,
        ];

        foreach ($materials as $material) {
            $materialCounts = ProgressHelper::calculateMaterialQuestionCounts($material, $isGuest);
            $counts['easy']   += $materialCounts['easy'];
            $counts['medium'] += $materialCounts['medium'];
            $counts['hard']   += $materialCounts['hard'];
            $counts['total']  += $materialCounts['total'];
        }

        return $counts;
    }

    protected function addActivityType($activity): object
    {
        if ($activity->total_correct >= 5) {
            $activity->type = 'achievement';
        } elseif ($activity->difficulty === 'hard' && $activity->is_correct && $activity->previous_hard_count === 0) {
            $activity->type = 'milestone';
        } else {
            $activity->type = 'progress';
        }

        return $activity;
    }

    protected function deduplicateActivities($activities, int $limit): \Illuminate\Support\Collection
    {
        $seen = [];

        return $activities->filter(function ($activity) use (&$seen) {
            $key = $activity->material_id . '_' . $activity->type;
            if (in_array($key, $seen)) {
                return false;
            }
            $seen[] = $key;

            return true;
        })->take($limit)->values();
    }

    /** @return array<int, array<string, mixed>> */
    public function getInProgressData(string $userId, bool $isGuest): array
    {
        $cacheKey = "dashboard_inprogress_{$userId}_" . ($isGuest ? '1' : '0');

        return Cache::remember(
            $cacheKey,
            300,
            function () use ($userId, $isGuest) {
                $progressStats    = $this->progressRepo->getDetailedUserProgress($userId);
                $materialProgress = $this->progressRepo->getUserMaterialProgress($userId);

                $materials = $this->materialRepo->getAllWithQuestions()->filter(
                    function ($material) use ($materialProgress) {
                        $progress       = $materialProgress->firstWhere('material_id', $material->id);
                        $totalQuestions = $material->questions->count();

                        if (! $progress || $totalQuestions <= 0) {
                            return false;
                        }

                        $correctAnswers = $progress->correct_answers;

                        return $correctAnswers > 0 && $correctAnswers < $totalQuestions;
                    },
                );

                return $this->processMaterialsWithStats($materials, $progressStats, $isGuest);
            },
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function getCompletedData(string $userId, bool $isGuest): array
    {
        $cacheKey = "dashboard_completed_{$userId}_" . ($isGuest ? '1' : '0');

        return Cache::remember(
            $cacheKey,
            300,
            function () use ($userId, $isGuest) {
                $progressStats    = $this->progressRepo->getDetailedUserProgress($userId);
                $materialProgress = $this->progressRepo->getUserMaterialProgress($userId);

                $materials = $this->materialRepo->getAllWithQuestions()->filter(
                    function ($material) use ($materialProgress, $isGuest) {
                        $progress = $materialProgress->firstWhere('material_id', $material->id);

                        if (! $progress) {
                            return false;
                        }

                        $correctAnswers = $progress->correct_answers;
                        $counts         = ProgressHelper::calculateMaterialQuestionCounts(
                            $material,
                            $isGuest,
                        );
                        $configuredTotalQuestions = $counts['total'];

                        return $correctAnswers >= $configuredTotalQuestions;
                    },
                );

                return $materials->map(
                    function ($material) use ($isGuest) {
                        $counts        = ProgressHelper::calculateMaterialQuestionCounts($material, $isGuest);
                        $beginnerTotal = $this->questionRepo->countByMaterialAndDifficulty($material->id, 'beginner');
                        $mediumTotal   = $this->questionRepo->countByMaterialAndDifficulty($material->id, 'medium');
                        $hardTotal     = $this->questionRepo->countByMaterialAndDifficulty($material->id, 'hard');

                        return [
                            'material' => $material,
                            'stats'    => [
                                'overall' => [
                                    'correct'    => $counts['total'],
                                    'total'      => $counts['total'],
                                    'percentage' => 100,
                                ],
                                'beginner' => [
                                    'correct'          => $counts['easy'],
                                    'total'            => $beginnerTotal,
                                    'configured_total' => $counts['easy'],
                                    'percentage'       => 100,
                                ],
                                'medium' => [
                                    'correct'          => $counts['medium'],
                                    'total'            => $mediumTotal,
                                    'configured_total' => $counts['medium'],
                                    'percentage'       => 100,
                                ],
                                'hard' => [
                                    'correct'          => $counts['hard'],
                                    'total'            => $hardTotal,
                                    'configured_total' => $counts['hard'],
                                    'percentage'       => 100,
                                ],
                            ],
                        ];
                    },
                )->values()->all();
            },
        );
    }

    protected function processMaterialsWithStats($materials, $progressStats, $isGuest)
    {
        return $materials->map(function ($material) use ($progressStats, $isGuest) {
            $counts = ProgressHelper::calculateMaterialQuestionCounts($material, $isGuest);

            $beginnerTotal = $this->questionRepo->countByMaterialAndDifficulty($material->id, 'beginner');
            $mediumTotal   = $this->questionRepo->countByMaterialAndDifficulty($material->id, 'medium');
            $hardTotal     = $this->questionRepo->countByMaterialAndDifficulty($material->id, 'hard');

            $beginnerStats = $progressStats->where('material_id', $material->id)
                ->where('difficulty', 'beginner')
                ->first();
            $beginnerCorrect    = $beginnerStats ? $beginnerStats->correct_answers : 0;
            $beginnerPercentage = ProgressHelper::calculateProgressPercentage($beginnerCorrect, $counts['easy']);

            $mediumStats = $progressStats->where('material_id', $material->id)
                ->where('difficulty', 'medium')
                ->first();
            $mediumCorrect    = $mediumStats ? $mediumStats->correct_answers : 0;
            $mediumPercentage = ProgressHelper::calculateProgressPercentage($mediumCorrect, $counts['medium']);

            $hardStats = $progressStats->where('material_id', $material->id)
                ->where('difficulty', 'hard')
                ->first();
            $hardCorrect    = $hardStats ? $hardStats->correct_answers : 0;
            $hardPercentage = ProgressHelper::calculateProgressPercentage($hardCorrect, $counts['hard']);

            $totalCorrect      = $beginnerCorrect + $mediumCorrect + $hardCorrect;
            $overallPercentage = ProgressHelper::calculateProgressPercentage($totalCorrect, $counts['total']);

            return [
                'material' => $material,
                'stats'    => [
                    'overall' => [
                        'correct'    => $totalCorrect,
                        'total'      => $counts['total'],
                        'percentage' => $overallPercentage,
                    ],
                    'beginner' => [
                        'correct'          => $beginnerCorrect,
                        'total'            => $beginnerTotal,
                        'configured_total' => $counts['easy'],
                        'percentage'       => $beginnerPercentage,
                    ],
                    'medium' => [
                        'correct'          => $mediumCorrect,
                        'total'            => $mediumTotal,
                        'configured_total' => $counts['medium'],
                        'percentage'       => $mediumPercentage,
                    ],
                    'hard' => [
                        'correct'          => $hardCorrect,
                        'total'            => $hardTotal,
                        'configured_total' => $counts['hard'],
                        'percentage'       => $hardPercentage,
                    ],
                ],
            ];
        })->values()->all();
    }
}
