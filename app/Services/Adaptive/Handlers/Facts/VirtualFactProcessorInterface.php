<?php

declare(strict_types=1);

namespace App\Services\Adaptive\Handlers\Facts;

interface VirtualFactProcessorInterface
{
    /**
     * @param array $facts Current primary facts (G-codes)
     * @return string[]|string|null Deducted virtual fact(s)
     */
    public function process(array $facts): array|string|null;
}
