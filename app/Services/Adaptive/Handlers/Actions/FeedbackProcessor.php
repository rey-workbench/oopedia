<?php

declare(strict_types=1);

namespace App\Services\Adaptive\Handlers\Actions;

use App\Rules\Adaptive\Constants\ActionConstants;

final class FeedbackProcessor implements ActionProcessorInterface
{
    public function process(array $instructions, array $state, array $context): array
    {
        if (isset($instructions[ActionConstants::KEY_NEXT_ACTION])) {
            $state['next_action'] = $instructions[ActionConstants::KEY_NEXT_ACTION];
        }

        if (isset($instructions[ActionConstants::KEY_MESSAGE])) {
            $state['_feedback_message'] = $instructions[ActionConstants::KEY_MESSAGE];
        }

        if (isset($instructions[ActionConstants::KEY_TITLE])) {
            $state['_feedback_title'] = $instructions[ActionConstants::KEY_TITLE];
        }

        return $state;
    }
}
