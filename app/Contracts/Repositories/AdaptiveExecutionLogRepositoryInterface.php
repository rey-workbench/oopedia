<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface AdaptiveExecutionLogRepositoryInterface
{
    public function count(): int;

    public function getRecent(int $limit = 10): Collection;
}
