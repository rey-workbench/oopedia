<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Services\AdaptiveEngineServiceInterface;
use App\Contracts\Services\FactGatheringServiceInterface;
use App\Contracts\Services\MaterialServiceInterface;
use App\Contracts\Services\NextActionResolverServiceInterface;
use App\Contracts\Services\PerformanceServiceInterface;
use App\Contracts\Services\ProgressServiceInterface;
use App\Contracts\Services\QuestionAnswerServiceInterface;
use App\Contracts\Services\QuestionListingServiceInterface;
use App\Contracts\Services\QuestionServiceInterface;
use App\Contracts\Services\QuizRewardServiceInterface;
use App\Contracts\Services\StreakServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;

class MaterialQuestionController extends Controller
{
    public function __construct(
        protected MaterialServiceInterface $materialService,
        protected QuestionServiceInterface $questionService,
        protected QuestionListingServiceInterface $questionListingService,
        protected QuestionAnswerServiceInterface $questionAnswerService,
        protected ProgressServiceInterface $progressService,
        protected QuizRewardServiceInterface $rewardService,
        protected StreakServiceInterface $streakService,
        protected PerformanceServiceInterface $performanceService,
        protected FactGatheringServiceInterface $factGathering,
        protected AdaptiveEngineServiceInterface $adaptiveEngine,
        protected NextActionResolverServiceInterface $nextActionResolver,
    ) {}

    // ==================== HELPER METHODS ====================

    protected function isGuestUser(): bool
    {
        return $this->isGuest();
    }

    protected function getUserId(): string|int
    {
        return $this->isGuestUser() ? Session::getId() : Auth::id();
    }

    /** @return array<string, mixed> */
    protected function getGuestProgress(Request $request): array
    {
        if (! $this->isGuestUser()) {
            return [];
        }

        $guestProgressJson = $request->cookie('guest_progress', '[]');

        return json_decode($guestProgressJson, true) ?? [];
    }

    // ==================== PUBLIC ROUTES ====================

    /**
     * Display list of all materials with progress.
     */
    public function index(Request $request): Response
    {
        $isGuest = $this->isGuestUser();
        $userId = $this->getUserId();
        $guestProgress = $this->getGuestProgress($request);

        $materials = $this->questionListingService->getMaterialsListWithStudentCount($userId, $isGuest, $guestProgress);

        return Inertia::render('Mahasiswa/Materials/Questions/Index', compact('materials', 'isGuest'));
    }

    /**
     * Display questions for a specific material.
     */
    public function show(int|string $materialId, Request $request): Response|RedirectResponse
    {
        $material = $this->materialService->getMaterialById((int) $materialId);
        if (! $material) {
            return redirect()->route('mahasiswa.materials.questions.index')
                ->with('error', 'Material tidak ditemukan');
        }

        $isGuest = $this->isGuestUser();

        // Get difficulty from session, fallback to beginner for all users
        // 'all' is not valid for the adaptive engine which needs a specific difficulty
        $difficulty = session('quiz_difficulty', 'beginner');

        // Clear session after reading to prevent persistence across different materials
        // session()->forget('quiz_difficulty');

        $guestProgress = $this->getGuestProgress($request);
        $userId = $this->getUserId();

        $data = $this->questionListingService->getQuizData($material, $difficulty, $userId, $isGuest, $guestProgress);

        // Fetch student state for detailed stats
        $studentStateData = [];
        if (! $isGuest) {
            $studentState = $this->performanceService->getStudentState($userId);
            if ($studentState) {
                $studentStateData = [
                    'gamification' => $studentState->gamification_data,
                    'performance' => $studentState->performance_metrics,
                    'learning_profile' => $studentState->learning_profile,
                ];
            }
        } else {
            // Mock state for guests from session to ensure continuity
            $studentStateData = [
                'gamification' => [
                    'global_xp' => session('guest_xp', 0),
                    'current_streak' => session('guest_streak', 0),
                    'current_level' => 'Tamu',
                ],
                'performance' => [
                    'hints_available' => 3,
                    'total_questions_answered' => count(session('guest_progress', [])),
                ],
                'learning_profile' => [],
            ];
        }

        return Inertia::render('Mahasiswa/Materials/Questions/Show/Index', array_merge($data, [
            'isGuest' => $isGuest,
            'studentState' => $studentStateData,
        ]));
    }

