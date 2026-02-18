<?php

namespace App\Services\Adaptive;

use App\Contracts\Services\AdaptiveEngineServiceInterface;
use App\Contracts\Services\FactGatheringServiceInterface;
use App\Contracts\Services\NextActionResolverServiceInterface;
use App\Contracts\Services\PerformanceServiceInterface;
use App\Contracts\Services\ProgressServiceInterface;
use App\Contracts\Services\QuestionAnswerServiceInterface;
use App\Contracts\Services\QuizRewardServiceInterface;
use App\Contracts\Services\StreakServiceInterface;
use App\Models\Material;
use App\Models\Question;
use App\Services\Gamification\LevelingService;

class AdaptiveQuizFlowService
{
    public function __construct(
        protected QuestionAnswerServiceInterface $questionAnswerService,
        protected PerformanceServiceInterface $performanceService,
        protected QuizRewardServiceInterface $rewardService,
        protected StreakServiceInterface $streakService,
        protected ProgressServiceInterface $progressService,
        protected FactGatheringServiceInterface $factGathering,
        protected AdaptiveEngineServiceInterface $adaptiveEngine,
        protected NextActionResolverServiceInterface $nextActionResolver,
        protected LevelingService $levelingService,
    ) {}

    /** @return array<string, mixed> */
    public function processAdaptiveAttempt(Material $material, Question $question, int $userId, array $data): array
    {
        $isCorrect = $this->questionAnswerService->determineCorrectness($question, $data);
        $usedHint = (bool) ($data['used_hint'] ?? false);
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
            ? $this->rewardService->calculateCorrectAnswerReward($studentState->toArray(), $usedHint, $question->difficulty ?? 'beginner', $timeSpent)
            : $this->rewardService->processWrongAnswer($studentState->toArray());

        $baseXpEarned = $rewardResult['global_xp_earned'] ?? 0;

        // Apply base rewards
        $gamification = $studentState->gamification_data ?? [];
        $gamification['global_xp'] = ($gamification['global_xp'] ?? 0) + $baseXpEarned;
        $studentState->gamification_data = $gamification;

        // 3.5 Calculate and apply additional streak XP bonus
        $streakXpBonus = 0;
        if ($isCorrect) {
            $streakXpBonus = $this->streakService->calculateStreakBonusXP($studentState->current_streak);

            if ($streakXpBonus > 0) {
                $gamification = $studentState->gamification_data ?? [];
                $gamification['global_xp'] = ($gamification['global_xp'] ?? 0) + $streakXpBonus;
                $studentState->gamification_data = $gamification;
            }
        }

        $totalXpEarned = $baseXpEarned + $streakXpBonus;

        // 3.6 Check and apply streak milestones (like hint bonuses)
        $streakBonus = $this->streakService->checkStreakBonus($studentState->toArray());
        if ($streakBonus && isset($streakBonus['updates'])) {
            // Apply updates (e.g., hints_available)
            $metrics = $studentState->performance_metrics ?? [];
            foreach ($streakBonus['updates'] as $key => $value) {
                $metrics[$key] = $value;
            }
            $studentState->performance_metrics = $metrics;
        }

        // 3.7 Recalculate Level based on new XP
        $currentXp = $studentState->gamification_data['global_xp'] ?? 0;
        $newLevel = $this->levelingService->determineLevel($currentXp);
        
        $gamification = $studentState->gamification_data ?? [];
        $gamification['current_level'] = $newLevel;
        $studentState->gamification_data = $gamification;

        $studentState->save();

        // 4. Save progress log (MANDATORY BEFORE GATHERING FACTS)
        $answerId = null;
        $userResponse = null;

        if ($question->question_type === 'multiple_choice' || $question->question_type === 'radio_button') {
            $answerId = $data['answer'] ?? null;
        } elseif ($question->question_type === 'fill_in_the_blank') {
            $userResponse = $data['fill_in_the_blank_answer'] ?? null;
        } elseif ($question->question_type === 'drag_and_drop') {
            $userResponse = $data['drag_and_drop_answers'] ?? null;
        }

        $savedProgress = $this->progressService->saveProgress([
            'user_id' => $userId,
            'material_id' => $material->id,
            'question_id' => $question->id,
            'answer_id' => $answerId,
            'user_response' => $userResponse,
            'is_correct' => $isCorrect,
            'is_answered' => true,
            'attributes' => [
                'score' => $score,
                'difficulty' => $question->difficulty ?? 'beginner',
                'used_hint' => $usedHint,
                'time_spent' => $timeSpent,
            ],
        ]);

        if ($timeSpent > 0) {
            $savedProgress?->setTimeSpent($timeSpent);
            $savedProgress?->save();
        }

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
            'is_correct' => $isCorrect,
            'used_hint' => $usedHint,
            'score' => $score,
            'time_spent' => $timeSpent,
            'difficulty' => $question->difficulty ?? 'beginner',
            'question_id' => $question->id,
            'material_id' => $material->id,
            'module_id' => $material->module_id ?? null,
        ]);

        // 7. Apply adaptive state changes
        $ruleOutput = $adaptiveResult['new_state'] ?? [];
        $adaptiveState = $studentState->adaptive_state;
        if (is_string($adaptiveState)) {
            $adaptiveState = json_decode($adaptiveState, true) ?? [];
        }
        $adaptiveState = $adaptiveState ?? [];

        $adaptiveState['last_rule'] = $adaptiveResult['triggered_rule'] ?? null;
        $adaptiveState['last_action'] = $ruleOutput['next_action'] ?? 'NEXT_QUESTION';
        $adaptiveState['time_metrics'] = [
            'avg_time_per_question' => $this->performanceService->calculateAverageTimeSpent($userId, $material->id),
            'total_time_spent' => $this->performanceService->calculateTotalTimeSpent($userId, $material->id),
        ];
        $studentState->adaptive_state = $adaptiveState;
        $studentState->save();

        // 8. Resolve next action
        $nextActionData = $this->nextActionResolver->resolve($ruleOutput['next_action'] ?? 'NEXT_QUESTION', $material, $question, $userId);

        // Sync response structure with frontend expectations
        $mappedState = [
            'gamification' => $studentState->gamification_data,
            'performance' => $studentState->performance_metrics,
            'learning_profile' => $studentState->learning_profile,
            'adaptive_state' => $studentState->adaptive_state,
        ];

        return [
            'status' => $isCorrect ? 'success' : 'error',
            'message' => $ruleOutput['message'] ?? ($isCorrect ? 'Jawaban benar!' : 'Jawaban salah, coba lagi.'),
            'score' => $score,
            'hasNextQuestion' => true,
            'nextUrl' => $nextActionData['url'],
            'adaptiveResult' => [
                'triggered_rule' => $adaptiveResult['triggered_rule'],
                'facts' => $adaptiveResult['facts'],
                'global_xp_earned' => $totalXpEarned,
                'streak_bonus' => $streakBonus ? $streakBonus['message'] : null,
                'new_state' => array_merge($mappedState, [
                    'recommendation' => $ruleOutput['recommendation'] ?? null,
                    'next_action' => $nextActionData['label'],
                    'next_action_data' => $nextActionData,
                    'message' => $ruleOutput['message'] ?? null,
                    'certification' => $ruleOutput['certification'] ?? null,
                    'intervention_type' => $ruleOutput['intervention_type'] ?? null,
                ]),
            ],
        ];
    }
}
