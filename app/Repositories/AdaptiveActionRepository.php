<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\AdaptiveActionRepositoryInterface;
use App\Models\AdaptiveAction;
use Illuminate\Database\Eloquent\Collection;

final class AdaptiveActionRepository implements AdaptiveActionRepositoryInterface
{
    public function find(string $id): ?AdaptiveAction
    {
        return AdaptiveAction::find($id);
    }

    public function findByIds(array $ids): Collection
    {
        return AdaptiveAction::whereIn('id', $ids)->get()->keyBy('id');
    }

    public function create(array $data): AdaptiveAction
    {
        return AdaptiveAction::create($data);
    }

    public function update(string $id, array $data): ?AdaptiveAction
    {
        $action = AdaptiveAction::find($id);

        if (! $action) {
            return null;
        }

        $action->update($data);

        return $action->fresh();
    }

    public function delete(string $id): bool
    {
        $action = AdaptiveAction::find($id);

        if (! $action) {
            return false;
        }

        return (bool) $action->delete();
    }

    public function count(): int
    {
        return AdaptiveAction::count();
    }

    public function allKeyedById(): Collection
    {
        return AdaptiveAction::all()->keyBy('id');
    }

    public function getAllForResources(): Collection
    {
        return AdaptiveAction::select(['id', 'name', 'description', 'variant'])->get();
    }
}
