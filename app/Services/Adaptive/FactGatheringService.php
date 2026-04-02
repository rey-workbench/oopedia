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
    // ==================== SCORE THRESHOLDS (G01-G04) ====================
    private const SCORE_CRITICAL_MAX  = 50;   // < 50  ? G01

    private const SCORE_REMEDIAL_MAX  = 75;   // 50-74 ? G02

    private const SCORE_STANDARD_MAX  = 90;   // 75-89 ? G03
    // = 90  ? G04

    // ==================== TIME THRESHOLDS (G05-G06) ====================
    // Canonical values live in AdaptiveConstants::ALLOCATED_TIME and AdaptiveConstants::TIME_FAST_THRESHOLD

    // ==================== OTHER THRESHOLDS ====================
    /** Consecutive failures on same question before G22 (persistent fail) */
    private const PERSISTENT_FAIL_THRESHOLD = 2;

    /** Minimum % of questions answered to be considered satisfactory (G26) */
    private const SATISFACTORY_PROGRESS_THRESHOLD = 50;

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
        string $questionId,
        string $materialId,
        ?string $moduleId = null,
    ): array {
        $facts = [];

        // G01-G04: Score Facts
        $facts = array_merge($facts, $this->getScoreFacts($score, $isCorrect));

        // G05: Time Facts
        $facts = array_merge($facts, $this->getTimeFacts($timeSpent, $difficulty));

        // G07-G08: Learning Style Facts
        $facts = array_merge($facts, $this->getLearningStyleFacts($studentState));

        // G09-G10: Error Type Facts
        $facts = array_merge($facts, $this->getErrorTypeFacts($studentState, $questionId, $isCorrect));

        // G12: Hint Facts (G11 removed)
        if ($usedHint) {
            $facts[] = AdaptiveConstants::FACT_HINT_USED;
        }

        // G13-G25: Module Facts
        if ($moduleId && $difficulty !== 'final') {
            $facts[] = $this->getModuleFact($moduleId);
        }

        // G15-G17: Difficulty Facts
        $facts[] = $this->getDifficultyFact($difficulty);

        // G18: Final Project (check difficulty='final')
        if ($difficulty === 'final') {
            $facts[] = AdaptiveConstants::FACT_IS_FINAL_PROJECT;
        }

        // G20-G21: Unlock Status Facts (G19 removed)
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

        if ($finalScore < self::SCORE_CRITICAL_MAX) {
            return [AdaptiveConstants::FACT_SCORE_CRITICAL];
        }
        if ($finalScore < self::SCORE_REMEDIAL_MAX) {
            return [AdaptiveConstants::FACT_SCORE_REMEDIAL];
        }
        if ($finalScore < self::SCORE_STANDARD_MAX) {
            return [AdaptiveConstants::FACT_SCORE_STANDARD];
        }

        return [AdaptiveConstants::FACT_SCORE_MASTERY];
    }

    /**
     * Get time-based facts (G05).
     */
    protected function getTimeFacts(int $timeSpent, string $difficulty = 'beginner'): array
    {
        $allocatedTime = AdaptiveConstants::ALLOCATED_TIME[$difficulty] ?? 60;
        $percentage    = ($timeSpent / $allocatedTime) * 100;

        return $percentage < AdaptiveConstants::TIME_FAST_THRESHOLD
            ? [AdaptiveConstants::FACT_TIME_FAST]
            : [];
    }

    /**
     * Get learning style facts (G07-G08, G27).
     * Mixed learners emit G07 + G08 + G27 so existing crisis rules still fire.
     */
    protected function getLearningStyleFacts(StudentState $state): array
    {
        $style = $state->learning_style;

        if ($style === 'mixed') {
            return [
                AdaptiveConstants::FACT_STYLE_VISUAL,
                AdaptiveConstants::FACT_STYLE_TEXTUAL,
                AdaptiveConstants::FACT_STYLE_MIXED,
            ];
        }

        return $style === 'visual'
            ? [AdaptiveConstants::FACT_STYLE_VISUAL]
            : [AdaptiveConstants::FACT_STYLE_TEXTUAL];
    }

    /**
     * Get error type facts (G09-G10).
     */
    protected function getErrorTypeFacts(StudentState $state, string $questionId, bool $isCorrect): array
    {
        if ($isCorrect) {
            return [];
        }

        // Get question type from database
        $question     = $this->questionRepo->find($questionId);
        $questionType = $question?->type ?? 'teori';

        // Syntax questions ? G09, Theory/Logic questions ? G10
        return $questionType === 'sintaks'
            ? [AdaptiveConstants::FACT_ERROR_SYNTAX]
            : [AdaptiveConstants::FACT_ERROR_LOGIC];
    }

    /**
     * Get module fact (G13-G25).
     */
    protected function getModuleFact(string $moduleId): string
    {
        return AdaptiveConstants::FACT_IN_MODULE;
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
            'final'    => AdaptiveConstants::FACT_DIFF_HARD, // Mapping final to hard fact
        ];

        return $difficultyMap[$difficulty] ?? AdaptiveConstants::FACT_DIFF_BEGINNER;
    }

    /**
     * Get unlock status facts (G20-G21).
     */
    protected function getUnlockStatusFacts(StudentState $state, string $materialId): array
    {
        $facts = [];

        // Use model navigation logic instead of ID math
        $currentMaterial = Material::find($materialId);
        if (! $currentMaterial) {
            return [];
        }

        $nextMaterial = $currentMaterial->getNextMaterial();
        $prevMaterial = $currentMaterial->getPreviousMaterial();

        $unlockedModules = $state->learning_profile['unlocked_modules'] ?? [];

        // Check if next material's module is unlocked (G20)
        if ($nextMaterial && in_array($nextMaterial->module_id, $unlockedModules)) {
            $facts[] = AdaptiveConstants::FACT_NEXT_UNLOCKED;
        }

        // Check if previous material's module is unlocked (G21)
        if ($prevMaterial && in_array($prevMaterial->module_id, $unlockedModules)) {
            $facts[] = AdaptiveConstants::FACT_PREV_UNLOCKED;
        }

        return $facts;
    }

    /**
     * Check if student has persistent failures (G22).
     */
    protected function isPersistentFail(string $userId, string $questionId): bool
    {
        $consecutiveFails = $this->progressRepo->getConsecutiveFailures($userId, $questionId);

        return $consecutiveFails >= self::PERSISTENT_FAIL_THRESHOLD;
    }

    /**
     * Check if student has satisfied enough questions in the material (G26).
     */
    protected function hasSatisfactoryProgress(string $userId, string $materialId, string $difficulty = 'all'): bool
    {
        $answeredIds = $this->progressRepo->getAnsweredQuestionIds($userId, $materialId);

        if ($difficulty === 'all' || $difficulty === 'final') {
            $answeredCount  = $answeredIds->count();
            $totalQuestions = $this->questionRepo->countByMaterial($materialId);
        } else {
            $allQuestions        = $this->questionRepo->getByMaterialAndDifficulty($materialId, 'all');
            $difficultyQuestions = $allQuestions->where('difficulty', $difficulty);

            $totalQuestions = $difficultyQuestions->count();
            $answeredCount  = $difficultyQuestions->filter(fn ($q) => $answeredIds->contains($q->id))->count();
        }

        if ($totalQuestions === 0) {
            return true;
        }

        $percentage = ($answeredCount / $totalQuestions) * 100;

        return $percentage >= self::SATISFACTORY_PROGRESS_THRESHOLD;
    }
}
