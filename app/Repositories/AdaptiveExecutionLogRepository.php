<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\AdaptiveExecutionLogRepositoryInterface;
use App\Models\AdaptiveExecutionLog;
use Illuminate\Database\Eloquent\Collection;

final class AdaptiveExecutionLogRepository implements AdaptiveExecutionLogRepositoryInterface
{
    public function count(): int
    {
        return AdaptiveExecutionLog::count();
    }

    public function getRecent(int $limit = 10): Collection
    {
        return AdaptiveExecutionLog::with(['user'])
            ->latest()
            ->limit($limit)
            ->get();
    }
}
