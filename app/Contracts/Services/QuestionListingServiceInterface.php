<?php

namespace App\Contracts\Services;

use App\Models\Material;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

/**
 * Contract for listing and preparing quiz question data for the student view.
 */
interface QuestionListingServiceInterface
{
    /**
     * Get all quiz data for a specific material and difficulty.
     *
     * @param array<string, mixed> $guestProgress
     * @return array<string, mixed>
     */
    public function getQuizData(Material $material, string $difficulty, int|string|null $userId, bool $isGuest, array $guestProgress = [], ?string $targetDifficulty = null): array;

    /**
     * Get all materials with student progress counts.
     *
     * @param array<string, mixed> $guestProgress
     * @return Collection<int, \App\Models\Material>
     */
    public function getMaterialsListWithStudentCount(int|string|null $userId, bool $isGuest, array $guestProgress = [], array $unlockedModules = []): Collection;

    /**
     * Get questions for the review/report view.
     *
     * @param array<string, mixed> $guestProgress
     * @return Collection<int, \App\Models\Question>
     */
    public function getReviewQuestions(Material $material, ?string $difficulty, int|string|null $userId, bool $isGuest, array $guestProgress = []): Collection;

    /**
     * Get answered question IDs from guest progress cookie data.
     *
     * @param array<string, mixed> $guestProgress
     * @return SupportCollection<int, int>
     */
    public function getGuestAnsweredQuestionIds(int $materialId, array $guestProgress = []): SupportCollection;

    /**
     * Get level-by-level progress for a material.
     *
     * @return array<string, mixed>
     */
    public function getLevelProgress(Material $material, string $difficulty, SupportCollection $answeredQuestionIds, bool $isGuest = false, ?Collection $preloadedQuestions = null): array;
}
