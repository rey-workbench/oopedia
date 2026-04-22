<?php

declare(strict_types=1);

namespace App\Services\Adaptive;

use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Contracts\Services\FactGatheringServiceInterface;
use App\Enums\Lms\ContentCategory;
use App\Enums\Lms\QuestionDifficulty;
use App\Models\Material;
use App\Models\StudentState;
use App\Rules\Adaptive\Constants\AdaptiveConstants;
use App\Rules\Adaptive\FactRegistry;
use App\Schemas\StudentStateSchema;

final class FactGatheringService implements FactGatheringServiceInterface
{
    public function __construct(
        public readonly ProgressRepositoryInterface $progressRepo,
        public readonly QuestionRepositoryInterface $questionRepo,
    ) {}

    public function gatherFacts(
        StudentState $studentState,
        bool $isCorrect,
        bool $usedHint,
        int $score,
        int $timeSpent,
        QuestionDifficulty $difficulty,
        string $questionId,
        string $materialId,
        ?string $moduleId = null,
    ): array {
        $facts = [
            ...$this->getScoreFacts($score, $isCorrect),
            ...$this->getTimeFacts($timeSpent, $difficulty, $isCorrect),
            ...$this->getLearningStyleFacts($studentState),
            ...$this->getErrorTypeFacts($studentState, $questionId, $isCorrect),
        ];

        $isFinalProject = $this->isFinalDifficulty($difficulty);

        if ($usedHint) {
            $facts[] = FactRegistry::getCode(AdaptiveConstants::FACT_HINT_USED);
        }

        if ($moduleId && ! $isFinalProject) {
            $facts[] = FactRegistry::getCode(AdaptiveConstants::FACT_IN_MODULE);
        }

        $facts[] = $this->getDifficultyFact($difficulty);

        if ($isFinalProject) {
            $facts[] = FactRegistry::getCode(AdaptiveConstants::FACT_IS_FINAL_PROJECT);
        }

        $facts = array_merge($facts, $this->getUnlockStatusFacts($studentState, $materialId));

        if ($this->isPersistentFail($studentState->user_id, $questionId)) {
            $facts[] = FactRegistry::getCode(AdaptiveConstants::FACT_PERSISTENT_FAIL);
        }

        if ($this->hasSatisfactoryProgress($studentState->user_id, $materialId)) {
            $facts[] = FactRegistry::getCode(AdaptiveConstants::FACT_SATISFACTORY_PROGRESS);
        }

        // Filter null values (jika fakta tidak ada di DB)
        return array_values(array_unique(array_filter($facts)));
    }

    protected function getScoreFacts(int $score, bool $isCorrect): array
    {
        // Fix Logika: Jika benar, evaluasi promosi. Jika salah, evaluasi remedial.
        if ($isCorrect) {
            if ($score >= 90) {
                return [FactRegistry::getCode(AdaptiveConstants::FACT_SCORE_PERFECT)];
            }
            return [FactRegistry::getCode(AdaptiveConstants::FACT_SCORE_PASS)];
        }

        // Jika salah
        if ($score <= 0) {
            return [FactRegistry::getCode(AdaptiveConstants::FACT_SCORE_ZERO)];
        }
        return [FactRegistry::getCode(AdaptiveConstants::FACT_SCORE_FAILURE)];
    }

    protected function getTimeFacts(int $timeSpent, QuestionDifficulty|string $difficulty, bool $isCorrect): array
    {
        $diffKey       = $difficulty instanceof QuestionDifficulty ? $difficulty->value : $difficulty;
        $allocatedTime = AdaptiveConstants::ALLOCATED_TIME[$diffKey] ?? 60;
        $percentage    = ($timeSpent / $allocatedTime) * 100;

        if ($percentage < AdaptiveConstants::TIME_FAST_THRESHOLD) {
            return $isCorrect 
                ? [FactRegistry::getCode(AdaptiveConstants::FACT_TIME_FAST_SUCCESS)]
                : [FactRegistry::getCode(AdaptiveConstants::FACT_TIME_FAST_FAIL)];
        }

        return [];
    }

