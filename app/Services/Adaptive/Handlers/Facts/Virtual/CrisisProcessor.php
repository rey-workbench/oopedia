<?php

declare(strict_types=1);

namespace App\Services\Adaptive\Handlers\Facts\Virtual;

use App\Rules\Adaptive\Constants\FactConstants;
use App\Rules\Adaptive\Constants\StudentStateSchema;
use App\Services\Adaptive\Handlers\Facts\VirtualFactProcessorInterface;

final class CrisisProcessor implements VirtualFactProcessorInterface
{
    public function process(array $facts, array $state): array|string|null
    {
        $wrongCount = (int) ($state[StudentStateSchema::WRONG_COUNT] ?? 0);
        
        // Jika sudah salah 5 kali atau lebih, nyalakan alarm krisis
        return ($wrongCount >= 5) ? FactConstants::V_CRISIS_STATE : null;
    }
}
