<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Models\AdaptiveFact;
use Illuminate\Database\Eloquent\Collection;

interface AdaptiveFactRepositoryInterface
{
    public function find(string $id): ?AdaptiveFact;

    public function updateOrCreate(array $attributes, array $values): AdaptiveFact;

    public function count(): int;

    public function getByCategory(string $category): Collection;

    public function getAllForResources(): Collection;
}
