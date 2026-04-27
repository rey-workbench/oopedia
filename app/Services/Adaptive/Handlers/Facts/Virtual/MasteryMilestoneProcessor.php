<?php

declare(strict_types=1);

namespace App\Services\Adaptive\Handlers\Facts\Virtual;

use App\Rules\Adaptive\Constants\FactConstants;
use App\Services\Adaptive\Handlers\Facts\VirtualFactProcessorInterface;

final class MasteryMilestoneProcessor implements VirtualFactProcessorInterface
{
    public function process(array $facts, array $state): array|string|null
    {
        $isPass = in_array(FactConstants::SCORE_PASS, $facts, true);
        if (!$isPass) {
            return null;
        }

        $virtual = [];

        if (in_array(FactConstants::DIFF_BEGINNER, $facts, true)) {
            $virtual[] = FactConstants::V_MASTERY_BEGINNER;
        }

        if (in_array(FactConstants::DIFF_MEDIUM, $facts, true)) {
            $virtual[] = FactConstants::V_MASTERY_MEDIUM;
        }

        if (in_array(FactConstants::DIFF_HARD, $facts, true)) {
            $virtual[] = FactConstants::V_MASTERY_HARD;
        }

        return $virtual;
    }
}
