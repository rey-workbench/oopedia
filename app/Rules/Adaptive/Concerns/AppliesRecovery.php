<?php

namespace App\Rules\Adaptive\Concerns;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

trait AppliesRecovery
{
    protected function applySyntaxRecovery(array $state, string $message): array
    {
        $state['recommendation'] = 'Latihan Sintaksis';
        $state['next_action']    = AdaptiveConstants::ACTION_STUDY_SYNTAX;
        $state['message']        = $message;
        $state['recovery_type']  = AdaptiveConstants::RECOVERY_SYNTAX;

        return $state;
    }

    protected function applyLogicRecovery(array $state, string $message): array
    {
        $state['recommendation'] = 'Pemahaman Konsep';
        $state['next_action']    = AdaptiveConstants::ACTION_STUDY_THEORY;
        $state['message']        = $message;
        $state['recovery_type']  = AdaptiveConstants::RECOVERY_LOGIC;

        return $state;
    }

    protected function applyIndependentRecovery(array $state, string $message): array
    {
        $state['recommendation'] = 'Coba Secara Mandiri';
        $state['next_action']    = AdaptiveConstants::ACTION_NEXT_QUESTION;
        $state['message']        = $message;
        $state['recovery_type']  = AdaptiveConstants::RECOVERY_INDEPENDENT;

        return $state;
    }

    protected function applyRemedialIndependent(array $state): array
    {
        $state['recommendation'] = 'Perkuat Pemahaman';
        $state['next_action']    = AdaptiveConstants::ACTION_STUDY_MIXED;
        $state['message']        = 'Nilai Anda perlu sedikit perbaikan. Mari perkuat pemahaman melalui materi komprehensif sebelum melanjutkan.';
        $state['recovery_type']  = AdaptiveConstants::RECOVERY_INDEPENDENT;

        return $state;
    }
}
