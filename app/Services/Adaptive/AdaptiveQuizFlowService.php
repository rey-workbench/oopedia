<?php

declare(strict_types=1);

namespace App\Services\Adaptive;

use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Services\AdaptiveEngineServiceInterface;
use App\Contracts\Services\AdaptiveQuizFlowServiceInterface;
use App\Contracts\Services\FactGatheringServiceInterface;
use App\Contracts\Services\GamificationServiceInterface;
use App\Contracts\Services\GuestProgressServiceInterface;
use App\Contracts\Services\NextActionResolverServiceInterface;
use App\Contracts\Services\PerformanceServiceInterface;
use App\Contracts\Services\QuestionAnswerServiceInterface;
use App\Enums\Lms\ContentCategory;
use App\Enums\Lms\QuestionDifficulty;
use App\Enums\Lms\QuestionType;
use App\Models\Material;
use App\Models\Question;
use App\Models\StudentState;
use App\Rules\Adaptive\Constants\AdaptiveConstants;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

final class AdaptiveQuizFlowService implements AdaptiveQuizFlowServiceInterface
{
    public function __construct(
        public readonly QuestionAnswerServiceInterface $questionAnswerService,
        public readonly PerformanceServiceInterface $performanceService,
        public readonly GamificationServiceInterface $gamificationService,
        public readonly ProgressRepositoryInterface $progressRepo,
        public readonly FactGatheringServiceInterface $factGathering,
        public readonly AdaptiveEngineServiceInterface $adaptiveEngine,
        public readonly NextActionResolverServiceInterface $nextActionResolver,
        public readonly GuestProgressServiceInterface $guestProgressService,
    ) {}

    /** @return array<string, mixed> */
    public function processAdaptiveAttemptByIds(
        string $materialId,
        string $questionId,
        string $userId,
        array $data,
    ): array {
        $material = Material::find($materialId);
        $question = Question::find($questionId);

        if (! $material || ! $question) {
            return [
                'status'      => 'error',
                'message'     => 'Material atau soal tidak ditemukan',
                'status_code' => 404,
            ];
        }

        return $this->processAdaptiveAttempt($material, $question, $userId, $data);
    }

