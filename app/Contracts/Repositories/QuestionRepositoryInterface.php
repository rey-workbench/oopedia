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

    public function find(string $id): ?Question;

    public function create(array $data): Question;

    public function update(string $id, array $data): ?Question;

    public function delete(string $id): bool;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function countAll(): int;

    public function findWithAnswers(string $id): Question;

    /** @return Collection<int, Question> */
    public function getByMaterialAndDifficulty(
        string $materialId,
        ?string $difficulty = null,
        ?string $subMaterialId = null,
    ): Collection;

    public function getFilteredQuestions(
        ?string $search = null,
        ?string $difficulty = null,
        ?string $materialId = null,
    ): LengthAwarePaginator;

    public function getQuestionsForBank(
        string $materialId,
        array $excludeIds,
        ?string $search = null,
        ?string $difficulty = null,
    ): LengthAwarePaginator;

    public function countByMaterialAndDifficulty(string $materialId, string $difficulty): int;

    public function existsByMaterialAndDifficulty(string $materialId, string $difficulty): bool;

    public function countByMaterial(string $materialId): int;
}
