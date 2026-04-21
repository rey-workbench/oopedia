<?php

namespace App\Rules\Adaptive\Concerns;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

trait AppliesProgression
{
    use AppliesAchievement;

    protected function applyStandardPromotion(array $state, bool $isCorrect): array
    {
        $message = $isCorrect
            ? 'Selamat! Jawabanmu benar. Mari lanjut ke tantangan berikutnya.'
            : 'Jawaban kurang tepat. Jangan menyerah, mari coba lagi atau ulas materi jika diperlukan.';

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
            message: 'Luar biasa! Kamu menguasai ini dengan sangat cepat. Mari percepat langkahmu ke tingkat menengah.',
            targetDifficulty: AdaptiveConstants::DIFFICULTY_MEDIUM,
            fastTrackActive: true,
        );
    }

    protected function applyCriticalBacktracking(array $state): array
    {
        return $this->setProgressionState(
            state: $state,
            nextAction: AdaptiveConstants::ACTION_REDUCE_DIFFICULTY,
            message: 'Sepertinya topik ini cukup menantang. Tidak apa-apa, mari ulas kembali dasar-dasarnya agar fondasi belajarmu lebih kuat.',
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
            message: 'Fantastis! Kamu sudah sangat mahir di level menengah. Siap untuk tantangan tersulit (Hard)?',
            targetDifficulty: AdaptiveConstants::DIFFICULTY_HARD,
            fastTrackActive: true,
        );
    }

    protected function applyAcceleratedMaterialPromotion(array $state, array $context = []): array
    {
        $state = $this->setProgressionState(
            state: $state,
            nextAction: AdaptiveConstants::ACTION_NEXT_MATERIAL,
            message: 'Penguasaan materimu luar biasa! Kamu bisa melangkah langsung ke materi berikutnya.',
            fastTrackActive: true,
        );

        return $this->applyModuleProgress($state, $context);
    }

    protected function applyModuleGraduation(array $state, array $context = []): array
    {
        $state = $this->setProgressionState(
            state: $state,
            nextAction: AdaptiveConstants::ACTION_FINISH_MATERIAL,
            message: 'Luar biasa! Dari ketepatanmu menjawab soal tingkat sulit, kamu terbukti telah menguasai modul ini sepenuhnya. Kamu lulus lebih awal, mari lanjut ke modul berikutnya!',
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
