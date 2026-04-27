<?php

declare(strict_types=1);

namespace App\Services\Adaptive\Handlers\Facts\Virtual;

use App\Rules\Adaptive\Constants\FactConstants;
use App\Services\Adaptive\Handlers\Facts\VirtualFactProcessorInterface;

final class StrugglingProcessor implements VirtualFactProcessorInterface
{
    public function process(array $facts): array|string|null
    {
        $isFail = in_array(FactConstants::SCORE_FAIL, $facts, true);
        $isSlow = in_array(FactConstants::TIME_SLOW, $facts, true);

        return ($isFail && $isSlow) ? FactConstants::V_STRUGGLING : null;
    }
}
