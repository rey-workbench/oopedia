<?php

namespace App\Contracts\Repositories;

use App\Models\QuizAttempt;
use App\Models\StudentState;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

/**
 * Contract for student progress data access.
 */
interface ProgressRepositoryInterface
{
    /** @return \Illuminate\Database\Eloquent\Collection<int, mixed> */
    public function getUserProgressStats(int|string|null $userId): \Illuminate\Database\Eloquent\Collection;

    /** @return \Illuminate\Database\Eloquent\Collection<int, mixed> */
    public function getUserMaterialProgress(int|string|null $userId): \Illuminate\Database\Eloquent\Collection;

    /** @return SupportCollection<int, mixed> */
    public function getRecentActivities(int|string|null $userId, int $limit = 5): SupportCollection;

    /** @return array<string, mixed> */
    public function getDetailedUserProgress(int|string|null $userId): array;

    /** @return Collection<int, mixed> */
    public function getCorrectAnswersWithAttempts(int $roleId = 3): Collection;

    /** @return \Illuminate\Database\Eloquent\Collection<int, mixed> */
    public function getLeaderboardStats(int $roleId = 3): \Illuminate\Database\Eloquent\Collection;

    public function getAttemptCount(int|string $userId, int $materialId, int $questionId): int;

    public function saveProgress(array $data): \App\Models\QuizAttempt;

    public function updateStudentState(int|string|null $userId, array $attributes): void;

    public function getStudentState(int|string|null $userId): ?StudentState;

    public function getOrCreateStudentState(int|string|null $userId): StudentState;

    /** @return array<string, mixed> */
    public function getUserMaterialProgressWithState(int|string|null $userId, int $materialId): array;

    /** @return SupportCollection<int, int> */
    public function getAnsweredQuestionIds(int|string $userId, int $materialId): SupportCollection;

    public function getConsecutiveFailures(int|string|null $userId, int $questionId): int;

    public function getLatestErrorType(int|string|null $userId, int $questionId): ?string;

    public function resetProgress(int|string $userId, int $materialId): void;

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
    public function getByUserAndMaterial(int|string $userId, int $materialId): \Illuminate\Database\Eloquent\Collection;

    /** @return \Illuminate\Database\Eloquent\Collection<int, mixed> */
    public function getWrongAnswers(int|string $userId, int $materialId): \Illuminate\Database\Eloquent\Collection;

    public function getLastAccessTime(int|string|null $userId, int $materialId): ?string;
}
