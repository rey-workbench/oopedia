<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Enums\Lms\QuestionDifficulty;
use App\Models\Question;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface QuestionRepositoryInterface
{
    public function find(string $id): ?Question;

    public function create(array $data): Question;

    public function delete(string $id): bool;

    public function countAll(): int;

    public function findWithAnswers(string $id): Question;

    public function getByMaterialAndDifficulty(
        string $materialId,
        ?string $difficulty = null,
    ): Collection;

    public function getFilteredQuestions(
        ?string $search = null,
        ?string $difficulty = null,
        ?string $materialId = null,
    ): LengthAwarePaginator;

    public function countByMaterialAndDifficulty(string $materialId, QuestionDifficulty $questionDifficulty): int;

    public function getRandomMultipleChoiceFromOtherMaterials(string $excludeMaterialId): ?Question;
}
