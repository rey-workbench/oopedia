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

        $facts = array_merge($facts, $this->getScoreFacts($score, $isCorrect));

        $facts = array_merge($facts, $this->getTimeFacts($timeSpent, $difficulty));

        $facts = array_merge($facts, $this->getLearningStyleFacts($studentState));

        $facts = array_merge($facts, $this->getErrorTypeFacts($studentState, $questionId, $isCorrect));

        if ($usedHint) {
            $facts[] = AdaptiveConstants::FACT_HINT_USED;
        }

        if ($moduleId && $difficulty !== Question::DIFFICULTY_FINAL) {
            $facts[] = $this->getModuleFact($moduleId);
        }

        $facts[] = $this->getDifficultyFact($difficulty);

        if ($difficulty === Question::DIFFICULTY_FINAL) {
            $facts[] = AdaptiveConstants::FACT_IS_FINAL_PROJECT;
        }

        $facts = array_merge($facts, $this->getUnlockStatusFacts($studentState, $materialId));

        if ($this->isPersistentFail($studentState->user_id, $questionId)) {
            $facts[] = AdaptiveConstants::FACT_PERSISTENT_FAIL;
        }

        if ($this->hasSatisfactoryProgress($studentState->user_id, $materialId, $difficulty)) {
            $facts[] = AdaptiveConstants::FACT_SATISFACTORY_PROGRESS;
        }

        return array_unique($facts);
    }

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

    protected function getTimeFacts(int $timeSpent, string $difficulty = Question::DIFFICULTY_BEGINNER): array
    {
        $allocatedTime = AdaptiveConstants::ALLOCATED_TIME[$difficulty] ?? 60;
        $percentage    = ($timeSpent / $allocatedTime) * 100;

        return $percentage < AdaptiveConstants::TIME_FAST_THRESHOLD
            ? [AdaptiveConstants::FACT_TIME_FAST]
            : [];
    }

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

    protected function getErrorTypeFacts(StudentState $state, string $questionId, bool $isCorrect): array
    {
        if ($isCorrect) {
            return [];
        }

        $question     = $this->questionRepo->find($questionId);
        $questionType = $question?->type ?? 'teori';

        return $questionType === Question::TYPE_SINTAKS
            ? [AdaptiveConstants::FACT_ERROR_SYNTAX]
            : [AdaptiveConstants::FACT_ERROR_LOGIC];
    }

    protected function getModuleFact(string $moduleId): string
    {
        return AdaptiveConstants::FACT_IN_MODULE;
    }

    protected function getDifficultyFact(string $difficulty): string
    {
        $difficultyMap = [
            Question::DIFFICULTY_BEGINNER => AdaptiveConstants::FACT_DIFF_BEGINNER,
            Question::DIFFICULTY_MEDIUM   => AdaptiveConstants::FACT_DIFF_MEDIUM,
            Question::DIFFICULTY_HARD     => AdaptiveConstants::FACT_DIFF_HARD,
            Question::DIFFICULTY_FINAL    => AdaptiveConstants::FACT_DIFF_HARD,
        ];

        return $difficultyMap[$difficulty] ?? AdaptiveConstants::FACT_DIFF_BEGINNER;
    }

    protected function getUnlockStatusFacts(StudentState $state, string $materialId): array
    {
        $facts = [];

        $currentMaterial = Material::find($materialId);
        if (! $currentMaterial) {
            return [];
        }

        $nextMaterial = $currentMaterial->getNextMaterial();
        $prevMaterial = $currentMaterial->getPreviousMaterial();

        $unlockedModules = $state->learning_profile['unlocked_modules'] ?? [];

        if ($nextMaterial && in_array($nextMaterial->module_id, $unlockedModules)) {
            $facts[] = AdaptiveConstants::FACT_NEXT_UNLOCKED;
        }

        if ($prevMaterial && in_array($prevMaterial->module_id, $unlockedModules)) {
            $facts[] = AdaptiveConstants::FACT_PREV_UNLOCKED;
        }

        return $facts;
    }

    protected function isPersistentFail(string $userId, string $questionId): bool
    {
        $consecutiveFails = $this->progressRepo->getConsecutiveFailures($userId, $questionId);

        return $consecutiveFails >= StudentStateSchema::THRESHOLD_PERSISTENT_FAIL;
    }

    protected function hasSatisfactoryProgress(string $userId, string $materialId, string $difficulty = 'all'): bool
    {
        $attemptedCount = $this->progressRepo->getAttemptedQuestionIds($userId, $materialId)->count();
        $totalQuestions = $this->questionRepo->countByMaterial($materialId);

        if ($totalQuestions === 0) {
            return true;
        }

        $percentage = ($attemptedCount / $totalQuestions) * 100;

        return $percentage >= StudentStateSchema::THRESHOLD_SATISFACTORY_PROGRESS;
    }
}
