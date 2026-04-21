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
            message: 'Sepertinya ada beberapa konsep yang perlu diperdalam. Mari pelajari materi ulasan ini sejenak agar pemahamanmu makin mantap.',
            recoveryType: AdaptiveConstants::RECOVERY_INDEPENDENT,
        );
    }

    protected function applyRemedialBeginner(array $state): array
    {
        return $this->setRecoveryState(
            state: $state,
            recommendation: 'Review Menyeluruh',
            nextAction: AdaptiveConstants::ACTION_REMEDIAL_AT_BEGINNER,
            message: 'Jangan berkecil hati! Mari kita mulai kembali dari dasar agar kamu bisa memahami konsep ini dengan lebih jernih.',
            recoveryType: AdaptiveConstants::RECOVERY_INDEPENDENT,
        );
    }

    protected function applyReviewPreviousMaterial(array $state): array
    {
        return $this->setRecoveryState(
            state: $state,
            recommendation: 'Ulas Materi Sebelumnya',
            nextAction: AdaptiveConstants::ACTION_REVIEW_PREVIOUS,
            message: 'Topik sebelumnya sepertinya sangat berkaitan dengan ini. Yuk, ulas kembali sejenak agar kamu lebih siap!',
            recoveryType: AdaptiveConstants::RECOVERY_INDEPENDENT,
        );
    }

    protected function applyFastWrongRecovery(array $state): array
    {
        return $this->setRecoveryState(
            state: $state,
            recommendation: 'Ulas Kembali',
            nextAction: AdaptiveConstants::ACTION_FAST_WRONG_RECOVERY,
            message: 'Kamu mengerjakan sangat cepat, namun ketelitian juga penting. Yuk, pelajari kembali materinya pelan-pelan.',
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
