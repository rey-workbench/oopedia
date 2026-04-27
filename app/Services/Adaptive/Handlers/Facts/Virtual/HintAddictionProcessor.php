<?php

declare(strict_types=1);

namespace App\Services\Adaptive\Handlers\Facts\Virtual;

use App\Rules\Adaptive\Constants\FactConstants;
use App\Services\Adaptive\Handlers\Facts\VirtualFactProcessorInterface;

final class HintAddictionProcessor implements VirtualFactProcessorInterface
{
    public function process(array $facts, array $state): array|string|null
    {
        $usedHint = in_array(FactConstants::HINT_USED, $facts, true);
        $isStruggling = in_array(FactConstants::V_STRUGGLING, $facts, true);

        // Mahasiswa dianggap tergantung bantuan jika dia sedang kesulitan DAN masih menekan hint
        return ($usedHint && $isStruggling) ? FactConstants::V_HINT_DEPENDENT : null;
    }
}
