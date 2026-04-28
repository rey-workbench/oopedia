<?php

declare(strict_types=1);

namespace App\Services\Adaptive\Handlers\Facts\Primary;

use App\Enums\Lms\QuestionDifficulty;
use App\Rules\Adaptive\Constants\FactConstants;
use App\Rules\Adaptive\Constants\PedagogicalConstants;
use App\Services\Adaptive\Handlers\Facts\PrimaryFactProcessorInterface;

final class EfficiencyProcessor implements PrimaryFactProcessorInterface
{
    public function process(bool $isCorrect, bool $usedHint, int $timeSpent, QuestionDifficulty $difficulty): ?string
    {
        return ($timeSpent <= PedagogicalConstants::TIME_QUICK_THRESHOLD)
            ? FactConstants::TIME_QUICK
            : FactConstants::TIME_SLOW;
    }
}
