<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Models\SusResult;
use Illuminate\Database\Eloquent\Collection;

interface SusResultRepositoryInterface
{
    /**
     * @return Collection<int, SusResult>
     */
    public function getAllWithUser(?string $assessmentType = null): Collection;

    /**
     * @return array<string>
     */
    public function getDistinctAssessmentTypes(): array;

    public function findByUserId(string $userId): ?SusResult;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): SusResult;
}
