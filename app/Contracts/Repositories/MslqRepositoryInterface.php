<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Models\MslqResult;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface MslqRepositoryInterface
{
    /**
     * Get all results with filtering and pagination.
     */
    public function getAll(?string $class = null, int $perPage = 10): LengthAwarePaginator;

    /**
     * Get all results for calculation (no pagination).
     */
    public function getAllForCalculation(?string $class = null): Collection;

    /**
     * Get distinct classes that have submitted results.
     */
    public function getDistinctClasses(): Collection;

    /**
     * Find a result by ID with relations.
     */
    public function findWithRelations(string $id): MslqResult;

    /**
     * Save a new result.
     */
    public function create(array $data): MslqResult;

    /**
     * Check if user has already submitted.
     */
    public function hasUserSubmitted(int|string $userId): bool;
}
