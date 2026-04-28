<?php

declare(strict_types=1);

namespace App\Services\Adaptive\Handlers\Actions;

use App\Rules\Adaptive\Constants\ActionConstants;

final class FeedbackProcessor implements ActionProcessorInterface
{
    public function process(array $instructions, array $state, array $context): array
    {
        if (isset($instructions[ActionConstants::KEY_FLOW])) {
            $flow                 = $instructions[ActionConstants::KEY_FLOW];
            $state['next_action'] = $flow; // Tetap simpan sebagai next_action untuk frontend agar tidak breaking change di JS

            // Jika harus kembali ke materi, arahkan ke sub_materi yang spesifik jika tersedia di context
            if ($flow === ActionConstants::FLOW_REVIEW && isset($context['sub_material_id'])) {
                $state['target_sub_material_id'] = $context['sub_material_id'];
            }
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
