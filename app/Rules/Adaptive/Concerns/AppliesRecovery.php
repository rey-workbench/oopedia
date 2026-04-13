<?php

namespace App\Rules\Adaptive\Concerns;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

trait AppliesRecovery
{
    protected function applySyntaxRecovery(array $state, string $message): array
    {
        return $this->setRecoveryState(
            state: $state,
            recommendation: 'Latihan Sintaksis',
            nextAction: AdaptiveConstants::ACTION_STUDY_SYNTAX,
            message: $message,
            recoveryType: AdaptiveConstants::RECOVERY_SYNTAX,
        );
    }

    protected function applyLogicRecovery(array $state, string $message): array
    {
        return $this->setRecoveryState(
            state: $state,
            recommendation: 'Pemahaman Konsep',
            nextAction: AdaptiveConstants::ACTION_STUDY_THEORY,
            message: $message,
            recoveryType: AdaptiveConstants::RECOVERY_LOGIC,
        );
    }

    protected function applyRemedialIndependent(array $state): array
    {
        return $this->setRecoveryState(
            state: $state,
            recommendation: 'Perkuat Pemahaman',
            nextAction: AdaptiveConstants::ACTION_STUDY_MIXED,
            message: 'Nilai Anda perlu sedikit perbaikan. Mari perkuat pemahaman melalui materi komprehensif sebelum melanjutkan.',
            recoveryType: AdaptiveConstants::RECOVERY_INDEPENDENT,
        );
    }

    private function setRecoveryState(
        array $state,
        string $recommendation,
        string $nextAction,
        string $message,
        string $recoveryType,
    ): array {
        $state['recommendation'] = $recommendation;
        $state['next_action']    = $nextAction;
        $state['message']        = $message;
        $state['recovery_type']  = $recoveryType;

        return $state;
    }
}