    /** @return array<string, mixed> */
    public function processAdaptiveAttempt(Material $material, Question $question, string $userId, array $data): array
    {
        $isCorrect  = $this->questionAnswerService->determineCorrectness($question, $data);
        $usedHint   = (bool) ($data['used_hint'] ?? false);
        $timeSpent  = (int) ($data['time_spent'] ?? 0);
        $isGuest    = $userId === 'guest';
        $difficulty = $question->difficulty ?? QuestionDifficulty::BEGINNER;

        $studentState = $this->resolveStudentState(
            userId: $userId,
            isGuest: $isGuest,
            isCorrect: $isCorrect,
            timeSpent: $timeSpent,
            usedHint: $usedHint,
            questionType: $question->type ?? ContentCategory::TEORI,
        );

        $score = $this->performanceService->calculateScore($isCorrect, $usedHint, $timeSpent, $difficulty);

        $rewardResult = $isCorrect
            ? $this->gamificationService->calculateCorrectAnswerReward(
                $studentState->toArray(),
                $usedHint,
                $difficulty,
                $timeSpent,
            )
            : $this->gamificationService->processWrongAnswer($studentState->toArray());

        [$totalXpEarned, $streakBonus] = $this->applyGamificationRewards($studentState, $rewardResult, $isCorrect);

        if (! $isGuest) {
            $studentState->save();
        } else {
            $this->guestProgressService->saveGamificationState(
                $studentState->global_xp,
                $studentState->current_streak,
            );
        }

        $this->persistAttemptData(
            question: $question,
            material: $material,
            userId: $userId,
            isGuest: $isGuest,
            isCorrect: $isCorrect,
            usedHint: $usedHint,
            timeSpent: $timeSpent,
            score: $score,
            difficulty: $difficulty,
            data: $data,
        );

        $adaptiveResult = $this->evaluateAdaptiveRuleSet(
            studentState: $studentState,
            isCorrect: $isCorrect,
            usedHint: $usedHint,
            score: $score,
            timeSpent: $timeSpent,
            difficulty: $difficulty,
            questionId: $question->id,
            materialId: $material->id,
            moduleId: $material->module_id ?? null,
        );

        $ruleOutput = $adaptiveResult['new_state'] ?? [];

        if (isset($ruleOutput['learning_profile'])) {
            $studentState->learning_profile = $ruleOutput['learning_profile'];
        }

        $this->persistCertification(
            $studentState,
            (string) $material->id,
            $ruleOutput['certification'] ?? null,
        );

        $this->mergeAdaptiveBadges($studentState, $ruleOutput);

        $adaptiveState = $this->buildAdaptiveState(
            studentState: $studentState,
            ruleOutput: $ruleOutput,
            adaptiveResult: $adaptiveResult,
            material: $material,
            userId: $userId,
            isGuest: $isGuest,
        );

        $studentState->adaptive_state = $adaptiveState;

        $this->saveStudentState($studentState, $isGuest);

        $nextActionData = $this->nextActionResolver->resolve(
            $ruleOutput['next_action'] ?? 'NEXT_QUESTION',
            $material,
            $question,
            $userId,
        );

        $mappedState = $this->mapStudentStatePayload($studentState);

        return [
            'status'          => $isCorrect ? 'success' : 'error',
            'message'         => $ruleOutput['message']
                ?? ($isCorrect ? 'Jawaban benar!' : 'Jawaban salah, coba lagi.'),
            'score'           => $score,
            'hasNextQuestion' => true,
            'nextUrl'         => $nextActionData['url'],
            'adaptiveResult'  => [
                'triggered_rule'   => $adaptiveResult['triggered_rule'],
                'facts'            => $adaptiveResult['facts'],
                'global_xp_earned' => $totalXpEarned,
                'streak_bonus'     => $streakBonus ? $streakBonus['message'] : null,
                'new_state'        => array_merge($mappedState, [
                    'recommendation'    => $ruleOutput['recommendation'] ?? null,
                    'next_action'       => $nextActionData['label'],
                    'next_action_data'  => $nextActionData,
                    'message'           => $ruleOutput['message']              ?? null,
                    'certification'     => $ruleOutput['certification']        ?? null,
                    'intervention_type' => $ruleOutput['intervention_type']    ?? null,
                    'recovery_type'     => $ruleOutput['recovery_type']        ?? null,
                    'fast_track_active' => $adaptiveState['fast_track_active'] ?? false,
                ]),
            ],
        ];
    }

    private function resolveStudentState(
        string $userId,
        bool $isGuest,
        bool $isCorrect,
        int $timeSpent,
        bool $usedHint,
        ContentCategory $questionType,
    ): StudentState {
        if ($isGuest) {
            return $this->guestProgressService->getStudentState();
        }

        $studentState = $this->performanceService->updateStudentPerformance(
            $userId,
            $isCorrect,
            $timeSpent,
            $usedHint,
        );

        $this->performanceService->updateLearningStyleFromInteraction($userId, $questionType, $timeSpent);

        return $studentState;
    }

    /** @param array<string, mixed> $data */
    private function persistAttemptData(
        Question $question,
        Material $material,
        string $userId,
        bool $isGuest,
        bool $isCorrect,
        bool $usedHint,
        int $timeSpent,
        int $score,
        QuestionDifficulty|string $difficulty,
        array $data,
    ): void {
        if ($isGuest) {
            $this->guestProgressService->saveProgress($data, $isCorrect, $question->id);

            return;
        }

        $answerPayload = $this->extractAnswerPayload($question, $data);

        $this->progressRepo->saveProgress([
            'user_id'       => $userId,
            'material_id'   => $material->id,
            'question_id'   => $question->id,
            'answer_id'     => $answerPayload['answer_id'],
            'user_response' => $answerPayload['user_response'],
            'is_correct'    => $isCorrect,
            'is_answered'   => true,
            'attributes'    => [
                'score'      => $score,
                'difficulty' => $difficulty,
                'used_hint'  => $usedHint,
                'time_spent' => $timeSpent,
            ],
        ]);

        $this->clearDashboardCaches($userId);
    }

