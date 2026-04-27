<?php

declare(strict_types=1);

namespace App\Services\Adaptive\Handlers\Facts\Virtual;

use App\Rules\Adaptive\Constants\FactConstants;
use App\Rules\Adaptive\Constants\StudentStateSchema;
use App\Services\Adaptive\Handlers\Facts\VirtualFactProcessorInterface;

final class BoredomProcessor implements VirtualFactProcessorInterface
{
    public function process(array $facts, array $state): array|string|null
    {
        $streak = (int) ($state[StudentStateSchema::CURRENT_STREAK] ?? 0);
        $isBeginner = in_array(FactConstants::DIFF_BEGINNER, $facts, true);
        $isPass = in_array(FactConstants::SCORE_PASS, $facts, true);

        // Jika sudah benar berturut-turut (>7) tapi masih di level beginner, kemungkinan bosan
        return ($streak > 7 && $isBeginner && $isPass) ? FactConstants::V_BOREDOM_DETECTED : null;
    }
}
