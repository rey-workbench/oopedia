<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Models\MslqAnswer;
use App\Models\MslqQuestion;
use App\Models\MslqResult;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface MslqRepositoryInterface
{
    public function getAll(?string $assessmentType = null, int $perPage = 10): LengthAwarePaginator;

    public function getAllForCalculation(?string $assessmentType = null): Collection;

    public function findWithRelations(string $id): MslqResult;

    public function create(array $data): MslqResult;

    public function findExistingResult(string $userId, string $assessmentType): ?MslqResult;

    /** @return Collection<int, MslqQuestion> */
    public function getOrderedQuestions(): Collection;

    public function createAnswer(array $data): MslqAnswer;
}
