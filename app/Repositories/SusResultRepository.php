<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\SusResultRepositoryInterface;
use App\Models\SusResult;
use Illuminate\Database\Eloquent\Collection;

final class SusResultRepository implements SusResultRepositoryInterface
{
    public function create(array $data): SusResult
    {
        return SusResult::create($data);
    }

    /** @return Collection<int, SusResult> */
    public function getAllWithUser(?string $class = null): Collection
    {
        $query = SusResult::with('user');

        if ($class) {
            $query->where('class', '=', $class);
        }

        return $query->get();
    }

    /** @return array<string> */
    public function getDistinctClasses(): array
    {
        return SusResult::distinct()->pluck('class')->filter()->values()->all();
    }

    public function findByUserId(string $userId): ?SusResult
    {
        return SusResult::where('user_id', '=', $userId)->with('user')->first();
    }
}
