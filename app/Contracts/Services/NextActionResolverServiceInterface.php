<?php

namespace App\Contracts\Services;

use App\Models\Material;
use App\Models\Question;

interface NextActionResolverServiceInterface
{
    public function resolve(
        string $actionCommand,
        Material $material,
        Question $question,
        ?string $userId = null,
    ): array;
}
