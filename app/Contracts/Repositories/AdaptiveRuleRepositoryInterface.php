<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Models\AdaptiveRule;
use Illuminate\Database\Eloquent\Collection;

interface AdaptiveRuleRepositoryInterface
{
    public function all(): Collection;

    public function find(string $id): ?AdaptiveRule;

    public function count(): int;

    public function getOrdered(): Collection;

    /** @return Collection<int, AdaptiveRule> */
    public function getWithExecutionStats(): Collection;
}
