<?php

declare(strict_types=1);

namespace App\Services\Adaptive\Handlers;

use App\Services\Adaptive\Handlers\Facts\VirtualFactProcessorInterface;

final class VirtualFactHandler
{
    /** @var VirtualFactProcessorInterface[] */
    private array $processors;

    public function __construct()
    {
        $this->processors = [
            new Facts\Virtual\ExcellentResultProcessor(),
            new Facts\Virtual\StrugglingProcessor(),
            new Facts\Virtual\SteadyLearnerProcessor(),
            new Facts\Virtual\UnfocusedProcessor(),
            new Facts\Virtual\MasteryMilestoneProcessor(),
        ];
    }

    /**
     * Derive virtual facts from primary facts.
     */
    public function derive(array $facts): array
    {
        $virtual = [];

        foreach ($this->processors as $processor) {
            $result = $processor->process($facts);
            if ($result) {
                if (is_array($result)) {
                    $virtual = array_merge($virtual, $result);
                } else {
                    $virtual[] = $result;
                }
            }
        }

        return array_values(array_unique($virtual));
    }
}
