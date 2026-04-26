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
use App\Rules\Adaptive\Constants\AdaptiveConstants as AC;
use App\Rules\Adaptive\FactRegistry;

final class FactGatheringService implements FactGatheringServiceInterface
{
    public function __construct(
        protected readonly ProgressRepositoryInterface $progressRepo,
        protected readonly QuestionRepositoryInterface $questionRepo,
    ) {}

    public function gatherFacts(
        StudentState $studentState,
        bool $isCorrect,
        bool $usedHint,
        int $score,
        int $timeSpent,
        QuestionDifficulty|string $difficulty,
        string $questionId,
        string $materialId,
        ?string $moduleId = null,
    ): array {
        $facts = [
            ...$this->evaluatePerformance($score, $isCorrect, $studentState, $materialId),
            ...$this->evaluateEfficiency($timeSpent, $difficulty, $isCorrect),
            ...$this->evaluateEnvironment($studentState, $difficulty, $moduleId),
            ...$this->evaluateBehaviour($studentState, $timeSpent, $difficulty, $isCorrect, $usedHint, $questionId),
            ...$this->evaluateProgression($studentState, $materialId, $questionId),
        ];

        return array_values(array_unique(array_filter($facts)));
    }

    private function evaluatePerformance(int $score, bool $isCorrect, StudentState $studentState, string $materialId): array
    {
        return [
            ...$this->evaluateScore($score, $isCorrect),
            ...$this->checkConsistency($studentState),
            ...$this->evaluateMastery($studentState, $materialId),
        ];
    }

    private function evaluateEfficiency(int $timeSpent, QuestionDifficulty|string $difficulty, bool $isCorrect): array
    {
        return $this->evaluateTimeEfficiency($timeSpent, $difficulty, $isCorrect);
    }

    private function evaluateEnvironment(StudentState $studentState, QuestionDifficulty|string $difficulty, ?string $moduleId): array
    {
        $facts = [
            ...$this->determineLearningStyle($studentState),
            $this->getCurrentDifficulty($difficulty),
        ];

        if ($moduleId && ! $this->isFinalDifficulty($difficulty)) {
            $facts[] = AC::FACT_IN_MODULE;
        }

        if ($this->isFinalDifficulty($difficulty)) {
            $facts[] = AC::FACT_IS_FINAL_PROJECT;
        }

        return $facts;
    }

    private function evaluateBehaviour(StudentState $studentState, int $timeSpent, QuestionDifficulty|string $difficulty, bool $isCorrect, bool $usedHint, string $questionId): array
    {
        $facts = [
            ...$this->diagnoseError($questionId, $isCorrect),
            ...$this->detectBehaviouralSigns($studentState, $timeSpent, $difficulty, $isCorrect),
        ];

        $facts[] = $usedHint ? AC::FACT_HINT_USED : AC::FACT_INDEPENDENT_WORK;

        return $facts;
    }

    private function evaluateProgression(StudentState $studentState, string $materialId, string $questionId): array
    {
        $facts = $this->checkModuleProgression($studentState, $materialId);

        if ($this->isPersistentFail((string) $studentState->user_id, $questionId)) {
            $facts[] = AC::FACT_PERSISTENT_FAIL;
        }

        if ($this->hasSatisfactoryProgress((string) $studentState->user_id, $materialId)) {
            $facts[] = AC::FACT_SATISFACTORY_PROGRESS;
        }

        if ($this->isModuleNearlyDone((string) $studentState->user_id, $materialId)) {
            $facts[] = AC::FACT_MODULE_NEARLY_DONE;
        }

        if ($this->isEligibleForGraduation((string) $studentState->user_id, $materialId)) {
            $facts[] = AC::FACT_MODULE_GRADUATION;
        }

        return $facts;
    }

    private function isPersistentFail(string $userId, string $questionId): bool
    {
        return $this->progressRepo->getConsecutiveFailures($userId, $questionId) >= AC::THRESHOLD_PERSISTENT_FAIL;
    }

    private function hasSatisfactoryProgress(string $userId, string $materialId): bool
    {
        $total = $this->questionRepo->countByMaterial($materialId);
        if ($total === 0) {
            return true;
        }

        return ($this->progressRepo->getAttemptedQuestionIds($userId, $materialId)->count() / $total * 100) >= AC::THRESHOLD_SATISFACTORY_PROGRESS;
    }

    private function isModuleNearlyDone(string $userId, string $materialId): bool
    {
        $total = $this->questionRepo->countByMaterial($materialId);
        if ($total === 0) {
            return false;
        }

        return ($this->progressRepo->getAttemptedQuestionIds($userId, $materialId)->count() / $total * 100) >= AC::THRESHOLD_MODULE_NEARLY_DONE_PCT;
    }

    private function isEligibleForGraduation(string $userId, string $materialId): bool
    {
        if (! $this->hasSatisfactoryProgress($userId, $materialId)) {
            return false;
        }
        $attempts = $this->progressRepo->getByUserAndMaterial($userId, $materialId);
        if ($attempts->isEmpty()) {
            return false;
        }

        return ($attempts->where('is_correct', true)->count() / $attempts->count() * 100) >= AC::THRESHOLD_MASTERY_ACCURACY;
    }

    private function isFinalDifficulty(QuestionDifficulty|string $difficulty): bool
    {
        return ($difficulty instanceof QuestionDifficulty) ? $difficulty === QuestionDifficulty::FINAL : $difficulty === QuestionDifficulty::FINAL->value;
    }
}
