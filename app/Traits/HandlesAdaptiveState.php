<?php

namespace App\Traits;

use App\Enums\Lms\QuestionDifficulty;

trait HandlesAdaptiveState
{
    abstract protected function getPerformanceService();

    abstract protected function getGuestProgressService();

    public function resolveStudentStateData(
        bool $isGuest,
        int|string $userId,
        int|string $materialId,
        &$targetDifficulty,
    ): array {
        if ($isGuest) {
            $guestSvc = $this->getGuestProgressService();

            return [
                'gamification' => [
                    'xp'             => $guestSvc->getGamificationState()['xp'],
                    'streak'         => $guestSvc->getGamificationState()['streak'],
                    'level'          => 'Tamu',
                ],
                'performance' => [
                    'hints_available' => 3,
                    'total_answered'  => count($guestSvc->getProgress()),
                ],
                'learning_profile' => [],
            ];
        }

        $studentState = $this->getPerformanceService()->getStudentState($userId);
        if (! $studentState) {
            return [];
        }

        // Reset navigation if user switched material
        if ($studentState->current_material_id             !== null
            && (string) $studentState->current_material_id !== (string) $materialId
        ) {
            $this->getPerformanceService()->resetMaterialMetrics($userId);
            $studentState->target_difficulty   = null;
            $studentState->current_material_id = null;
        }

        $targetDifficulty = QuestionDifficulty::tryFrom($studentState->target_difficulty);

        return [
            'xp'             => $studentState->xp,
            'level'          => $studentState->level,
            'streak'         => $studentState->streak,
            'learning_style' => $studentState->learning_style,
            'total_answered' => $studentState->total_answered,
            'hints_available'=> $studentState->hints_available,
        ];
    }

    /** Kept for backward compat in MaterialQuestionController — delegates to resetMaterialMetrics */
    public function resetMaterialScopedState($studentState, int|string $materialId): void
    {
        $this->getPerformanceService()->resetMaterialMetrics($studentState->user_id);
    }
}
