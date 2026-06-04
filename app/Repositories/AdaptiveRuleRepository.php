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

    public function find(string $id): ?AdaptiveRule
    {
        return AdaptiveRule::find($id);
    }

    public function findByIds(array $ids): Collection
    {
        return AdaptiveRule::whereIn('id', $ids)->get()->keyBy('id');
    }

    public function create(array $data): AdaptiveRule
    {
        return AdaptiveRule::create($data);
    }

    public function delete(string $id): bool
    {
        $rule = AdaptiveRule::find($id);

        if (! $rule) {
            return false;
        }

        return (bool) $rule->delete();
    }

    public function getActiveOrdered(): Collection
    {
        return AdaptiveRule::where('is_active', true)->orderBy('priority')->get();
    }

    public function findNameById(string $id): ?string
    {
        return AdaptiveRule::where('id', $id)->value('name');
    }
}
