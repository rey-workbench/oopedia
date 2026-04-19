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
            ...$this->getTimeFacts($timeSpent, $difficulty),
            ...$this->getLearningStyleFacts($studentState),
            ...$this->getErrorTypeFacts($studentState, $questionId, $isCorrect),
        ];

        $isFinalProject = $this->isFinalDifficulty($difficulty);

        if ($usedHint) {
            $facts[] = AdaptiveConstants::FACT_HINT_USED;
        }

        if ($moduleId && ! $isFinalProject) {
            $facts[] = AdaptiveConstants::FACT_IN_MODULE;
        }

        $facts[] = $this->getDifficultyFact($difficulty);

        if ($isFinalProject) {
            $facts[] = AdaptiveConstants::FACT_IS_FINAL_PROJECT;
        }

        $facts = array_merge($facts, $this->getUnlockStatusFacts($studentState, $materialId));

        if ($this->isPersistentFail($studentState->user_id, $questionId)) {
            $facts[] = AdaptiveConstants::FACT_PERSISTENT_FAIL;
        }

        if ($this->hasSatisfactoryProgress($studentState->user_id, $materialId)) {
            $facts[] = AdaptiveConstants::FACT_SATISFACTORY_PROGRESS;
        }

        // BUG: FACT_IS_PRACTICE (G17) tidak diproduksi - rule yang menggunakan isPractice() tidak akan pernah trigger
        //      Perlu tambahan logika untuk mendeteksi mode practice (latihan vs quiz normal)
        //      Contoh: dari parameter request, question type, atau material type

        return array_values(array_unique($facts));
    }

    /**
     * TODO: Facts yang belum diproduksi (reserved tapi belum diimplementasi):
     * - FACT_NO_ERROR (G10) - digunakan di HasErrorType::hasNoError() tapi tidak pernah diproduksi
     * - FACT_IS_PRACTICE (G17) - digunakan di HasDifficultyLevel::isPractice() tapi tidak pernah diproduksi
     * - FACT_MODULE_STARTED - reserved constant, tidak diproduksi
     * - FACT_COMPLETED_MODULE - reserved constant, tidak diproduksi
     * - FACT_COMPLETED_ALL_MODULES - reserved constant, tidak diproduksi
     * - FACT_HIGH_ENGAGEMENT - reserved constant, tidak diproduksi
     * - FACT_TIME_SLOW - reserved constant, tidak diproduksi
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

    protected function getTimeFacts(int $timeSpent, QuestionDifficulty|string $difficulty = QuestionDifficulty::BEGINNER): array
    {
        $diffKey       = $difficulty instanceof QuestionDifficulty ? $difficulty->value : $difficulty;
        $allocatedTime = AdaptiveConstants::ALLOCATED_TIME[$diffKey] ?? 60;
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
        $questionType = $question?->type;

        return $questionType === ContentCategory::SINTAKS
            ? [AdaptiveConstants::FACT_ERROR_SYNTAX]
            : [AdaptiveConstants::FACT_ERROR_LOGIC];
    }

    protected function getDifficultyFact(QuestionDifficulty|string $difficulty): string
    {
        $diffKey       = $difficulty instanceof QuestionDifficulty ? $difficulty->value : $difficulty;
        $difficultyMap = [
            QuestionDifficulty::BEGINNER->value => AdaptiveConstants::FACT_DIFF_BEGINNER,
            QuestionDifficulty::MEDIUM->value   => AdaptiveConstants::FACT_DIFF_MEDIUM,
            QuestionDifficulty::HARD->value     => AdaptiveConstants::FACT_DIFF_HARD,
            QuestionDifficulty::FINAL->value    => AdaptiveConstants::FACT_DIFF_HARD,
        ];

        return $difficultyMap[$diffKey] ?? AdaptiveConstants::FACT_DIFF_BEGINNER;
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
        $unlockedSet     = array_map('strval', is_array($unlockedModules) ? $unlockedModules : []);

        if ($nextMaterial && in_array((string) $nextMaterial->module_id, $unlockedSet, true)) {
            $facts[] = AdaptiveConstants::FACT_NEXT_UNLOCKED;
        }

        if ($prevMaterial && in_array((string) $prevMaterial->module_id, $unlockedSet, true)) {
            $facts[] = AdaptiveConstants::FACT_PREV_UNLOCKED;
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

        if ($totalQuestions === 0) {
            return true;
        }

        $percentage = ($attemptedCount / $totalQuestions) * 100;

        return $percentage >= StudentStateSchema::THRESHOLD_SATISFACTORY_PROGRESS;
    }

    private function isFinalDifficulty(QuestionDifficulty|string $difficulty): bool
    {
        if ($difficulty instanceof QuestionDifficulty) {
            return $difficulty === QuestionDifficulty::FINAL;
        }

        return $difficulty === QuestionDifficulty::FINAL->value;
    }
}
