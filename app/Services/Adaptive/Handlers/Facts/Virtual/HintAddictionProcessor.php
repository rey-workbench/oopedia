<?php

declare(strict_types=1);

namespace App\Services\Adaptive\Handlers\Facts\Virtual;

use App\Rules\Adaptive\Constants\FactConstants;
use App\Services\Adaptive\Handlers\Facts\VirtualFactProcessorInterface;

final class HintAddictionProcessor implements VirtualFactProcessorInterface
{
    public function process(array $facts, array $state): array|string|null
    {
        $isPass = in_array(FactConstants::SCORE_PASS, $facts, true);
        $isHint = in_array(FactConstants::HINT_USED, $facts, true);

        return ($isPass && $isHint) ? FactConstants::V_HINT_DEPENDENT : null;
    }
}
