<?php

namespace App\Contracts\Services;

use App\Models\Material;
use App\Models\Question;

interface NextActionResolverServiceInterface
{
    /** @return array<string, mixed> */
    public function resolve(string $actionCommand, Material $material, Question $question, ?int $userId = null): array;
}
