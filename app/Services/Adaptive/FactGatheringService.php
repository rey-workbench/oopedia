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
            ...$this->evaluateScore($score, $isCorrect),
            ...$this->evaluateTimeEfficiency($timeSpent, $difficulty, $isCorrect),
            ...$this->determineLearningStyle($studentState),
            ...$this->diagnoseError($questionId, $isCorrect),
            ...$this->checkConsistency($studentState),
            ...$this->evaluateMastery($studentState, $materialId),
            ...$this->detectBehaviouralSigns($studentState, $timeSpent, $difficulty, $isCorrect),
        ];

        $isFinalProject = $this->isFinalDifficulty($difficulty);

        if ($usedHint) {
            $facts[] = FactRegistry::getCode(AC::FACT_HINT_USED);
        } else {
            // Pure Positive Logic: jika tidak pakai hint, ini fakta positif "Bekerja Mandiri"
            $facts[] = FactRegistry::getCode(AC::FACT_INDEPENDENT_WORK);
        }

        if ($moduleId && ! $isFinalProject) {
            $facts[] = FactRegistry::getCode(AC::FACT_IN_MODULE);
        }

        $facts[] = $this->getCurrentDifficulty($difficulty);

        if ($isFinalProject) {
            $facts[] = FactRegistry::getCode(AC::FACT_IS_FINAL_PROJECT);
        }

        $facts = array_merge($facts, $this->checkModuleProgression($studentState, $materialId));

        if ($this->isPersistentFail((string) $studentState->user_id, $questionId)) {
            $facts[] = FactRegistry::getCode(AC::FACT_PERSISTENT_FAIL);
        }

        if ($this->hasSatisfactoryProgress((string) $studentState->user_id, $materialId)) {
            $facts[] = FactRegistry::getCode(AC::FACT_SATISFACTORY_PROGRESS);
        }

        if ($this->isModuleNearlyDone((string) $studentState->user_id, $materialId)) {
            $facts[] = FactRegistry::getCode(AC::FACT_MODULE_NEARLY_DONE);
        }

        if ($this->isEligibleForGraduation((string) $studentState->user_id, $materialId)) {
            $facts[] = FactRegistry::getCode(AC::FACT_MODULE_GRADUATION);
        }

        return array_values(array_unique(array_filter($facts)));
    }

    private function evaluateScore(int $score, bool $isCorrect): array
    {
        if ($isCorrect) {
            return $score >= 90
                ? [FactRegistry::getCode(AC::FACT_SCORE_PERFECT)]
                : [FactRegistry::getCode(AC::FACT_SCORE_PASS)];
        }

        return $score <= 0
            ? [FactRegistry::getCode(AC::FACT_SCORE_ZERO)]
            : [FactRegistry::getCode(AC::FACT_SCORE_FAILURE)];
    }

    private function evaluateTimeEfficiency(int $timeSpent, QuestionDifficulty|string $difficulty, bool $isCorrect): array
    {
        $diffKey       = $difficulty instanceof QuestionDifficulty ? $difficulty->value : $difficulty;
        $allocatedTime = AC::ALLOCATED_TIME[$diffKey] ?? 60;
        $percentage    = ($allocatedTime > 0) ? ($timeSpent / $allocatedTime) * 100 : 100;

        if ($percentage < AC::TIME_FAST_THRESHOLD) {
            return $isCorrect
                ? [FactRegistry::getCode(AC::FACT_TIME_FAST_SUCCESS)]
                : [FactRegistry::getCode(AC::FACT_TIME_FAST_FAIL)];
        }

        if ($percentage >= 100) {
            return $isCorrect
                ? [FactRegistry::getCode(AC::FACT_TIME_SLOW_SUCCESS)]
                : [FactRegistry::getCode(AC::FACT_TIME_SLOW_FAIL)];
        }

        return [];
    }

    private function determineLearningStyle(StudentState $state): array
    {
        $style = $state->learning_style ?? AC::STYLE_VISUAL;

        return match ($style) {
            AC::STYLE_MIXED                   => [FactRegistry::getCode(AC::FACT_STYLE_MIXED)],
            AC::STYLE_TEXTUAL                 => [FactRegistry::getCode(AC::FACT_STYLE_TEXTUAL)],
            default                           => [FactRegistry::getCode(AC::FACT_STYLE_VISUAL)],
        };
    }

    private function diagnoseError(string $questionId, bool $isCorrect): array
    {
        if ($isCorrect) {
            return [FactRegistry::getCode(AC::FACT_NO_ERROR)];
        }

        $question = $this->questionRepo->find($questionId);
        $type     = $question?->type;

        return match ($type) {
            ContentCategory::SINTAKS => [FactRegistry::getCode(AC::FACT_ERROR_SYNTAX)],
            ContentCategory::MIXED   => [FactRegistry::getCode(AC::FACT_ERROR_CONCEPT)],
            default                  => [FactRegistry::getCode(AC::FACT_ERROR_LOGIC)],
        };
    }

    private function checkConsistency(StudentState $state): array
    {
        return ($state->streak ?? 0) >= AC::THRESHOLD_CONSISTENCY_STREAK
            ? [FactRegistry::getCode(AC::FACT_CONSISTENCY_HIGH)]
            : [];
    }

    private function evaluateMastery(StudentState $state, string $materialId): array
    {
        $facts    = [];
        $attempts = $this->progressRepo->getByUserAndMaterial((string) $state->user_id, $materialId);

        if ($attempts->isEmpty()) {
            return [];
        }

        $grouped = $attempts->groupBy(fn ($a) => $a->question->difficulty->value ?? 'beginner');

        foreach ($grouped as $diffKey => $diffAttempts) {
            // Hitung unique questions yang telah dijawab, bukan total attempts
            $uniqueQuestions = $diffAttempts->unique('question_id');
            if ($uniqueQuestions->count() < AC::THRESHOLD_MASTERY_MIN_ATTEMPTS) {
                continue;
            }

            // Hanya ambil attempt TERAKHIR per soal untuk kalkulasi akurasi
            $latestPerQuestion = $diffAttempts
                ->sortByDesc('created_at')
                ->unique('question_id');

            $accuracy = ($latestPerQuestion->where('is_correct', true)->count() / $latestPerQuestion->count()) * 100;
            if ($accuracy < AC::THRESHOLD_MASTERY_ACCURACY) {
                continue;
            }

            $factName = match ($diffKey) {
                QuestionDifficulty::BEGINNER->value => AC::FACT_MASTERY_BEGINNER,
                QuestionDifficulty::MEDIUM->value   => AC::FACT_MASTERY_MEDIUM,
                default                             => AC::FACT_MASTERY_HARD,
            };

            if ($factName) {
                $facts[] = FactRegistry::getCode($factName);
            }
        }

        return $facts;
    }

    private function detectBehaviouralSigns(StudentState $state, int $timeSpent, QuestionDifficulty|string $difficulty, bool $isCorrect): array
    {
        $facts         = [];
        $diffKey       = $difficulty instanceof QuestionDifficulty ? $difficulty->value : $difficulty;
        $allocatedTime = AC::ALLOCATED_TIME[$diffKey] ?? 60;
        $isFast        = (($timeSpent / $allocatedTime) * 100) < AC::TIME_FAST_THRESHOLD;
        $isSlow        = (($timeSpent / $allocatedTime) * 100) >= 100;

        if ($isCorrect && $isFast && ($state->streak ?? 0) >= AC::THRESHOLD_BOREDOM_STREAK) {
            $facts[] = FactRegistry::getCode(AC::FACT_BOREDOM_SIGNS);
        }

        if (! $isCorrect && $isSlow && ($state->wrong_streak ?? 0) >= AC::THRESHOLD_ANXIETY_STREAK) {
            $facts[] = FactRegistry::getCode(AC::FACT_ANXIETY_SIGNS);
        }

        if (! $isCorrect && $isSlow && ($state->wrong_streak ?? 0) >= AC::THRESHOLD_PERSISTENT_FAIL) {
            $facts[] = FactRegistry::getCode(AC::FACT_HIGH_STRUGGLE);
        }

        return $facts;
    }

    private function getCurrentDifficulty(QuestionDifficulty|string $difficulty): ?string
    {
        $diffKey = $difficulty instanceof QuestionDifficulty ? $difficulty->value : $difficulty;
        $name    = match ($diffKey) {
            QuestionDifficulty::MEDIUM->value => AC::FACT_DIFF_MEDIUM,
            QuestionDifficulty::HARD->value, QuestionDifficulty::FINAL->value => AC::FACT_DIFF_HARD,
            default => AC::FACT_DIFF_BEGINNER,
        };

        return FactRegistry::getCode($name);
    }

    private function checkModuleProgression(StudentState $state, string $materialId): array
    {
        $facts    = [];
        $material = Material::find($materialId);
        if (! $material) {
            return [];
        }

        $unlockedSet = array_map('strval', $state->unlocked_modules ?? []);

        $next = $material->getNextMaterial();
        if ($next && in_array((string) $next->module_id, $unlockedSet, true)) {
            $facts[] = FactRegistry::getCode(AC::FACT_NEXT_UNLOCKED);
        } elseif ($next) {
            $facts[] = FactRegistry::getCode(AC::FACT_NEXT_LOCKED);
        }

        $prev = $material->getPreviousMaterial();
        if ($prev && in_array((string) $prev->module_id, $unlockedSet, true)) {
            $facts[] = FactRegistry::getCode(AC::FACT_PREV_UNLOCKED);
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
