<?php

declare(strict_types=1);

namespace App\Services\Adaptive\Handlers\Facts\Virtual;

use App\Rules\Adaptive\Constants\FactConstants;
use App\Services\Adaptive\Handlers\Facts\VirtualFactProcessorInterface;

final class ExcellentResultProcessor implements VirtualFactProcessorInterface
{
    public function process(array $facts, array $state): array|string|null
    {
        $isPass  = in_array(FactConstants::SCORE_PASS, $facts, true);
        $isQuick = in_array(FactConstants::TIME_QUICK, $facts, true);

        return ($isPass && $isQuick) ? FactConstants::V_EXCELLENT_RESULT : null;
    }
}
