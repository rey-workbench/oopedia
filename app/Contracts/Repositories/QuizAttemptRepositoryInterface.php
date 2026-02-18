<?php

namespace App\Contracts\Repositories;

use App\Models\QuizAttempt;
use Illuminate\Database\Eloquent\Collection;

/**
 * Contract for QuizAttempt data access.
 */
interface QuizAttemptRepositoryInterface
{
    public function create(array $data): QuizAttempt;

    public function find(int $id): ?QuizAttempt;

    /** @return Collection<int, QuizAttempt> */
    public function getByUser(int $userId): Collection;

    /** @return Collection<int, QuizAttempt> */
    public function getByUserAndQuestion(int $userId, int $questionId): Collection;

    /** @return Collection<int, QuizAttempt> */
    public function getByMaterial(int $materialId): Collection;

    public function getBestAttempt(int $userId, int $questionId): ?QuizAttempt;

    public function getLatestAttempt(int $userId, int $questionId): ?QuizAttempt;

    public function countAttempts(int $userId, int $questionId): int;

    /** @return Collection<int, QuizAttempt> */
    public function getCorrectAttempts(int $userId): Collection;

    /** @return array<string, mixed> */
    public function getUserStats(int $userId): array;
}
