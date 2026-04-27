<?php

declare(strict_types=1);

namespace App\Services\Adaptive\Handlers\Actions;

interface ActionProcessorInterface
{
    /**
     * Process a specific part of the action instructions.
     *
     * @param array $instructions
     * @param array $state
     * @param array $context
     * @return array The updated state
     */
    public function process(array $instructions, array $state, array $context): array;
}
