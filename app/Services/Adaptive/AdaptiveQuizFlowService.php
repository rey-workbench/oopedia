<?php

declare(strict_types=1);

namespace App\Services\Adaptive;

use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\StudentStateRepositoryInterface;
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
        public readonly StudentStateRepositoryInterface $studentStateRepo,
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
            isCorrect: $isCorrect,
            timeSpent: $timeSpent,
            usedHint: $usedHint,
            questionType: $question->type ?? ContentCategory::TEORI,
        );

        $score = $this->performanceService->calculateScore($isCorrect, $usedHint, $timeSpent, $difficulty);

        $rewardData = $this->gamificationService->applySubmissionRewards(
            $userId,
            $isCorrect,
            $difficulty,
            $timeSpent,
            $usedHint,
        );

        $totalXpEarned = $rewardData['xp_reward'] ?? 0;

        // Fetch streak bonus specifically
        $rewardedState = $this->performanceService->getStudentState($userId);
        $streakBonus   = $this->gamificationService->checkStreakBonus($rewardedState->toArray());

        // Sync state after rewards
        $studentState = $this->performanceService->getStudentState($userId);

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

        // Save state directly from engine results - Repository will filter transient keys
        $this->saveStudentState($studentState->fill($ruleOutput), $isGuest, $userId);

        $nextActionData = $this->nextActionResolver->resolve(
            $adaptiveResult['triggered_rule']['action'] ?? 'H01',
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
                    'fast_track_active' => $ruleOutput['fast_track_active']    ?? false,
                ]),
            ],
        ];
    }

    private function resolveStudentState(
        string $userId,
        bool $isCorrect,
        int $timeSpent,
        bool $usedHint,
        ContentCategory $questionType,
    ): StudentState {
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
        QuestionDifficulty $difficulty,
        array $data,
    ): void {
        if ($isGuest) {
            $data['material_id'] = $material->id;
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
                'difficulty' => $difficulty->value,
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

    // Deprecated: State management now handled by AdaptiveEngineService and Rule parameters.

    private function saveStudentState(StudentState $studentState, bool $isGuest, string $userId): void
    {
        if ($isGuest) {
            $this->guestProgressService->saveStudentState($studentState);

            return;
        }

        // Persist all flat columns from the engine-updated model
        $this->studentStateRepo->update($userId, array_merge(
            $studentState->getDirty(),
            ['last_active_at' => now()],
        ));
    }

    /** @return array<string, mixed> */
    private function mapStudentStatePayload(StudentState $studentState): array
    {
        return [
            'xp'                => $studentState->xp,
            'level'             => $studentState->level,
            'streak'            => $studentState->streak,
            'correct_count'     => $studentState->correct_count,
            'total_answered'    => $studentState->total_answered,
            'learning_style'    => $studentState->learning_style,
            'target_difficulty' => $studentState->target_difficulty,
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
        QuestionDifficulty $difficulty,
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
}
