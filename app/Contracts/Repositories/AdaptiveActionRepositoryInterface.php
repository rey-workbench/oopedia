<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Models\AdaptiveAction;
use Illuminate\Database\Eloquent\Collection;

interface AdaptiveActionRepositoryInterface
{
    public function find(string $id): ?AdaptiveAction;

    /** @param array<string> $ids */
    public function findByIds(array $ids): Collection;

    public function create(array $data): AdaptiveAction;

    public function update(string $id, array $data): ?AdaptiveAction;

    public function delete(string $id): bool;

    public function count(): int;

    public function allKeyedById(): Collection;

    public function getAllForResources(): Collection;
}
