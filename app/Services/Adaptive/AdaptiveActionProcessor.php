<?php

declare(strict_types=1);

namespace App\Services\Adaptive;

use App\Contracts\Services\AdaptiveActionProcessorInterface;
use App\Enums\Adaptive\AdaptiveActionId;
use App\Models\StudentState;

final class AdaptiveActionProcessor implements AdaptiveActionProcessorInterface
{
    private const array DIFFICULTY_ORDER = ['beginner', 'medium', 'hard', 'final'];

    public function process(StudentState $studentState, array $actions, string $materialId): StudentState
    {
        $adaptiveState = $studentState->adaptive_state ?? [];

        // Reset transient flags before applying new actions
        $adaptiveState['show_guidance']  = false;
        $adaptiveState['needs_remedial'] = false;

        foreach ($actions as $action) {
            // Recommendation is now just an ID string or an object with an ID
            $actionIdString = is_array($action) ? $action['id'] : $action;

            $actionId = AdaptiveActionId::tryFrom($actionIdString);
            if (! $actionId) {
                continue;
            }

            match ($actionId) {
                AdaptiveActionId::REMEDIAL           => $this->handleRemedial($studentState, $adaptiveState, $materialId),
                AdaptiveActionId::REMEDIAL_INTENSIVE => $this->handleRemedialIntensive($studentState, $adaptiveState, $materialId),
                AdaptiveActionId::REDUCE_DIFF        => $this->handleReduceDifficulty($studentState),
                AdaptiveActionId::INCREASE_DIFF      => $this->handleIncreaseDifficulty($studentState, 1),
                AdaptiveActionId::REDUCE_HINT        => $this->handleReduceHint($studentState, $adaptiveState),
                AdaptiveActionId::NEW_CHALLENGE      => $this->handleNewChallenge($studentState),
                AdaptiveActionId::STREAK_BONUS       => $this->handleStreakBonus($studentState, $adaptiveState),
                AdaptiveActionId::CERTIFICATION      => $this->handleCertification($adaptiveState),
                AdaptiveActionId::SHOW_GUIDANCE      => $this->handleShowGuidance($adaptiveState),
                AdaptiveActionId::NOTIFY_TEACHER     => $this->handleNotifyTeacher($adaptiveState),
                AdaptiveActionId::GIVE_HINT          => $this->handleGiveHint($studentState),
                AdaptiveActionId::FEEDBACK           => null,
            };
        }

        $studentState->adaptive_state = $adaptiveState;

        return $studentState;
    }

    private function handleRemedial(StudentState $studentState, array &$adaptiveState, string $materialId): void
    {
        $adaptiveState['needs_remedial']       = true;
        $adaptiveState['remedial_material_id'] = $materialId;
        $studentState->target_difficulty       = 'beginner';
    }

    private function handleRemedialIntensive(StudentState $studentState, array &$adaptiveState, string $materialId): void
    {
        $this->handleRemedial($studentState, $adaptiveState, $materialId);
        $adaptiveState['forced_easy_count'] = 5;
    }

    private function handleReduceDifficulty(StudentState $studentState): void
    {
        $currentDiff  = $studentState->target_difficulty ?? 'beginner';
        $currentIndex = array_search($currentDiff, self::DIFFICULTY_ORDER, true);

        if ($currentIndex > 0) {
            $studentState->target_difficulty = self::DIFFICULTY_ORDER[$currentIndex - 1];
        }
    }

    private function handleIncreaseDifficulty(StudentState $studentState, int $steps): void
    {
        $currentDiff  = $studentState->target_difficulty ?? 'beginner';
        $currentIndex = array_search($currentDiff, self::DIFFICULTY_ORDER, true);

        $newIndex                        = min(2, $currentIndex + $steps);
        $studentState->target_difficulty = self::DIFFICULTY_ORDER[$newIndex];
    }

    private function handleReduceHint(StudentState $studentState, array &$adaptiveState): void
    {
        $currentMax                             = $adaptiveState['max_hints_per_session'] ?? 3;
        $adaptiveState['max_hints_per_session'] = max(0, $currentMax - 1);
        $studentState->hints_available          = min($studentState->hints_available, $adaptiveState['max_hints_per_session']);
        $adaptiveState['scaffold_mode']         = 'minimal';
    }

    private function handleNewChallenge(StudentState $studentState): void
    {
        $studentState->xp += 100;
        $studentState->hints_available = ($studentState->hints_available ?? 0) + 1;
    }

    private function handleStreakBonus(StudentState $studentState, array &$adaptiveState): void
    {
        $studentState->xp += 50;
        $badges                  = $adaptiveState['badges'] ?? [];
        $badges[]                = 'streak_' . ($studentState->streak ?? 0);
        $adaptiveState['badges'] = array_unique($badges);
    }

    private function handleCertification(array &$adaptiveState): void
    {
        $certs                            = $adaptiveState['certifications'] ?? [];
        $certs[]                          = 'GOLD';
        $adaptiveState['certifications']  = array_unique($certs);
        $adaptiveState['unlock_advanced'] = true;
        $this->handleNotifyTeacher($adaptiveState);
    }

    private function handleShowGuidance(array &$adaptiveState): void
    {
        $adaptiveState['show_guidance'] = true;
    }

    private function handleNotifyTeacher(array &$adaptiveState): void
    {
        $adaptiveState['notify_teacher']      = true;
        $adaptiveState['notify_teacher_type'] = 'general';
    }

    private function handleGiveHint(StudentState $studentState): void
    {
        $studentState->hints_available = ($studentState->hints_available ?? 0) + 1;
    }
}
