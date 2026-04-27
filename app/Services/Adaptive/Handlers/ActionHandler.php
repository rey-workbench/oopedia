<?php

declare(strict_types=1);

namespace App\Services\Adaptive\Handlers;
use App\Services\Adaptive\Handlers\Actions\ActionProcessorInterface;

final class ActionHandler
{
    /**
     * @var ActionProcessorInterface[]
     */
    private array $processors;

    public function __construct()
    {
        // Define the pipeline order
        $this->processors = [
            new Actions\ModuleProcessor(),
            new Actions\BadgeProcessor(),
            new Actions\CertificationProcessor(),
            new Actions\StateProcessor(),
            new Actions\FeedbackProcessor(),
        ];
    }

    /**
     * Apply action instructions to the student state.
     */
    public function apply(array $instructions, array $state, array $context): array
    {
        foreach ($this->processors as $processor) {
            $state = $processor->process($instructions, $state, $context);
        }

        return $state;
    }
}
