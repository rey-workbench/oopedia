<?php

declare(strict_types=1);

namespace App\Services\Adaptive\Handlers\Facts\Primary;

use App\Enums\Lms\QuestionDifficulty;
use App\Rules\Adaptive\Constants\FactConstants;
use App\Services\Adaptive\Handlers\Facts\PrimaryFactProcessorInterface;

final class DifficultyProcessor implements PrimaryFactProcessorInterface
{
    public function process(bool $isCorrect, bool $usedHint, int $timeSpent, QuestionDifficulty $difficulty): ?string
    {
        return match ($difficulty) {
            QuestionDifficulty::BEGINNER => FactConstants::DIFF_BEGINNER,
            QuestionDifficulty::MEDIUM   => FactConstants::DIFF_MEDIUM,
            QuestionDifficulty::HARD     => FactConstants::DIFF_HARD,
            default                      => FactConstants::DIFF_BEGINNER,
        };
    }
}
