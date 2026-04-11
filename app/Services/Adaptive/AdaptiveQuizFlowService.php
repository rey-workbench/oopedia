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
        public readonly GuestProgressServiceInterface $guestProgressService,
    ) {
    }

    /** @return array<string, mixed> */
    public function processAdaptiveAttempt(Material $material, Question $question, string $userId, array $data): array
    {
        $isCorrect = $this->questionAnswerService->determineCorrectness($question, $data);
        $usedHint  = (bool) ($data['used_hint'] ?? false);
        $timeSpent = (int) ($data['time_spent'] ?? 0);
        $isGuest   = $userId === 'guest';

        $studentState = null;
        if (! $isGuest) {
            $studentState = $this->performanceService->updateStudentPerformance(
                $userId,
                $isCorrect,
                $timeSpent,
                $usedHint,
            );

            $this->performanceService->updateLearningStyleFromInteraction(
                $userId,
                $question->type ?? Question::TYPE_TEORI,
                $timeSpent,
            );
        } else {
            $studentState = $this->guestProgressService->getStudentState();
        }

        $score = $this->performanceService->calculateScore($isCorrect, $usedHint, $timeSpent, $question->difficulty);

        $rewardResult = $isCorrect
            ? $this->gamificationService->calculateCorrectAnswerReward(
                $studentState->toArray(),
                $usedHint,
                $question->difficulty ?? Question::DIFFICULTY_BEGINNER,
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

        $answerId     = null;
        $userResponse = null;

        if ($question->question_type === Question::QUESTION_TYPE_RADIO_BUTTON) {
            $answerId = $data['answer'] ?? null;
        } elseif ($question->question_type === Question::QUESTION_TYPE_FILL_IN_THE_BLANK) {
            $userResponse = $data['fill_in_the_blank_answer'] ?? null;
        } elseif ($question->question_type === Question::QUESTION_TYPE_DRAG_AND_DROP) {
            $userResponse = $data['drag_and_drop_answers'] ?? null;
        }

        if (! $isGuest) {
            $this->progressRepo->saveProgress([
                'user_id'       => $userId,
                'material_id'   => $material->id,
                'question_id'   => $question->id,
                'answer_id'     => $answerId,
                'user_response' => $userResponse,
                'is_correct'    => $isCorrect,
                'is_answered'   => true,
                'attributes'    => [
                    'score'      => $score,
                    'difficulty' => $question->difficulty ?? Question::DIFFICULTY_BEGINNER,
                    'used_hint'  => $usedHint,
                    'time_spent' => $timeSpent,
                ],
            ]);

            try {
                Cache::forget("dashboard_index_{$userId}_false");
                Cache::forget("dashboard_index_{$userId}_true");
                Cache::forget("dashboard_inprogress_{$userId}_false");
                Cache::forget("dashboard_inprogress_{$userId}_true");
                Cache::forget("dashboard_completed_{$userId}_false");
                Cache::forget("dashboard_completed_{$userId}_true");
            } catch (\Throwable $e) {
                Log::warning('Failed to clear dashboard caches: ' . $e->getMessage());
            }
        } else {
            $this->guestProgressService->saveProgress($data, $isCorrect, $question->id);
        }

        $facts = $this->factGathering->gatherFacts(
            studentState: $studentState,
            isCorrect: $isCorrect,
            usedHint: $usedHint,
            score: $score,
            timeSpent: $timeSpent,
            difficulty: $question->difficulty ?? Question::DIFFICULTY_BEGINNER,
            questionId: $question->id,
            materialId: $material->id,
            moduleId: $material->module_id ?? null,
        );

        $adaptiveResult = $this->adaptiveEngine->evaluate($facts, $studentState->toArray(), [
            'is_correct'  => $isCorrect,
            'used_hint'   => $usedHint,
            'score'       => $score,
            'time_spent'  => $timeSpent,
            'difficulty'  => $question->difficulty ?? Question::DIFFICULTY_BEGINNER,
            'question_id' => $question->id,
            'material_id' => $material->id,
            'module_id'   => $material->module_id ?? null,
        ]);

        $ruleOutput    = $adaptiveResult['new_state'] ?? [];
        $adaptiveState = $studentState->adaptive_state;
        if (is_string($adaptiveState)) {
            $adaptiveState = json_decode($adaptiveState, true) ?? [];
        }
        $adaptiveState = $adaptiveState ?? [];

        if (isset($ruleOutput['adaptive_state'])) {
            $adaptiveState = array_merge($adaptiveState, $ruleOutput['adaptive_state']);
        }

        $adaptiveState['current_material_id'] = $material->id;
        $adaptiveState['last_rule']           = $adaptiveResult['triggered_rule'] ?? null;
        $adaptiveState['fast_track_active']   = $ruleOutput['fast_track_active']
            ?? ($adaptiveState['fast_track_active'] ?? false);

        if (isset($ruleOutput['target_difficulty'])) {
            $adaptiveState['target_difficulty'] = $ruleOutput['target_difficulty'];
        }

        $adaptiveState['time_metrics']      = [
            'avg_time_per_question' => (! $isGuest)
                ? $this->performanceService->calculateAverageTimeSpent($userId, $material->id)
                : 0,
            'total_time_spent'      => (! $isGuest)
                ? $this->performanceService->calculateTotalTimeSpent($userId, $material->id)
                : 0,
        ];

        if (isset($ruleOutput['learning_profile'])) {
            $studentState->learning_profile = $ruleOutput['learning_profile'];
        }

        $studentState->adaptive_state = $adaptiveState;

        if ($isGuest) {
            $this->guestProgressService->saveStudentState($studentState);
        } else {
            $studentState->save();
        }

        $nextActionData = $this->nextActionResolver->resolve(
            $ruleOutput['next_action'] ?? 'NEXT_QUESTION',
            $material,
            $question,
            $userId,
        );

        $mappedState = [
            'gamification'     => $studentState->gamification_data,
            'performance'      => $studentState->performance_metrics,
            'learning_profile' => $studentState->learning_profile,
            'adaptive_state'   => $studentState->adaptive_state,
        ];

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
}
