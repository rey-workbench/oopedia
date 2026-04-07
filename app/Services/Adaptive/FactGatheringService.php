<?php

declare(strict_types=1);

namespace App\Services\Adaptive;

use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Contracts\Services\FactGatheringServiceInterface;
use App\Models\Material;
use App\Models\Question;
use App\Models\StudentState;
use App\Rules\Adaptive\Constants\AdaptiveConstants;
use App\Schemas\StudentStateSchema;

/**
 * FactGatheringService
 *
 * Responsible for gathering facts (G01-G25) from student state and context.
 * Facts are used by adaptive rules for decision making.
 */
final class FactGatheringService implements FactGatheringServiceInterface
{
    public function __construct(
        public readonly ProgressRepositoryInterface $progressRepo,
        public readonly QuestionRepositoryInterface $questionRepo,
    ) {
    }

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
        if ($moduleId && $difficulty !== Question::DIFFICULTY_FINAL) {
            $facts[] = $this->getModuleFact($moduleId);
        }

        // G15-G17: Difficulty Facts
        $facts[] = $this->getDifficultyFact($difficulty);

        // G18: Final Project (check difficulty='final')
        if ($difficulty === Question::DIFFICULTY_FINAL) {
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
        $finalScore = $isCorrect
            ? max($score, StudentStateSchema::SCORE_MIN_CORRECT)
            : min($score, StudentStateSchema::SCORE_MAX_WRONG);

        if ($finalScore < StudentStateSchema::FACT_SCORE_CRITICAL_MAX) {
            return [AdaptiveConstants::FACT_SCORE_CRITICAL];
        }
        if ($finalScore < StudentStateSchema::FACT_SCORE_REMEDIAL_MAX) {
            return [AdaptiveConstants::FACT_SCORE_REMEDIAL];
        }
        if ($finalScore < StudentStateSchema::FACT_SCORE_STANDARD_MAX) {
            return [AdaptiveConstants::FACT_SCORE_STANDARD];
        }

        return [AdaptiveConstants::FACT_SCORE_MASTERY];
    }

    /**
     * Get time-based facts (G05).
     */
    protected function getTimeFacts(int $timeSpent, string $difficulty = Question::DIFFICULTY_BEGINNER): array
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

        if ($style === StudentStateSchema::STYLE_MIXED) {
            return [
                AdaptiveConstants::FACT_STYLE_VISUAL,
                AdaptiveConstants::FACT_STYLE_TEXTUAL,
                AdaptiveConstants::FACT_STYLE_MIXED,
            ];
        }

        return $style === StudentStateSchema::STYLE_VISUAL
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
        return $questionType === Question::TYPE_SINTAKS
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
            Question::DIFFICULTY_BEGINNER => AdaptiveConstants::FACT_DIFF_BEGINNER,
            Question::DIFFICULTY_MEDIUM   => AdaptiveConstants::FACT_DIFF_MEDIUM,
            Question::DIFFICULTY_HARD     => AdaptiveConstants::FACT_DIFF_HARD,
            Question::DIFFICULTY_FINAL    => AdaptiveConstants::FACT_DIFF_HARD, // Mapping final to hard fact
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

        return $consecutiveFails >= StudentStateSchema::THRESHOLD_PERSISTENT_FAIL;
    }

    /**
     * Check if student has satisfied enough questions in the material (G26).
     *
     * Uses weighted progress calculation that gives credit for:
     * - Overall material progress
     * - Difficulty progression (reaching harder levels)
     * - Performance at current difficulty
     */
    protected function hasSatisfactoryProgress(string $userId, string $materialId, string $difficulty = 'all'): bool
    {
        $answeredIds = $this->progressRepo->getAnsweredQuestionIds($userId, $materialId);

        if ($difficulty === 'all' || $difficulty === 'final') {
            $answeredCount  = $answeredIds->count();
            $totalQuestions = $this->questionRepo->countByMaterial($materialId);

            if ($totalQuestions === 0) {
                return true;
            }

            $percentage = ($answeredCount / $totalQuestions) * 100;

            return $percentage >= StudentStateSchema::THRESHOLD_SATISFACTORY_PROGRESS;
        }

        // For specific difficulty: use weighted progress calculation
        $allQuestions     = $this->questionRepo->getByMaterialAndDifficulty($materialId, 'all');
        $answeredIdsArray = $answeredIds->toArray();

        // Count answered questions by difficulty
        $beginnerAnswered = $allQuestions
            ->where('difficulty', Question::DIFFICULTY_BEGINNER)
            ->filter(fn ($q) => in_array($q->id, $answeredIdsArray))
            ->count();

        $mediumAnswered = $allQuestions
            ->where('difficulty', Question::DIFFICULTY_MEDIUM)
            ->filter(fn ($q) => in_array($q->id, $answeredIdsArray))
            ->count();

        $hardAnswered = $allQuestions
            ->where('difficulty', Question::DIFFICULTY_HARD)
            ->filter(fn ($q) => in_array($q->id, $answeredIdsArray))
            ->count();

        // Total questions by difficulty
        $beginnerTotal = $allQuestions->where('difficulty', Question::DIFFICULTY_BEGINNER)->count();
        $mediumTotal   = $allQuestions->where('difficulty', Question::DIFFICULTY_MEDIUM)->count();
        $hardTotal     = $allQuestions->where('difficulty', Question::DIFFICULTY_HARD)->count();

        if ($beginnerTotal + $mediumTotal + $hardTotal === 0) {
            return true;
        }

        // Weighted progress calculation
        $weightedAnswered = ($beginnerAnswered * StudentStateSchema::WEIGHT_PROGRESS_BEGINNER)
            + ($mediumAnswered * StudentStateSchema::WEIGHT_PROGRESS_MEDIUM)
            + ($hardAnswered * StudentStateSchema::WEIGHT_PROGRESS_HARD);

        $weightedTotal = ($beginnerTotal * StudentStateSchema::WEIGHT_PROGRESS_BEGINNER)
            + ($mediumTotal * StudentStateSchema::WEIGHT_PROGRESS_MEDIUM)
            + ($hardTotal * StudentStateSchema::WEIGHT_PROGRESS_HARD);

        $weightedPercentage = ($weightedAnswered / $weightedTotal) * 100;

        // Difficulty progression bonus
        $progressionBonus = 0;
        if ($difficulty === Question::DIFFICULTY_HARD && $hardAnswered > 0) {
            // Student has proven ability at hardest level
            $progressionBonus = StudentStateSchema::BONUS_REACHING_HARD_BASE
                + min(StudentStateSchema::BONUS_MAX_HARD_PROGRESSION, $hardAnswered * StudentStateSchema::BONUS_HARD_QUESTION_ANSWERED);
        } elseif ($difficulty === Question::DIFFICULTY_MEDIUM && $mediumAnswered > 0) {
            // Student reached medium level
            if ($mediumAnswered >= StudentStateSchema::THRESHOLD_MEDIUM_REACHED_COUNT) {
                $progressionBonus = StudentStateSchema::BONUS_REACHING_MEDIUM_STREAK;
            }
        }

        $finalPercentage = min(100, $weightedPercentage + $progressionBonus);

        return $finalPercentage >= StudentStateSchema::THRESHOLD_SATISFACTORY_PROGRESS;
    }
}
