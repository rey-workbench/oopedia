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

    public function find(string $id): ?QuizAttempt;

    /** @return Collection<int, QuizAttempt> */
    public function getByUser(string $userId): Collection;

    /** @return Collection<int, QuizAttempt> */
    public function getByUserAndQuestion(string $userId, string $questionId): Collection;

    /** @return Collection<int, QuizAttempt> */
    public function getByMaterial(string $materialId): Collection;

    public function getBestAttempt(string $userId, string $questionId): ?QuizAttempt;

    public function getLatestAttempt(string $userId, string $questionId): ?QuizAttempt;

    public function countAttempts(string $userId, string $questionId): int;

    /** @return Collection<int, QuizAttempt> */
    public function getCorrectAttempts(string $userId): Collection;

    /** @return array<string, mixed> */
    public function getUserStats(string $userId): array;
}
