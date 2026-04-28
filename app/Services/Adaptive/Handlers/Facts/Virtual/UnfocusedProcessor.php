<?php

declare(strict_types=1);

namespace App\Services\Adaptive\Handlers\Facts\Virtual;

use App\Rules\Adaptive\Constants\FactConstants;
use App\Services\Adaptive\Handlers\Facts\VirtualFactProcessorInterface;

final class UnfocusedProcessor implements VirtualFactProcessorInterface
{
    public function process(array $facts, array $state): array|string|null
    {
        $isFail  = in_array(FactConstants::SCORE_FAIL, $facts, true);
        $isQuick = in_array(FactConstants::TIME_QUICK, $facts, true);

        return ($isFail && $isQuick) ? FactConstants::V_UNFOCUSED : null;
    }
}
