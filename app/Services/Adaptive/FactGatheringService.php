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
    /** Streak berturut-turut benar untuk dianggap "konsisten tinggi". */
    private const CONSISTENCY_STREAK_THRESHOLD = 3;

    /** Streak berturut-turut cepat+benar untuk dianggap "bosan". */
    private const BOREDOM_STREAK_THRESHOLD = 3;

    /** Streak berturut-turut lambat+salah untuk dianggap "cemas". */
    private const ANXIETY_STREAK_THRESHOLD = 2;

    /** Persentase soal terjawab untuk dianggap "hampir selesai". */
    private const MODULE_NEARLY_DONE_PCT = 80;

    /** Akurasi minimum per difficulty untuk dianggap "mastery". */
    private const MASTERY_ACCURACY_THRESHOLD = 70;

    /** Minimum soal dijawab per difficulty untuk evaluasi mastery. */
    private const MASTERY_MIN_ATTEMPTS = 3;

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
            ...$this->getErrorTypeFacts($questionId, $isCorrect),
            ...$this->getConsistencyFacts($studentState),
            ...$this->getMasteryFacts($studentState, $materialId),
            ...$this->getBehaviouralFacts($studentState, $timeSpent, $difficulty, $isCorrect),
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

        if ($this->isModuleNearlyDone($studentState->user_id, $materialId)) {
            $facts[] = FactRegistry::getCode(AdaptiveConstants::FACT_MODULE_NEARLY_DONE);
        }

        if ($this->isEligibleForGraduation($studentState->user_id, $materialId)) {
            $facts[] = FactRegistry::getCode(AdaptiveConstants::FACT_MODULE_GRADUATION);
        }

        // Filter null values (jika fakta tidak ada di DB)
        return array_values(array_unique(array_filter($facts)));
    }

    // ── Score Facts ─────────────────────────────────────────────────────

    protected function getScoreFacts(int $score, bool $isCorrect): array
    {
        if ($isCorrect) {
            if ($score >= 90) {
                return [FactRegistry::getCode(AdaptiveConstants::FACT_SCORE_PERFECT)];
            }

            return [FactRegistry::getCode(AdaptiveConstants::FACT_SCORE_PASS)];
        }

        if ($score <= 0) {
            return [FactRegistry::getCode(AdaptiveConstants::FACT_SCORE_ZERO)];
        }

        return [FactRegistry::getCode(AdaptiveConstants::FACT_SCORE_FAILURE)];
    }

    // ── Time Facts ──────────────────────────────────────────────────────

    protected function getTimeFacts(int $timeSpent, QuestionDifficulty|string $difficulty, bool $isCorrect): array
    {
        $diffKey       = $difficulty instanceof QuestionDifficulty ? $difficulty->value : $difficulty;
        $allocatedTime = AdaptiveConstants::ALLOCATED_TIME[$diffKey] ?? 60;
        $percentage    = ($allocatedTime > 0) ? ($timeSpent / $allocatedTime) * 100 : 100;

        if ($percentage < AdaptiveConstants::TIME_FAST_THRESHOLD) {
            return $isCorrect
                ? [FactRegistry::getCode(AdaptiveConstants::FACT_TIME_FAST_SUCCESS)]
                : [FactRegistry::getCode(AdaptiveConstants::FACT_TIME_FAST_FAIL)];
        }

        // Slow: waktu >= allocated time (100%+)
        if ($percentage >= 100) {
            return $isCorrect
                ? [FactRegistry::getCode(AdaptiveConstants::FACT_TIME_SLOW_SUCCESS)]
                : [FactRegistry::getCode(AdaptiveConstants::FACT_TIME_SLOW_FAIL)];
        }

        return [];
    }

    // ── Learning Style Facts ────────────────────────────────────────────

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

    // ── Error Type Facts ────────────────────────────────────────────────

    protected function getErrorTypeFacts(string $questionId, bool $isCorrect): array
    {
        if ($isCorrect) {
            return [FactRegistry::getCode(AdaptiveConstants::FACT_NO_ERROR)];
        }

        $question     = $this->questionRepo->find($questionId);
        $questionType = $question?->type;

        if ($questionType === ContentCategory::SINTAKS) {
            return [FactRegistry::getCode(AdaptiveConstants::FACT_ERROR_SYNTAX)];
        }

        if ($questionType === ContentCategory::MIXED) {
            return [FactRegistry::getCode(AdaptiveConstants::FACT_ERROR_CONCEPT)];
        }

        return [FactRegistry::getCode(AdaptiveConstants::FACT_ERROR_LOGIC)];
    }

    // ── Consistency Facts ───────────────────────────────────────────────

    /**
     * Konsistensi tinggi: streak benar berturut-turut >= threshold.
     */
    protected function getConsistencyFacts(StudentState $state): array
    {
        $streak = $state->streak ?? 0;

        if ($streak >= self::CONSISTENCY_STREAK_THRESHOLD) {
            return [FactRegistry::getCode(AdaptiveConstants::FACT_CONSISTENCY_HIGH)];
        }

        return [];
    }

    // ── Mastery Facts ───────────────────────────────────────────────────

    /**
     * Mastery per difficulty: jika akurasi di level tertentu >= threshold
     * dan sudah menjawab cukup soal di level tersebut.
     */
    protected function getMasteryFacts(StudentState $state, string $materialId): array
    {
        $facts  = [];
        $userId = $state->user_id;

        $attempts = $this->progressRepo->getByUserAndMaterial($userId, $materialId);

        if ($attempts->isEmpty()) {
            return [];
        }

        $grouped = $attempts->groupBy(fn ($a) => $a->attributes['difficulty'] ?? 'beginner');

        foreach ($grouped as $diffKey => $diffAttempts) {
            if ($diffAttempts->count() < self::MASTERY_MIN_ATTEMPTS) {
                continue;
            }

            $correctCount = $diffAttempts->where('is_correct', true)->count();
            $accuracy     = ($correctCount / $diffAttempts->count()) * 100;

            if ($accuracy < self::MASTERY_ACCURACY_THRESHOLD) {
                continue;
            }

            $factName = match ($diffKey) {
                QuestionDifficulty::BEGINNER->value => AdaptiveConstants::FACT_MASTERY_BEGINNER,
                QuestionDifficulty::MEDIUM->value   => AdaptiveConstants::FACT_MASTERY_MEDIUM,
                QuestionDifficulty::HARD->value,
                QuestionDifficulty::FINAL->value    => AdaptiveConstants::FACT_MASTERY_HARD,
                default                             => null,
            };

            if ($factName) {
                $facts[] = FactRegistry::getCode($factName);
            }
        }

        return $facts;
    }

    // ── Behavioural / Psychological Facts ───────────────────────────────

    /**
     * Deteksi pola perilaku dari state siswa:
     * - Boredom: streak tinggi + jawaban cepat berturut (terlalu mudah)
     * - Anxiety: wrong_streak tinggi + waktu lambat (terlalu sulit)
     * - High Struggle: persistent fail + waktu lambat
     */
    protected function getBehaviouralFacts(
        StudentState $state,
        int $timeSpent,
        QuestionDifficulty|string $difficulty,
        bool $isCorrect,
    ): array {
        $facts = [];

        $diffKey       = $difficulty instanceof QuestionDifficulty ? $difficulty->value : $difficulty;
        $allocatedTime = AdaptiveConstants::ALLOCATED_TIME[$diffKey] ?? 60;
        $timePct       = ($allocatedTime > 0) ? ($timeSpent / $allocatedTime) * 100 : 100;
        $isFast        = $timePct < AdaptiveConstants::TIME_FAST_THRESHOLD;
        $isSlow        = $timePct >= 100;

        $streak      = $state->streak       ?? 0;
        $wrongStreak = $state->wrong_streak ?? 0;

        // Boredom: benar berturut-turut + cepat → terlalu mudah
        if ($isCorrect && $isFast && $streak >= self::BOREDOM_STREAK_THRESHOLD) {
            $facts[] = FactRegistry::getCode(AdaptiveConstants::FACT_BOREDOM_SIGNS);
        }

        // Anxiety: salah berturut-turut + lambat → terlalu sulit
        if (! $isCorrect && $isSlow && $wrongStreak >= self::ANXIETY_STREAK_THRESHOLD) {
            $facts[] = FactRegistry::getCode(AdaptiveConstants::FACT_ANXIETY_SIGNS);
        }

        // High Struggle: salah berturut-turut banyak + lambat
        if (! $isCorrect && $isSlow && $wrongStreak >= StudentStateSchema::THRESHOLD_PERSISTENT_FAIL) {
            $facts[] = FactRegistry::getCode(AdaptiveConstants::FACT_HIGH_STRUGGLE);
        }

        return $facts;
    }

    // ── Difficulty Facts ────────────────────────────────────────────────

    protected function getDifficultyFact(QuestionDifficulty|string $difficulty): ?string
    {
        $diffKey = $difficulty instanceof QuestionDifficulty ? $difficulty->value : $difficulty;

        $name = match ($diffKey) {
            QuestionDifficulty::BEGINNER->value => AdaptiveConstants::FACT_DIFF_BEGINNER,
            QuestionDifficulty::MEDIUM->value   => AdaptiveConstants::FACT_DIFF_MEDIUM,
            QuestionDifficulty::HARD->value     => AdaptiveConstants::FACT_DIFF_HARD,
            QuestionDifficulty::FINAL->value    => AdaptiveConstants::FACT_DIFF_HARD,
            default                             => AdaptiveConstants::FACT_DIFF_BEGINNER,
        };

        return FactRegistry::getCode($name);
    }

    // ── Unlock Status Facts ─────────────────────────────────────────────

    protected function getUnlockStatusFacts(StudentState $state, string $materialId): array
    {
        $facts           = [];
        $currentMaterial = Material::find($materialId);

        if (! $currentMaterial) {
            return [];
        }

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

    // ── Progress & Graduation Checks ────────────────────────────────────

    protected function isPersistentFail(string $userId, string $questionId): bool
    {
        $consecutiveFails = $this->progressRepo->getConsecutiveFailures($userId, $questionId);

        return $consecutiveFails >= StudentStateSchema::THRESHOLD_PERSISTENT_FAIL;
    }

    protected function hasSatisfactoryProgress(string $userId, string $materialId): bool
    {
        $attemptedCount  = $this->progressRepo->getAttemptedQuestionIds($userId, $materialId)->count();
        $totalQuestions  = $this->questionRepo->countByMaterial($materialId);

        if ($totalQuestions === 0) {
            return true;
        }

        $percentage = ($attemptedCount / $totalQuestions) * 100;

        return $percentage >= StudentStateSchema::THRESHOLD_SATISFACTORY_PROGRESS;
    }

    /**
     * Module Nearly Done: siswa sudah menjawab >= 80% soal di materi ini.
     */
    protected function isModuleNearlyDone(string $userId, string $materialId): bool
    {
        $attemptedCount  = $this->progressRepo->getAttemptedQuestionIds($userId, $materialId)->count();
        $totalQuestions  = $this->questionRepo->countByMaterial($materialId);

        if ($totalQuestions === 0) {
            return false;
        }

        $percentage = ($attemptedCount / $totalQuestions) * 100;

        return $percentage >= self::MODULE_NEARLY_DONE_PCT;
    }

    /**
     * Module Graduation: siswa layak lulus modul jika:
     * - Sudah menjawab >= 61% soal (satisfactory progress)
     * - Akurasi keseluruhan >= 70%
     */
    protected function isEligibleForGraduation(string $userId, string $materialId): bool
    {
        if (! $this->hasSatisfactoryProgress($userId, $materialId)) {
            return false;
        }

        $attempts = $this->progressRepo->getByUserAndMaterial($userId, $materialId);

        if ($attempts->isEmpty()) {
            return false;
        }

        $correctCount = $attempts->where('is_correct', true)->count();
        $accuracy     = ($correctCount / $attempts->count()) * 100;

        return $accuracy >= self::MASTERY_ACCURACY_THRESHOLD;
    }

    private function isFinalDifficulty(QuestionDifficulty|string $difficulty): bool
    {
        return ($difficulty instanceof QuestionDifficulty)
            ? $difficulty === QuestionDifficulty::FINAL
            : $difficulty === QuestionDifficulty::FINAL->value;
    }
}
