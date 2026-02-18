<?php

namespace App\Contracts\Repositories;

use App\Models\Question;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Contract for Question data access.
 */
interface QuestionRepositoryInterface
{
    /** @return Collection<int, Question> */
    public function all(): Collection;

    public function find(int $id): ?Question;

    public function create(array $data): Question;

    public function update(int $id, array $data): ?Question;

    public function delete(int $id): bool;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function countAll(): int;

    public function findWithAnswers(int $id): Question;

    /** @return Collection<int, Question> */
    public function getByMaterialAndDifficulty(int $materialId, ?string $difficulty = null): Collection;

    public function getFilteredQuestions(?string $search = null, ?string $difficulty = null, ?int $materialId = null): LengthAwarePaginator;

    public function getQuestionsForBank(int $materialId, array $excludeIds, ?string $search = null, ?string $difficulty = null): LengthAwarePaginator;

    public function countByMaterialAndDifficulty(int $materialId, string $difficulty): int;

    public function existsByMaterialAndDifficulty(int $materialId, string $difficulty): bool;

    public function countByMaterial(int $materialId): int;
}
