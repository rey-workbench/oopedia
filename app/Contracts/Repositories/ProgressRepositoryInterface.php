<?php

namespace App\Contracts\Repositories;

use App\Models\QuizAttempt;
use App\Models\StudentState;
use Collection;
use Illuminate\Support\SupportCollection;

/**
 * Contract for student progress data access.
 */
interface ProgressRepositoryInterface
{
    /** @return Collection<int, mixed> */
    public function getUserProgressStats(?string $userId): Collection;

    /** @return Collection<int, mixed> */
    public function getUserMaterialProgress(?string $userId): Collection;

    /** @return SupportCollection<int, mixed> */
    public function getRecentActivities(?string $userId, int $limit = 5): SupportCollection;

    /** @return array<string, mixed> */
    public function getDetailedUserProgress(?string $userId): array;

    /** @return Collection<int, mixed> */
    public function getCorrectAnswersWithAttempts(string $roleId): Collection;

    /** @return Collection<int, mixed> */
    public function getLeaderboardStats(string $roleId): Collection;

    public function getAttemptCount(string $userId, string $materialId, string $questionId): int;

    public function saveProgress(array $data): QuizAttempt;

    public function updateStudentState(?string $userId, array $attributes): void;

    public function getStudentState(?string $userId): ?StudentState;

    public function getOrCreateStudentState(?string $userId): StudentState;

    /** @return array<string, mixed> */
    public function getUserMaterialProgressWithState(?string $userId, string $materialId): array;

    /** @return SupportCollection<int, string> */
    public function getAnsweredQuestionIds(string $userId, string $materialId): SupportCollection;

    public function getConsecutiveFailures(?string $userId, string $questionId): int;

    public function getLatestErrorType(?string $userId, string $questionId): ?string;

    public function resetProgress(string $userId, string $materialId): void;

    public function updateOrCreateProgress(array $conditions, array $values): void;

    /** @return Collection<int, mixed> */
    public function getStudentCountByMaterial(): Collection;

    /** @return Collection<int, mixed> */
    public function getRecentSystemProgress(int $limit): Collection;

    /** @return Collection<int, mixed> */
    public function getMaterialPerformanceStats(): Collection;

    /** @return Collection<int, mixed> */
    public function getPopularMaterials(int $limit): Collection;

    /** @return Collection<int, mixed> */
    public function getByUserAndMaterial(string $userId, string $materialId): Collection;

    /** @return Collection<int, mixed> */
    public function getWrongAnswers(string $userId, string $materialId): Collection;

    public function getLastAccessTime(?string $userId, string $materialId): ?string;

    /**
     * Get the latest QuizAttempt for each of the given question IDs by a user.
     *
     * @param array<int, string> $questionIds
     * @return SupportCollection<string, QuizAttempt> keyed by question_id
     */
    public function getLatestAttemptsForQuestions(string $userId, array $questionIds): SupportCollection;
}