    /**
     * @param array<string, mixed> $data
     * @return array{answer_id: mixed, user_response: mixed}
     */
    private function extractAnswerPayload(Question $question, array $data): array
    {
        if ($question->question_type === QuestionType::RADIO_BUTTON) {
            return [
                'answer_id'     => $data['answer'] ?? null,
                'user_response' => null,
            ];
        }

        if ($question->question_type === QuestionType::FILL_IN_THE_BLANK) {
            return [
                'answer_id'     => null,
                'user_response' => $data['fill_in_the_blank_answer'] ?? null,
            ];
        }

        if ($question->question_type === QuestionType::DRAG_AND_DROP) {
            return [
                'answer_id'     => null,
                'user_response' => $data['drag_and_drop_answers'] ?? null,
            ];
        }

        return [
            'answer_id'     => null,
            'user_response' => null,
        ];
    }

    private function clearDashboardCaches(string $userId): void
    {
        try {
            Cache::forget("dashboard_index_{$userId}_false");
            Cache::forget("dashboard_index_{$userId}_true");
            Cache::forget("dashboard_inprogress_{$userId}_false");
            Cache::forget("dashboard_inprogress_{$userId}_true");
            Cache::forget("dashboard_completed_{$userId}_false");
            Cache::forget("dashboard_completed_{$userId}_true");
        } catch (\Throwable $throwable) {
            Log::warning('Failed to clear dashboard caches: ' . $throwable->getMessage());
        }
    }

    /**
     * @param array<string, mixed> $ruleOutput
     * @param array<string, mixed> $adaptiveResult
     * @return array<string, mixed>
     */
    private function buildAdaptiveState(
        StudentState $studentState,
        array $ruleOutput,
        array $adaptiveResult,
        Material $material,
        string $userId,
        bool $isGuest,
    ): array {
        $adaptiveState = $studentState->adaptive_state;

        if (is_string($adaptiveState)) {
            $adaptiveState = json_decode($adaptiveState, true) ?? [];
        }

        $adaptiveState = is_array($adaptiveState) ? $adaptiveState : [];

        if (isset($ruleOutput[AdaptiveConstants::ADAPTIVE_STATE]) && is_array($ruleOutput[AdaptiveConstants::ADAPTIVE_STATE])) {
            $adaptiveState = array_merge($adaptiveState, $ruleOutput[AdaptiveConstants::ADAPTIVE_STATE]);
        }

        $adaptiveState['current_material_id'] = $material->id;
        $adaptiveState['last_rule']           = $adaptiveResult['triggered_rule'] ?? null;
        $adaptiveState['fast_track_active']   = $ruleOutput[AdaptiveConstants::FAST_TRACK_ACTIVE]
            ?? ($adaptiveState['fast_track_active'] ?? false);

        if (isset($ruleOutput[AdaptiveConstants::TARGET_DIFFICULTY])) {
            $adaptiveState['target_difficulty'] = $ruleOutput[AdaptiveConstants::TARGET_DIFFICULTY];
        }

        $adaptiveState['time_metrics'] = [
            'avg_time_per_question' => $isGuest
                ? 0
                : $this->performanceService->calculateAverageTimeSpent($userId, $material->id),
            'total_time_spent'      => $isGuest
                ? 0
                : $this->performanceService->calculateTotalTimeSpent($userId, $material->id),
        ];

        return $adaptiveState;
    }

    private function saveStudentState(StudentState $studentState, bool $isGuest): void
    {
        if ($isGuest) {
            $this->guestProgressService->saveStudentState($studentState);

            return;
        }

        $studentState->save();
    }

    /** @return array<string, mixed> */
    private function mapStudentStatePayload(StudentState $studentState): array
    {
        return [
            'gamification'     => $studentState->gamification_data,
            'performance'      => $studentState->performance_metrics,
            'learning_profile' => $studentState->learning_profile,
            'adaptive_state'   => $studentState->adaptive_state,
        ];
    }

