<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Models\MslqResult;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface MslqRepositoryInterface
{
    public function getAll(?string $assessmentType = null, int $perPage = 10): LengthAwarePaginator;

    public function getAllForCalculation(?string $assessmentType = null): Collection;

    public function getDistinctAssessmentTypes(): Collection;

    public function findWithRelations(string $id): MslqResult;

    public function create(array $data): MslqResult;
}
