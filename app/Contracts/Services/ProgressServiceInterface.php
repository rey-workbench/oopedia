<?php

namespace App\Contracts\Services;

use App\Models\QuizAttempt;
use Illuminate\Support\Collection as SupportCollection;

/**
 * Contract for tracking and saving student learning progress.
 */
interface ProgressServiceInterface
{
    /**
     * Get the number of attempts a user has made on a specific question.
     */
    public function getAttemptCount(int|string $userId, int $materialId, int $questionId): int;

    /**
     * Get all question IDs that a user has answered in a material.
     *
     * @return SupportCollection<int, int>
     */
    public function getAnsweredQuestionIds(int|string $userId, int $materialId): SupportCollection;

    /**
     * Persist a progress record.
     */
    public function saveProgress(array $data): QuizAttempt;
}
