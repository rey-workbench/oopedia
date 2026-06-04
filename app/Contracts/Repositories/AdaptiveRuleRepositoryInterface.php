<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Models\AdaptiveRule;
use Illuminate\Database\Eloquent\Collection;

interface AdaptiveRuleRepositoryInterface
{
    public function all(): Collection;

    public function count(): int;

    public function getOrdered(): Collection;

    /** @return Collection<int, AdaptiveRule> */
    public function getWithExecutionStats(): Collection;

    public function find(string $id): ?AdaptiveRule;

    /** @param array<string> $ids */
    public function findByIds(array $ids): Collection;

    public function create(array $data): AdaptiveRule;

    public function delete(string $id): bool;

    public function getActiveOrdered(): Collection;

    public function findNameById(string $id): ?string;
}
