<?php

declare(strict_types=1);

namespace App\Services\Adaptive\Handlers\Facts;

interface VirtualFactProcessorInterface
{
    /**
     * @param array $facts Current primary facts (G-codes)
     * @param array $state Current student state
     * @return string[]|string|null Deducted virtual fact(s)
     */
    public function process(array $facts, array $state): array|string|null;
}
