<?php

namespace App\Rules\Adaptive\Concerns;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

trait AppliesProgression
{
    use AppliesAchievement;

    protected function applyStandardPromotion(array $state, bool $isCorrect): array
    {
        $message = $isCorrect
            ? 'Jawaban tepat! Mari lanjut ke soal berikutnya.'
            : 'Jawaban kurang tepat. Mari coba lagi atau ulas kembali materi jika kesulitan.';

        return $this->setProgressionState(
            state: $state,
            nextAction: AdaptiveConstants::ACTION_NEXT_QUESTION,
            message: $message,
        );
    }

    protected function applyAcceleratedJump(array $state): array
    {
        return $this->setProgressionState(
            state: $state,
            nextAction: AdaptiveConstants::ACTION_NEXT_QUESTION,
            message: 'Luar biasa! Penguasaan dan kecepatan Anda sangat baik. Lanjutkan ke level menengah.',
            targetDifficulty: AdaptiveConstants::DIFFICULTY_MEDIUM,
            fastTrackActive: true,
        );
    }

    protected function applyCriticalBacktracking(array $state): array
    {
        return $this->setProgressionState(
            state: $state,
            nextAction: AdaptiveConstants::ACTION_REDUCE_DIFFICULTY,
            message: 'Soal ini sepertinya terlalu sulit sekarang. Mari turunkan tingkat kesulitan dan perkuat fondasi Anda.',
            targetDifficulty: AdaptiveConstants::DIFFICULTY_BEGINNER,
            recommendation: 'Review Dasar',
            fastTrackActive: false,
        );
    }

    protected function applyMasteryMedium(array $state): array
    {
        return $this->setProgressionState(
            state: $state,
            nextAction: AdaptiveConstants::ACTION_NEXT_QUESTION,
            message: 'Luar biasa! Penguasaan dan kecepatan Anda di level menengah sangat baik. Lanjutkan ke level sulit (Hard).',
            targetDifficulty: AdaptiveConstants::DIFFICULTY_HARD,
            fastTrackActive: true,
        );
    }

    protected function applyAcceleratedMaterialPromotion(array $state, array $context): array
    {
        $state = $this->setProgressionState(
            state: $state,
            nextAction: AdaptiveConstants::ACTION_NEXT_MATERIAL,
            message: 'Luar biasa! Penguasaan materi Anda sangat baik. Mari langsung lanjut ke materi berikutnya!',
            fastTrackActive: true,
        );

        return $this->applyModuleProgress($state, $context);
    }

    private function setProgressionState(
        array $state,
        string $nextAction,
        string $message,
        ?string $targetDifficulty = null,
        ?string $recommendation = null,
        ?bool $fastTrackActive = null,
    ): array {
        $state['next_action'] = $nextAction;
        $state['message']     = $message;

        if ($targetDifficulty !== null) {
            $state['target_difficulty'] = $targetDifficulty;
        }

        if ($recommendation !== null) {
            $state['recommendation'] = $recommendation;
        }

        if ($fastTrackActive !== null) {
            $state['fast_track_active'] = $fastTrackActive;
        }

        return $state;
    }
}
