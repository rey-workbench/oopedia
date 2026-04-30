<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Models\StudentState;

interface StudentStateRepositoryInterface
{
    public function findOrCreate(string $userId): StudentState;

    public function update(string $userId, array $data): StudentState;
}
