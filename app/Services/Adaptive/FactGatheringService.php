<?php

namespace App\Services\Adaptive;

use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Contracts\Services\FactGatheringServiceInterface;
use App\Models\Material;
use App\Models\StudentState;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

/**
 * FactGatheringService
 *
 * Responsible for gathering facts (G01-G25) from student state and context.
 * Facts are used by adaptive rules for decision making.
 */
class FactGatheringService implements FactGatheringServiceInterface
{
    public function __construct(
        protected ProgressRepositoryInterface $progressRepo,
        protected QuestionRepositoryInterface $questionRepo,
    ) {}

    public function gatherFacts(
        StudentState $studentState,
        bool $isCorrect,
        bool $usedHint,
        int $score,
        int $timeSpent,
        string $difficulty,
        int $questionId,
        int $materialId,
        ?int $moduleId = null,
    ): array {
        $facts = [];

        // G01-G04: Score Facts
        $facts = array_merge($facts, $this->getScoreFacts($score, $isCorrect));

        // G05-G06: Time Facts
        $facts = array_merge($facts, $this->getTimeFacts($timeSpent, $difficulty));

        // G07-G08: Learning Style Facts
        $facts = array_merge($facts, $this->getLearningStyleFacts($studentState));

        // G09-G10: Error Type Facts
        $facts = array_merge($facts, $this->getErrorTypeFacts($studentState, $questionId, $isCorrect));

        // G11-G12: Hint Facts
        $facts[] = $usedHint ? AdaptiveConstants::FACT_HINT_USED : AdaptiveConstants::FACT_HINT_NONE;

        // G13-G25: Module Facts
        if ($moduleId) {
            $facts[] = $this->getModuleFact($moduleId);
        }

        // G15-G17: Difficulty Facts
        $facts[] = $this->getDifficultyFact($difficulty);

        // G18: Final Project (check difficulty='final')
        if ($difficulty === 'final') {
            $facts[] = AdaptiveConstants::FACT_IS_FINAL_PROJECT;
        }

        // G19-G21: Unlock Status Facts
        $facts = array_merge($facts, $this->getUnlockStatusFacts($studentState, $materialId));

        // G22: Persistent Fail
        if ($this->isPersistentFail($studentState->user_id, $questionId)) {
            $facts[] = AdaptiveConstants::FACT_PERSISTENT_FAIL;
        }

        // G26: Satisfactory Progress (>= 60% of current difficulty questions completed)
        if ($this->hasSatisfactoryProgress($studentState->user_id, $materialId, $difficulty)) {
            $facts[] = AdaptiveConstants::FACT_SATISFACTORY_PROGRESS;
        }

        return array_unique($facts);
    }

    /**
     * Get score-based facts (G01-G04).
     */
    protected function getScoreFacts(int $score, bool $isCorrect): array
    {
        // Normalize score: correct answers get at least 70, wrong get max 69
        $finalScore = $isCorrect ? max($score, 70) : min($score, 69);

        if ($finalScore < 40) {
            return [AdaptiveConstants::FACT_SCORE_CRITICAL];
        }
        if ($finalScore < 70) {
            return [AdaptiveConstants::FACT_SCORE_REMEDIAL];
        }
        if ($finalScore < 90) {
            return [AdaptiveConstants::FACT_SCORE_STANDARD];
        }

        return [AdaptiveConstants::FACT_SCORE_MASTERY];
    }

    /**
     * Get time-based facts (G05-G06).
     */
    protected function getTimeFacts(int $timeSpent, string $difficulty = 'beginner'): array
    {
        // Allocation based on difficulty
        $allocatedTimeMap = [
            'beginner' => 45,
            'medium'   => 90,
            'hard'     => 150,
            'final'    => 300,
        ];

        $allocatedTime = $allocatedTimeMap[$difficulty] ?? 60;
        $percentage    = ($timeSpent / $allocatedTime) * 100;

        return $percentage < 50 ? [AdaptiveConstants::FACT_TIME_FAST] : [AdaptiveConstants::FACT_TIME_NORMAL];
    }