    /**
     * Display difficulty levels progress for a material.
     */
    public function levels(int|string $materialId, Request $request): Response|RedirectResponse
    {
        $material = $this->materialService->getMaterialById((int) $materialId);
        if (! $material) {
            return redirect()->route('mahasiswa.materials.questions.index')
                ->with('error', 'Material tidak ditemukan');
        }

        $materials = $this->materialService->getAllOrdered();
        $difficulty = 'all';
        $isGuest = $this->isGuestUser();
        $userId = $this->getUserId();
        $guestProgress = $this->getGuestProgress($request);

        $answeredQuestionIds = $isGuest
            ? $this->questionListingService->getGuestAnsweredQuestionIds($material->id, $guestProgress)
            : $this->progressService->getAnsweredQuestionIds($userId, $material->id);

        $levels = $this->questionListingService->getLevelProgress($material, $difficulty, $answeredQuestionIds, $isGuest);

        return Inertia::render('Mahasiswa/Materials/Questions/Levels/Index', compact(
            'material',
            'materials',
            'levels',
            'difficulty',
            'isGuest',
        ));
    }

    /**
     * Display review page for answered questions.
     */
    public function review(int|string $id, Request $request): Response
    {
        $material = $this->materialService->getMaterialWithQuestionsAndAnswers((int) $id);
        $materials = $this->materialService->getAllOrdered();
        $difficulty = $request->query('difficulty', 'all');
        $isGuest = $this->isGuestUser();
        $userId = $this->getUserId();
        $guestProgress = $this->getGuestProgress($request);

        $questions = $this->questionListingService->getReviewQuestions($material, $difficulty, $userId, $isGuest, $guestProgress);

        return Inertia::render('Mahasiswa/Materials/Questions/Review/Index', [
            'material' => $material,
            'materials' => $materials,
            'questions' => $questions,
            'difficulty' => $difficulty,
            'isGuest' => $isGuest,
        ]);
    }

    /**
     * Get attempt count for a specific question.
     */
    public function getAttempts(int|string $materialId, int|string $questionId, Request $request): JsonResponse
    {
        $isGuest = $this->isGuestUser();

        if ($isGuest) {
            $progressKey = $materialId . '_' . $questionId;
            $guestProgress = $this->getGuestProgress($request);
            $attempts = isset($guestProgress[$progressKey]) ? $guestProgress[$progressKey]['attempt_number'] : 0;
        } else {
            $attempts = $this->progressService->getAttemptCount(Auth::id(), (int) $materialId, (int) $questionId);
        }

        return response()->json(['attempts' => $attempts]);
    }

    /**
     * Check answer submission.
     */
    public function checkAnswer(int|string $materialId, int|string $questionId, Request $request): JsonResponse
    {
        $material = $this->materialService->getMaterialById((int) $materialId);
        $question = $this->questionService->getQuestionById((int) $questionId);

        if (! $material || ! $question) {
            return response()->json([
                'status' => 'error',
                'message' => 'Material atau soal tidak ditemukan',
            ], 404);
        }

        $userId = $this->getUserId();
        $isGuest = $this->isGuestUser();

        if (! $isGuest) {
            $result = $this->handleAdaptiveCheck($material, $question, $userId, $request->all());

            return response()->json($result);
        }

        $result = $this->questionAnswerService->checkAnswer(
            array_merge($request->all(), ['material_id' => $materialId]),
            $userId,
            $isGuest,
        );

        return response()->json($result);
    }

    /**
     * Handle the complete adaptive quiz checking flow.
     */
    /** @return array<string, mixed> */
    protected function handleAdaptiveCheck(Material $material, Question $question, int $userId, array $data): array
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
