<?php

namespace App\Contracts\Repositories;

use App\Models\StudentState;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

/**
 * Contract for student progress data access.
 */
interface ProgressRepositoryInterface
{
    /** @return \Illuminate\Database\Eloquent\Collection<int, mixed> */
    public function getUserProgressStats(string|null $userId): \Illuminate\Database\Eloquent\Collection;

    /** @return \Illuminate\Database\Eloquent\Collection<int, mixed> */
    public function getUserMaterialProgress(string|null $userId): \Illuminate\Database\Eloquent\Collection;

    /** @return SupportCollection<int, mixed> */
    public function getRecentActivities(string|null $userId, int $limit = 5): SupportCollection;

    /** @return array<string, mixed> */
    public function getDetailedUserProgress(string|null $userId): array;

    /** @return Collection<int, mixed> */
    public function getCorrectAnswersWithAttempts(string $roleId): Collection;

    /** @return \Illuminate\Database\Eloquent\Collection<int, mixed> */
    public function getLeaderboardStats(string $roleId): \Illuminate\Database\Eloquent\Collection;

    public function getAttemptCount(string $userId, string $materialId, string $questionId): int;

    public function saveProgress(array $data): \App\Models\QuizAttempt;

    public function updateStudentState(string|null $userId, array $attributes): void;

    public function getStudentState(string|null $userId): ?StudentState;

    public function getOrCreateStudentState(string|null $userId): StudentState;

    /** @return array<string, mixed> */
    public function getUserMaterialProgressWithState(string|null $userId, string $materialId): array;

    /** @return SupportCollection<int, string> */
    public function getAnsweredQuestionIds(string $userId, string $materialId): SupportCollection;

    public function getConsecutiveFailures(string|null $userId, string $questionId): int;

    public function getLatestErrorType(string|null $userId, string $questionId): ?string;

    public function resetProgress(string $userId, string $materialId): void;

    public function updateOrCreateProgress(array $conditions, array $values): void;

    /** @return \Illuminate\Database\Eloquent\Collection<int, mixed> */
    public function getStudentCountByMaterial(): \Illuminate\Database\Eloquent\Collection;

    /** @return \Illuminate\Database\Eloquent\Collection<int, mixed> */
    public function getRecentSystemProgress(int $limit): \Illuminate\Database\Eloquent\Collection;

    /** @return \Illuminate\Database\Eloquent\Collection<int, mixed> */
    public function getMaterialPerformanceStats(): \Illuminate\Database\Eloquent\Collection;

    /** @return \Illuminate\Database\Eloquent\Collection<int, mixed> */
    public function getPopularMaterials(int $limit): \Illuminate\Database\Eloquent\Collection;

    /** @return \Illuminate\Database\Eloquent\Collection<int, mixed> */
    public function getByUserAndMaterial(string $userId, string $materialId): \Illuminate\Database\Eloquent\Collection;

    /** @return \Illuminate\Database\Eloquent\Collection<int, mixed> */
    public function getWrongAnswers(string $userId, string $materialId): \Illuminate\Database\Eloquent\Collection;

    public function getLastAccessTime(string|null $userId, string $materialId): ?string;

    /**
     * Get the latest QuizAttempt for each of the given question IDs by a user.
     *
     * @param  array<int, string>  $questionIds
     * @return \Illuminate\Support\Collection<string, \App\Models\QuizAttempt> keyed by question_id
     */
    public function getLatestAttemptsForQuestions(string $userId, array $questionIds): \Illuminate\Support\Collection;
}
