<?php

namespace App\Services\Adaptive;

use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Services\AdaptiveEngineServiceInterface;
use App\Contracts\Services\FactGatheringServiceInterface;
use App\Contracts\Services\GamificationServiceInterface;
use App\Contracts\Services\NextActionResolverServiceInterface;
use App\Contracts\Services\PerformanceServiceInterface;
use App\Contracts\Services\QuestionAnswerServiceInterface;
use App\Models\Material;
use App\Models\Question;
use Illuminate\Support\Facades\Cache;

class AdaptiveQuizFlowService
{
    public function __construct(
        protected QuestionAnswerServiceInterface $questionAnswerService,
        protected PerformanceServiceInterface $performanceService,
        protected GamificationServiceInterface $gamificationService,
        protected ProgressRepositoryInterface $progressRepo,
        protected FactGatheringServiceInterface $factGathering,
        protected AdaptiveEngineServiceInterface $adaptiveEngine,
        protected NextActionResolverServiceInterface $nextActionResolver,
    ) {}

    /** @return array<string, mixed> */
    public function processAdaptiveAttempt(Material $material, Question $question, int $userId, array $data): array
    {
        $isCorrect = $this->questionAnswerService->determineCorrectness($question, $data);
        $usedHint  = (bool) ($data['used_hint'] ?? false);
        $timeSpent = (int) ($data['time_spent'] ?? 0);

        // 1. Update student performance
        $studentState = $this->performanceService->updateStudentPerformance($userId, $isCorrect, $timeSpent, $usedHint);

        // 1.5 Update Learning Style based on Real-time Interaction
        $this->performanceService->updateLearningStyleFromInteraction(
            $userId,
            $question->type ?? 'teori',
            $timeSpent,
        );

        // 2. Calculate score with nuance (not just binary 100/0)
        $score = $this->performanceService->calculateScore($isCorrect, $usedHint, $timeSpent, $question->difficulty);

        // 3. Calculate rewards
        $rewardResult = $isCorrect
            ? $this->gamificationService->calculateCorrectAnswerReward($studentState->toArray(), $usedHint, $question->difficulty ?? 'beginner', $timeSpent)
            : $this->gamificationService->processWrongAnswer($studentState->toArray());

        $baseXpEarned = $rewardResult['global_xp_earned'] ?? 0;

        // 3.5-3.7 Apply all gamification rewards (XP, streak bonus, level) atomically
        [$totalXpEarned, $streakBonus] = $this->applyGamificationRewards($studentState, $rewardResult, $isCorrect);

        $studentState->save();

        // 4. Save progress log (MANDATORY BEFORE GATHERING FACTS)
        $answerId     = null;
        $userResponse = null;

        if ($question->question_type === 'multiple_choice' || $question->question_type === 'radio_button') {
            $answerId = $data['answer'] ?? null;
        } elseif ($question->question_type === 'fill_in_the_blank') {
            $userResponse = $data['fill_in_the_blank_answer'] ?? null;
        } elseif ($question->question_type === 'drag_and_drop') {
            $userResponse = $data['drag_and_drop_answers'] ?? null;
        }

        $savedProgress = $this->progressRepo->saveProgress([
            'user_id'       => $userId,
            'material_id'   => $material->id,
            'question_id'   => $question->id,
            'answer_id'     => $answerId,
            'user_response' => $userResponse,
            'is_correct'    => $isCorrect,
            'is_answered'   => true,
            'attributes'    => [
                'score'      => $score,
                'difficulty' => $question->difficulty ?? 'beginner',
                'used_hint'  => $usedHint,
                'time_spent' => $timeSpent,
            ],
        ]);

        // 4.5 Invalidate dashboard caches so progress is visible immediately
        Cache::forget("dashboard_index_{$userId}_false");
        Cache::forget("dashboard_index_{$userId}_true");
        Cache::forget("dashboard_inprogress_{$userId}_false");
        Cache::forget("dashboard_inprogress_{$userId}_true");
        Cache::forget("dashboard_completed_{$userId}_false");
        Cache::forget("dashboard_completed_{$userId}_true");

        // 5. Gather facts for adaptive engine (Uses updated progress)
        $facts = $this->factGathering->gatherFacts(
            studentState: $studentState,
            isCorrect: $isCorrect,
            usedHint: $usedHint,
            score: $score,
            timeSpent: $timeSpent,
            difficulty: $question->difficulty ?? 'beginner',
            questionId: $question->id,
            materialId: $material->id,
            moduleId: $material->module_id ?? null,
        );

        // 6. Evaluate adaptive rules (forward chaining)
        $adaptiveResult = $this->adaptiveEngine->evaluate($facts, $studentState->toArray(), [
            'is_correct'  => $isCorrect,
            'used_hint'   => $usedHint,
            'score'       => $score,
            'time_spent'  => $timeSpent,
            'difficulty'  => $question->difficulty ?? 'beginner',
            'question_id' => $question->id,
            'material_id' => $material->id,
            'module_id'   => $material->module_id ?? null,
        ]);

        // 7. Apply adaptive state changes
        $ruleOutput    = $adaptiveResult['new_state'] ?? [];
        $adaptiveState = $studentState->adaptive_state;
        if (is_string($adaptiveState)) {
            $adaptiveState = json_decode($adaptiveState, true) ?? [];
        }
        $adaptiveState = $adaptiveState ?? [];

        $adaptiveState['current_material_id'] = $material->id;
        $adaptiveState['last_rule']         = $adaptiveResult['triggered_rule'] ?? null;
        $adaptiveState['fast_track_active'] = $ruleOutput['fast_track_active']  ?? ($adaptiveState['fast_track_active'] ?? false);
        if (isset($ruleOutput['target_difficulty'])) {
            $adaptiveState['target_difficulty'] = $ruleOutput['target_difficulty'];
        }
        $adaptiveState['time_metrics']      = [
            'avg_time_per_question' => $this->performanceService->calculateAverageTimeSpent($userId, $material->id),
            'total_time_spent'      => $this->performanceService->calculateTotalTimeSpent($userId, $material->id),
        ];
        $studentState->adaptive_state = $adaptiveState;
        $studentState->save();

        // 8. Resolve next action
        $nextActionData = $this->nextActionResolver->resolve($ruleOutput['next_action'] ?? 'NEXT_QUESTION', $material, $question, $userId);

        // Sync response structure with frontend expectations
        $mappedState = [
            'gamification'     => $studentState->gamification_data,
            'performance'      => $studentState->performance_metrics,
            'learning_profile' => $studentState->learning_profile,
            'adaptive_state'   => $studentState->adaptive_state,
        ];

        // Ensure next action specific messages overwrite the default success message
        $finalMessage = $nextActionData['message'] ?? ($ruleOutput['message'] ?? ($isCorrect ? 'Jawaban benar!' : 'Jawaban salah, coba lagi.'));

        return [
            'status'          => $isCorrect ? 'success' : 'error',
            'message'         => $finalMessage,
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
    private function applyGamificationRewards(\App\Models\StudentState $state, array $rewardResult, bool $isCorrect): array
    {
        $baseXpEarned = $rewardResult['global_xp_earned'] ?? 0;

        // Base XP
        $gamification              = $state->gamification_data ?? [];
        $gamification['global_xp'] = ($gamification['global_xp'] ?? 0) + $baseXpEarned;
        $state->gamification_data  = $gamification;

        // Streak XP bonus
        $streakXpBonus = 0;
        if ($isCorrect) {
            $streakXpBonus = $this->gamificationService->calculateStreakBonusXP($state->current_streak);

            if ($streakXpBonus > 0) {
                $gamification              = $state->gamification_data ?? [];
                $gamification['global_xp'] = ($gamification['global_xp'] ?? 0) + $streakXpBonus;
                $state->gamification_data  = $gamification;
            }
        }

        // Streak milestone rewards (e.g., bonus hints)
        $streakMilestone = $this->gamificationService->checkStreakBonus($state->toArray());
        if ($streakMilestone && isset($streakMilestone['updates'])) {
            $metrics = $state->performance_metrics ?? [];
            foreach ($streakMilestone['updates'] as $key => $value) {
                $metrics[$key] = $value;
            }
            $state->performance_metrics = $metrics;
        }

        // Recalculate level from cumulative XP
        $gamification                  = $state->gamification_data ?? [];
        $gamification['current_level'] = $this->gamificationService->determineLevel($gamification['global_xp'] ?? 0);
        $state->gamification_data      = $gamification;

        return [$baseXpEarned + $streakXpBonus, $streakMilestone];
    }
}