    protected function getLearningStyleFacts(StudentState $state): array
    {
        $style = $state->learning_style ?? StudentStateSchema::STYLE_VISUAL;

        if ($style === StudentStateSchema::STYLE_MIXED) {
            return [FactRegistry::getCode(AdaptiveConstants::FACT_STYLE_MIXED)];
        }

        return $style === StudentStateSchema::STYLE_VISUAL
            ? [FactRegistry::getCode(AdaptiveConstants::FACT_STYLE_VISUAL)]
            : [FactRegistry::getCode(AdaptiveConstants::FACT_STYLE_TEXTUAL)];
    }

    protected function getErrorTypeFacts(StudentState $state, string $questionId, bool $isCorrect): array
    {
        if ($isCorrect) {
            return [FactRegistry::getCode(AdaptiveConstants::FACT_NO_ERROR)];
        }

        $question     = $this->questionRepo->find($questionId);
        $questionType = $question?->type;

        return $questionType === ContentCategory::SINTAKS
            ? [FactRegistry::getCode(AdaptiveConstants::FACT_ERROR_SYNTAX)]
            : [FactRegistry::getCode(AdaptiveConstants::FACT_ERROR_LOGIC)];
    }

    protected function getDifficultyFact(QuestionDifficulty|string $difficulty): ?string
    {
        $diffKey = $difficulty instanceof QuestionDifficulty ? $difficulty->value : $difficulty;
        
        $name = match($diffKey) {
            QuestionDifficulty::BEGINNER->value => AdaptiveConstants::FACT_DIFF_BEGINNER,
            QuestionDifficulty::MEDIUM->value   => AdaptiveConstants::FACT_DIFF_MEDIUM,
            QuestionDifficulty::HARD->value     => AdaptiveConstants::FACT_DIFF_HARD,
            QuestionDifficulty::FINAL->value    => AdaptiveConstants::FACT_DIFF_HARD,
            default => AdaptiveConstants::FACT_DIFF_BEGINNER
        };

        return FactRegistry::getCode($name);
    }

    protected function getUnlockStatusFacts(StudentState $state, string $materialId): array
    {
        $facts = [];
        $currentMaterial = Material::find($materialId);
        if (! $currentMaterial) return [];

        $nextMaterial = $currentMaterial->getNextMaterial();
        $prevMaterial = $currentMaterial->getPreviousMaterial();

        $unlockedModules = $state->unlocked_modules ?? [];
        $unlockedSet     = array_map('strval', is_array($unlockedModules) ? $unlockedModules : []);

        if ($nextMaterial && in_array((string) $nextMaterial->module_id, $unlockedSet, true)) {
            $facts[] = FactRegistry::getCode(AdaptiveConstants::FACT_NEXT_UNLOCKED);
        }

        if ($prevMaterial && in_array((string) $prevMaterial->module_id, $unlockedSet, true)) {
            $facts[] = FactRegistry::getCode(AdaptiveConstants::FACT_PREV_UNLOCKED);
        }

        return $facts;
    }

    protected function isPersistentFail(string $userId, string $questionId): bool
    {
        $consecutiveFails = $this->progressRepo->getConsecutiveFailures($userId, $questionId);
        return $consecutiveFails >= StudentStateSchema::THRESHOLD_PERSISTENT_FAIL;
    }

    protected function hasSatisfactoryProgress(string $userId, string $materialId): bool
    {
        $attemptedCount = $this->progressRepo->getAttemptedQuestionIds($userId, $materialId)->count();
        $totalQuestions = $this->questionRepo->countByMaterial($materialId);

        if ($totalQuestions === 0) return true;

        $percentage = ($attemptedCount / $totalQuestions) * 100;
        return $percentage >= StudentStateSchema::THRESHOLD_SATISFACTORY_PROGRESS;
    }

    private function isFinalDifficulty(QuestionDifficulty|string $difficulty): bool
    {
        return ($difficulty instanceof QuestionDifficulty) 
            ? $difficulty === QuestionDifficulty::FINAL 
            : $difficulty === QuestionDifficulty::FINAL->value;
    }
}
