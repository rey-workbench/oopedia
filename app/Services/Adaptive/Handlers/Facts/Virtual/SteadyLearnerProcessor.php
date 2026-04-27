<?php

declare(strict_types=1);

namespace App\Services\Adaptive\Handlers\Facts\Virtual;

use App\Rules\Adaptive\Constants\FactConstants;
use App\Services\Adaptive\Handlers\Facts\VirtualFactProcessorInterface;

final class SteadyLearnerProcessor implements VirtualFactProcessorInterface
{
    public function process(array $facts, array $state): array|string|null
    {
        $isPass = in_array(FactConstants::SCORE_PASS, $facts, true);
        $isSlow = in_array(FactConstants::TIME_SLOW, $facts, true);

        return ($isPass && $isSlow) ? FactConstants::V_STEADY_LEARNER : null;
    }
}
