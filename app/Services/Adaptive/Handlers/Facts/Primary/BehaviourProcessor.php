<?php

declare(strict_types=1);

namespace App\Services\Adaptive\Handlers\Facts\Primary;

use App\Enums\Lms\QuestionDifficulty;
use App\Rules\Adaptive\Constants\FactConstants;
use App\Services\Adaptive\Handlers\Facts\PrimaryFactProcessorInterface;

final class BehaviourProcessor implements PrimaryFactProcessorInterface
{
    public function process(bool $isCorrect, bool $usedHint, int $timeSpent, QuestionDifficulty $difficulty): ?string
    {
        return $usedHint ? FactConstants::HINT_USED : null;
    }
}
