<?php

namespace App\Traits;

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
            return [
                'gamification' => [
                    'global_xp'       => $this->getGuestProgressService()->getGamificationState()['global_xp'],
                    'current_streak'  => $this->getGuestProgressService()->getGamificationState()['current_streak'],
                    'current_level'   => 'Tamu',
                ],
                'performance' => [
                    'hints_available'          => 3,
                    'total_questions_answered' => count($this->getGuestProgressService()->getProgress()),
                ],
                'learning_profile' => [],
            ];
        }

        $studentState = $this->getPerformanceService()->getStudentState($userId);
        if (! $studentState) {
            return [];
        }

        $adaptiveState = $studentState->adaptive_state ?? [];
        if (is_string($adaptiveState)) {
            $adaptiveState = json_decode($adaptiveState, true) ?? [];
        }

        $this->handleMaterialChange($studentState, $adaptiveState, $materialId);

        $targetDifficulty = $adaptiveState['target_difficulty'] ?? null;

        return [
            'gamification'     => $studentState->gamification_data,
            'performance'      => $studentState->performance_metrics,
            'learning_profile' => $studentState->learning_profile,
        ];
    }

    public function handleMaterialChange($studentState, array &$adaptiveState, int|string $materialId): void
    {
        $lastMaterialId = $adaptiveState['current_material_id'] ?? null;
        if ($lastMaterialId !== null && (string) $lastMaterialId !== (string) $materialId) {
            $adaptiveState['target_difficulty'] = null;
            $adaptiveState['fast_track_active'] = false;
            $adaptiveState['last_rule']         = null;

            $metrics                           = $studentState->performance_metrics ?? [];
            $metrics['wrong_streak']           = 0;
            $studentState->performance_metrics = $metrics;

            $studentState->adaptive_state = $adaptiveState;
            $studentState->save();
        }
    }

    public function resetMaterialScopedState($studentState, array &$adaptiveState): void
    {
        $adaptiveState['target_difficulty'] = null;
        $adaptiveState['fast_track_active'] = false;
        $adaptiveState['last_rule']         = null;

        $metrics                           = $studentState->performance_metrics ?? [];
        $metrics['wrong_streak']           = 0;
        $studentState->performance_metrics = $metrics;

        $studentState->adaptive_state = $adaptiveState;
        $studentState->save();
    }
}
