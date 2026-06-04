<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\AdaptiveFactRepositoryInterface;
use App\Models\AdaptiveFact;
use Illuminate\Database\Eloquent\Collection;

final class AdaptiveFactRepository implements AdaptiveFactRepositoryInterface
{
    public function find(string $id): ?AdaptiveFact
    {
        return AdaptiveFact::find($id);
    }

    public function updateOrCreate(array $attributes, array $values): AdaptiveFact
    {
        return AdaptiveFact::updateOrCreate($attributes, $values);
    }

    public function count(): int
    {
        return AdaptiveFact::count();
    }

    public function getByCategory(string $category): Collection
    {
        return AdaptiveFact::where('category', $category)->get();
    }

    public function getAllForResources(): Collection
    {
        return AdaptiveFact::select(['id', 'name', 'category'])->get();
    }
}