    /**
     * Get learning style facts (G07-G08).
     */
    protected function getLearningStyleFacts(StudentState $state): array
    {
        // Use accessor from model
        $style = $state->learning_style;

        return $style === 'visual' ? [AdaptiveConstants::FACT_STYLE_VISUAL] : [AdaptiveConstants::FACT_STYLE_TEXTUAL];
    }

    /**
     * Get error type facts (G09-G10).
     */
    protected function getErrorTypeFacts(StudentState $state, int $questionId, bool $isCorrect): array
    {
        if ($isCorrect) {
            return [];
        }

        // Get question type from database
        $question     = $this->questionRepo->find($questionId);
        $questionType = $question?->type ?? 'teori';

        // Syntax questions → G09, Theory/Logic questions → G10
        return $questionType === 'sintaks' ? [AdaptiveConstants::FACT_ERROR_SYNTAX] : [AdaptiveConstants::FACT_ERROR_LOGIC];
    }

    /**
     * Get module fact (G13-G25).
     */
    protected function getModuleFact(int $moduleId): string
    {
        $moduleMap = [
            1 => AdaptiveConstants::FACT_MODULE_FOUNDATION,
            2 => AdaptiveConstants::FACT_MODULE_ENCAPSULATION,
            3 => AdaptiveConstants::FACT_MODULE_INHERITANCE,
            4 => AdaptiveConstants::FACT_MODULE_POLYMORPHISM,
            5 => AdaptiveConstants::FACT_MODULE_ABSTRACTION,
        ];

        return $moduleMap[$moduleId] ?? AdaptiveConstants::FACT_MODULE_FOUNDATION;
    }

    /**
     * Get difficulty fact (G15-G17).
     */
    protected function getDifficultyFact(string $difficulty): string
    {
        $difficultyMap = [
            'beginner' => AdaptiveConstants::FACT_DIFF_BEGINNER,
            'medium'   => AdaptiveConstants::FACT_DIFF_MEDIUM,
            'hard'     => AdaptiveConstants::FACT_DIFF_HARD,
        ];

        return $difficultyMap[$difficulty] ?? AdaptiveConstants::FACT_DIFF_BEGINNER;
    }

    /**
     * Get unlock status facts (G19-G21).
     */
    protected function getUnlockStatusFacts(StudentState $state, int $materialId): array
    {
        $facts = [];

        // Use model navigation logic instead of ID math
        $currentMaterial = Material::find($materialId);
        if (! $currentMaterial) {
            return [AdaptiveConstants::FACT_NEXT_LOCKED];
        }

        $nextMaterial = $currentMaterial->getNextMaterial();
        $prevMaterial = $currentMaterial->getPreviousMaterial();

        $unlockedModules = $state->learning_profile['unlocked_modules'] ?? [];

        // Check if next material's module is locked
        if ($nextMaterial && ! in_array($nextMaterial->module_id, $unlockedModules)) {
            $facts[] = AdaptiveConstants::FACT_NEXT_LOCKED;
        } else {
            $facts[] = AdaptiveConstants::FACT_NEXT_UNLOCKED;
        }

        // Check if previous material's module is unlocked
        if ($prevMaterial && in_array($prevMaterial->module_id, $unlockedModules)) {
            $facts[] = AdaptiveConstants::FACT_PREV_UNLOCKED;
        }

        return $facts;
    }

    /**
     * Check if student has persistent failures (G22).
     */
    protected function isPersistentFail(int $userId, int $questionId): bool
    {
        $consecutiveFails = $this->progressRepo->getConsecutiveFailures($userId, $questionId);

        return $consecutiveFails >= 3;
    }

    /**
     * Check if student has satisfied enough questions in the material (G26).
     */
    protected function hasSatisfactoryProgress(int $userId, int $materialId): bool
    {
        $answeredCount  = $this->progressRepo->getAnsweredQuestionIds($userId, $materialId)->count();
        $totalQuestions = $this->questionRepo->countByMaterial($materialId);

        if ($totalQuestions === 0) {
            return true;
        }

        $percentage = ($answeredCount / $totalQuestions) * 100;

        return $percentage >= 60; // 60% threshold for "almost done" skip logic
    }
}