    /**
     * Build facts and execute the registered adaptive rule set in one place.
     *
     * @return array<string, mixed>
     */
    private function evaluateAdaptiveRuleSet(
        StudentState $studentState,
        bool $isCorrect,
        bool $usedHint,
        int $score,
        int $timeSpent,
        QuestionDifficulty|string $difficulty,
        string $questionId,
        string $materialId,
        ?string $moduleId,
    ): array {
        $facts = $this->factGathering->gatherFacts(
            studentState: $studentState,
            isCorrect: $isCorrect,
            usedHint: $usedHint,
            score: $score,
            timeSpent: $timeSpent,
            difficulty: $difficulty,
            questionId: $questionId,
            materialId: $materialId,
            moduleId: $moduleId,
        );

        return $this->adaptiveEngine->evaluate($facts, $studentState->toArray(), [
            'is_correct'  => $isCorrect,
            'used_hint'   => $usedHint,
            'score'       => $score,
            'time_spent'  => $timeSpent,
            'difficulty'  => $difficulty,
            'question_id' => $questionId,
            'material_id' => $materialId,
            'module_id'   => $moduleId,
        ]);
    }

    /**
     * Apply all gamification rewards to StudentState atomically:
     * base XP -> streak XP bonus -> streak milestone hints -> level recalculation.
     *
     * @return array{0: int, 1: array|null} [totalXpEarned, streakMilestoneData]
     */
    private function applyGamificationRewards(StudentState $state, array $rewardResult, bool $isCorrect): array
    {
        $baseXpEarned = $rewardResult['global_xp_earned'] ?? 0;

        $gamification              = $state->gamification_data ?? [];
        $gamification['global_xp'] = ($gamification['global_xp'] ?? 0) + $baseXpEarned;
        $state->gamification_data  = $gamification;

        $streakXpBonus = 0;
        if ($isCorrect) {
            $streakXpBonus = $this->gamificationService->calculateStreakBonusXP($state->current_streak);

            if ($streakXpBonus > 0) {
                $gamification              = $state->gamification_data ?? [];
                $gamification['global_xp'] = ($gamification['global_xp'] ?? 0) + $streakXpBonus;
                $state->gamification_data  = $gamification;
            }
        }

        $streakMilestone = $this->gamificationService->checkStreakBonus($state->toArray());
        if ($streakMilestone && isset($streakMilestone['updates'])) {
            $metrics = $state->performance_metrics ?? [];
            foreach ($streakMilestone['updates'] as $key => $value) {
                $metrics[$key] = $value;
            }
            $state->performance_metrics = $metrics;
        }

        $gamification                  = $state->gamification_data ?? [];
        $gamification['current_level'] = $this->gamificationService->determineLevel($gamification['global_xp'] ?? 0);
        $state->gamification_data      = $gamification;

        return [$baseXpEarned + $streakXpBonus, $streakMilestone];
    }

    private function persistCertification(StudentState $studentState, string $materialId, mixed $certification): void
    {
        if (! is_string($certification) || $certification === '' || $materialId === '') {
            return;
        }

        $learningProfile = $studentState->learning_profile    ?? [];
        $certifications  = $learningProfile['certifications'] ?? [];

        if (! is_array($certifications)) {
            $certifications = [];
        }

        $existingCertification = $certifications[$materialId] ?? null;
        $shouldUpdate          = AdaptiveConstants::certificationRank($certification)
            >= AdaptiveConstants::certificationRank(is_string($existingCertification) ? $existingCertification : null);

        if (! $shouldUpdate) {
            return;
        }

        $certifications[$materialId]       = $certification;
        $learningProfile['certifications'] = $certifications;
        $studentState->learning_profile    = $learningProfile;
    }

    private function mergeAdaptiveBadges(StudentState $studentState, array $ruleOutput): void
    {
        $newBadges = $ruleOutput['gamification_data']['badges'] ?? null;

        if (! is_array($newBadges) || $newBadges === []) {
            return;
        }

        $gamificationData = $studentState->gamification_data ?? [];
        $currentBadges    = $gamificationData['badges']      ?? [];

        if (! is_array($currentBadges)) {
            $currentBadges = [];
        }

        $gamificationData['badges']      = array_values(array_unique(array_merge($currentBadges, $newBadges)));
        $studentState->gamification_data = $gamificationData;
    }
}
