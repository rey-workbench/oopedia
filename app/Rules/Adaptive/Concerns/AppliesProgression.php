<?php

namespace App\Rules\Adaptive\Concerns;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

trait AppliesProgression
{
    use AppliesAchievement;

    protected function applyStandardPromotion(array $state, bool $isCorrect): array
    {
        $state['next_action'] = AdaptiveConstants::ACTION_NEXT_QUESTION;
        $state['message']     = $isCorrect
            ? 'Jawaban tepat! Mari lanjut ke soal berikutnya.'
            : 'Jawaban kurang tepat. Mari coba lagi atau ulas kembali materi jika kesulitan.';

        return $state;
    }

    protected function applyAcceleratedJump(array $state): array
    {
        $state['fast_track_active'] = true;
        $state['target_difficulty'] = AdaptiveConstants::DIFFICULTY_MEDIUM;
        $state['next_action']       = AdaptiveConstants::ACTION_NEXT_QUESTION;
        $state['message']           = 'Luar biasa! Penguasaan dan kecepatan Anda sangat baik. '
            . 'Lanjutkan ke level menengah.';

        return $state;
    }

    protected function applyCriticalBacktracking(array $state): array
    {
        $state['fast_track_active'] = false;
        $state['target_difficulty'] = AdaptiveConstants::DIFFICULTY_BEGINNER;
        $state['recommendation']    = 'Review Dasar';
        $state['next_action']       = AdaptiveConstants::ACTION_REDUCE_DIFFICULTY;
        $state['message']           = 'Soal ini sepertinya terlalu sulit sekarang. '
            . 'Mari turunkan tingkat kesulitan dan perkuat fondasi Anda.';

        return $state;
    }

    protected function applyMasteryMedium(array $state): array
    {
        $state['fast_track_active'] = true;
        $state['target_difficulty'] = AdaptiveConstants::DIFFICULTY_HARD;
        $state['next_action']       = AdaptiveConstants::ACTION_NEXT_QUESTION;
        $state['message']           = 'Luar biasa! Penguasaan dan kecepatan Anda di level menengah sangat baik. '
            . 'Lanjutkan ke level sulit (Hard).';

        return $state;
    }

    protected function applyAcceleratedMaterialPromotion(array $state, array $context): array
    {
        $state['fast_track_active'] = true;
        $state['next_action']       = AdaptiveConstants::ACTION_NEXT_MATERIAL;
        $state['message']           = 'Luar biasa! Penguasaan materi Anda sangat baik. '
            . 'Mari langsung lanjut ke materi berikutnya!';

        return $this->applyModuleProgress($state, $context);
    }
}
