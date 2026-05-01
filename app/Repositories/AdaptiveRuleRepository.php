<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\AdaptiveRuleRepositoryInterface;
use App\Models\AdaptiveRule;
use Illuminate\Database\Eloquent\Collection;

final class AdaptiveRuleRepository implements AdaptiveRuleRepositoryInterface
{
    public function all(): Collection
    {
        return AdaptiveRule::select(['id', 'name', 'priority', 'is_active'])->get();
    }

    public function find(string $id): ?AdaptiveRule
    {
        return AdaptiveRule::find($id);
    }

    public function count(): int
    {
        return AdaptiveRule::count();
    }

    public function getOrdered(): Collection
    {
        return AdaptiveRule::orderBy('priority')->get();
    }

    public function getWithExecutionStats(): Collection
    {
        return AdaptiveRule::withCount('executionLogs')
            ->orderByDesc('execution_logs_count')
            ->get();
    }
}
