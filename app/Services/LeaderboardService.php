<?php

namespace App\Services;

use App\Repositories\Interfaces\MaterialRepositoryInterface;
use App\Repositories\Interfaces\ProgressRepositoryInterface;

class LeaderboardService extends BaseService
{
    protected $materialRepo;
    protected $progressRepo;

    public function __construct(
        MaterialRepositoryInterface $materialRepo,
        ProgressRepositoryInterface $progressRepo
    ) {
        $this->materialRepo = $materialRepo;
        $this->progressRepo = $progressRepo;
    }

    public function getLeaderboardData($currentUserId)
    {
        // Get difficulty question counts from active configurations
        $difficultyCount = $this->calculateDifficultyTotals();

        // Get correct answers with attempts for scoring
        $correctAnswers = $this->progressRepo->getCorrectAnswersWithAttempts(3);

        // Calculate user scores
        $userScores = $this->calculateUserScores($correctAnswers);

        // Get leaderboard statistics
        $leaderboardData = $this->progressRepo->getLeaderboardStats(3);

        // Get materials for calculating total questions
        $materials = $this->materialRepo->getAllWithQuestionsAndConfigs();
        $totalConfiguredQuestions = $this->calculateTotalConfiguredQuestions($materials);

        // Process leaderboard data
        $leaderboardData = $this->processLeaderboardData(
            $leaderboardData,
            $userScores,
            $totalConfiguredQuestions,
            $difficultyCount
        );

        // Find current user rank
        $currentUserRank = $leaderboardData->firstWhere('id', $currentUserId);

        return [
            'leaderboardData' => $leaderboardData,
            'currentUserRank' => $currentUserRank
        ];
    }

    protected function calculateDifficultyTotals()
    {
        $totals = [
            'beginner' => 0,
            'medium' => 0,
            'hard' => 0
        ];

        $materials = $this->materialRepo->getAllWithQuestionsAndConfigs();

        foreach ($materials as $material) {
            $config = $material->questionBankConfigs->where('is_active', true)->first();

            if ($config) {
                $totals['beginner'] += $config->beginner_count;
                $totals['medium'] += $config->medium_count;
                $totals['hard'] += $config->hard_count;
            } else {
                $totals['beginner'] += $material->questions->where('difficulty', 'beginner')->count();
                $totals['medium'] += $material->questions->where('difficulty', 'medium')->count();
                $totals['hard'] += $material->questions->where('difficulty', 'hard')->count();
            }
        }

        return $totals;
    }

    protected function calculateUserScores($correctAnswers)
    {
        $userScores = [];

        foreach ($correctAnswers as $answer) {
            $userId = $answer->user_id;
            $attempts = (int)$answer->attempts_needed;

            if (!isset($userScores[$userId])) {
                $userScores[$userId] = 0;
            }

            // Base points by difficulty
            $basePoin = match ($answer->difficulty) {
                'beginner' => 5,
                'medium' => 10,
                'hard' => 15,
                default => 0  
            };

            // Attempt multiplier
            $attemptMultiplier = match ($attempts) {
                1 => 1.0,
                2 => 0.8,
                3 => 0.6,
                4 => 0.4,
                default => 0.2
            };

            $finalPoin = floor($basePoin * $attemptMultiplier);
            $userScores[$userId] += $finalPoin;
        }

        return $userScores;
    }

    protected function calculateTotalConfiguredQuestions($materials)
    {
        $total = 0;

        foreach ($materials as $material) {
            $config = $material->questionBankConfigs->where('is_active', true)->first();

            if ($config) {
                $total += $config->beginner_count + $config->medium_count + $config->hard_count;
            } else {
                $total += $material->questions->count();
            }
        }

        return $total;
    }

    protected function processLeaderboardData($leaderboardData, $userScores, $totalConfiguredQuestions, $difficultyCount)
    {
        // Add weighted scores
        foreach ($leaderboardData as $data) {
            $data->weighted_score = $userScores[$data->id] ?? 0;
        }

        // Sort by score descending
        $leaderboardData = $leaderboardData->sortByDesc('weighted_score')->values();

        // Add ranks, percentages, and badges
        $rank = 1;
        foreach ($leaderboardData as $data) {
            $data->rank = $rank++;

            // Calculate percentage
            $data->percentage = $totalConfiguredQuestions > 0
                ? min(100, round(($data->total_correct_questions / $totalConfiguredQuestions) * 100))
                : 0;

            $data->formatted_score = number_format($data->weighted_score, 0, ',', '.');

            // Determine badge
            $badge = $this->determineBadge($data, $difficultyCount);
            $data->badge = $badge['name'];
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
        } else {
            return ['name' => 'Learner', 'color' => 'secondary'];
        }
    }
}
