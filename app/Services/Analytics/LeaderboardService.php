<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Services\LeaderboardServiceInterface;
use App\Helpers\ProgressHelper;
use Illuminate\Support\Facades\Cache;

final class LeaderboardService implements LeaderboardServiceInterface
{
    public function __construct(
        public readonly MaterialRepositoryInterface $materialRepo,
        public readonly ProgressRepositoryInterface $progressRepo,
    ) {
    }

    /** @return array<string, mixed> */
    public function getLeaderboardData(string $currentUserId): array
    {
        $leaderboardData = Cache::remember('global_leaderboard_data', 600, function () {
            $materials       = $this->materialRepo->getAllWithQuestionsAndConfigs();
            $difficultyCount = ProgressHelper::calculateDifficultyTotals($materials);

            $correctAnswers = $this->progressRepo->getCorrectAnswersWithAttempts('mahasiswa');

            $userScores = $this->calculateUserScores($correctAnswers);

            $leaderboardDataRaw = $this->progressRepo->getLeaderboardStats('mahasiswa');

            $totalConfiguredQuestions = ProgressHelper::calculateTotalQuestions($materials);

            return $this->processLeaderboardData(
                $leaderboardDataRaw,
                $userScores,
                $totalConfiguredQuestions,
                $difficultyCount,
            );
        });

        $currentUserRank = $leaderboardData->firstWhere('id', $currentUserId);

        return [
            'leaderboardData' => $leaderboardData,
            'currentUserRank' => $currentUserRank,
        ];
    }

    protected function calculateUserScores($correctAnswers)
    {
        $userScores = [];

        foreach ($correctAnswers as $answer) {
            $userId   = $answer->user_id;
            $attempts = (int) $answer->attempts_needed;

            if (! isset($userScores[$userId])) {
                $userScores[$userId] = 0;
            }

            $basePoin = match ($answer->difficulty) {
                'beginner' => 5,
                'medium'   => 10,
                'hard'     => 15,
                default    => 0
            };

            $attemptMultiplier = match ($attempts) {
                1       => 1.0,
                2       => 0.8,
                3       => 0.6,
                4       => 0.4,
                default => 0.2
            };

            $finalPoin = floor($basePoin * $attemptMultiplier);
            $userScores[$userId] += $finalPoin;
        }

        return $userScores;
    }

    protected function processLeaderboardData(
        $leaderboardData,
        $userScores,
        $totalConfiguredQuestions,
        $difficultyCount,
    ) {
        foreach ($leaderboardData as $data) {
            $data->weighted_score = $userScores[$data->id] ?? 0;
        }

        $leaderboardData = $leaderboardData->sortByDesc('weighted_score')->values();

        $rank = 1;
        foreach ($leaderboardData as $data) {
            $data->rank = $rank++;

            $data->percentage = ProgressHelper::calculateProgressPercentage(
                $data->total_correct_questions,
                $totalConfiguredQuestions,
            );

            $data->formatted_score = number_format($data->weighted_score, 0, ',', '.');

            $badge             = $this->determineBadge($data, $difficultyCount);
            $data->badge       = $badge['name'];
            $data->badge_color = $badge['color'];
        }

        return $leaderboardData;
    }

    protected function determineBadge($data, $difficultyCount)
    {
        if ($data->hard_completed >= $difficultyCount['hard'] && $difficultyCount['hard'] > 0) {
            return ['name' => 'Hard', 'color' => 'danger'];
        } elseif ($data->medium_completed >= $difficultyCount['medium'] && $difficultyCount['medium'] > 0) {
            return ['name' => 'Medium', 'color' => 'warning'];
        } elseif ($data->beginner_completed >= $difficultyCount['beginner'] && $difficultyCount['beginner'] > 0) {
            return ['name' => 'Beginner', 'color' => 'success'];
        }

        return ['name' => 'Learner', 'color' => 'secondary'];
    }
}
