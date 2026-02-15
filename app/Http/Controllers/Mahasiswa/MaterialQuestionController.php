<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Contracts\Services\MaterialServiceInterface;
use App\Contracts\Services\QuestionServiceInterface;
use App\Contracts\Services\QuestionAnswerServiceInterface;
use App\Contracts\Services\QuestionListingServiceInterface;
use App\Contracts\Services\ProgressServiceInterface;
use App\Contracts\Services\AdaptiveEngineServiceInterface;
use App\Services\Gamification\QuizRewardService;
use App\Services\Gamification\StreakService;
use App\Services\User\PerformanceService;
use App\Services\Adaptive\FactGatheringService;
use App\Services\Adaptive\NextActionResolverService;
use Inertia\Inertia;

class MaterialQuestionController extends Controller
{
    public function __construct(
        protected MaterialServiceInterface $materialService,
        protected QuestionServiceInterface $questionService,
        protected QuestionListingServiceInterface $questionListingService,
        protected QuestionAnswerServiceInterface $questionAnswerService,
        protected ProgressServiceInterface $progressService,
        protected QuizRewardService $rewardService,
        protected StreakService $streakService,
        protected PerformanceService $performanceService,
        protected FactGatheringService $factGathering,
        protected AdaptiveEngineServiceInterface $adaptiveEngine,
        protected NextActionResolverService $nextActionResolver
    ) {}

    // ==================== HELPER METHODS ====================

    protected function isGuestUser(): bool
    {
        return !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);
    }

    protected function getUserId(): string|int
    {
        return $this->isGuestUser() ? session()->getId() : auth()->id();
    }

    protected function getGuestProgress(Request $request): array
    {
        if (!$this->isGuestUser()) {
            return [];
        }
        
        $guestProgressJson = $request->cookie('guest_progress', '[]');
        return json_decode($guestProgressJson, true) ?? [];
    }

    // ==================== PUBLIC ROUTES ====================

    /**
     * Display list of all materials with progress.
     */
    public function index(Request $request)
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
    public function show(int $materialId, Request $request  )
    {
        $material = $this->materialService->getMaterialById($materialId);
        if (!$material) {
            return redirect()->route('mahasiswa.materials.questions.index')
                ->with('error', 'Material tidak ditemukan');
        }
        
        $isGuest = $this->isGuestUser();
        
        // Get difficulty from session, fallback to default based on user type
        $defaultDifficulty = $isGuest ? 'beginner' : 'all';
        $difficulty = session('quiz_difficulty', $defaultDifficulty);
        
        // Clear session after reading to prevent persistence across different materials
        session()->forget('quiz_difficulty');
        
        $guestProgress = $this->getGuestProgress($request);
        $userId = $this->getUserId();

        $data = $this->questionListingService->getQuizData($material, $difficulty, $userId, $isGuest, $guestProgress);

        return Inertia::render('Mahasiswa/Materials/Questions/Show/Index', $data);
    }

    /**
     * Display difficulty levels progress for a material.
     */
    public function levels(int $materialId, Request $request)
    {
        $material = $this->materialService->getMaterialById($materialId);
        if (!$material) {
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

        $levels = $this->questionListingService->getLevelProgress($material, $difficulty, $answeredQuestionIds);

        return Inertia::render('Mahasiswa/Materials/Questions/Levels/Index', compact(
            'material',
            'materials',
            'levels',
            'difficulty',
            'isGuest'
        ));
    }

    /**
     * Display review page for answered questions.
     */
    public function review($id, Request $request)
    {
        $material = $this->materialService->getMaterialWithQuestionsAndAnswers($id);
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
            'isGuest' => $isGuest
        ]);
    }

    /**
     * Get attempt count for a specific question.
     */
    public function getAttempts(int $materialId, int $questionId, Request $request)
    {
        $isGuest = $this->isGuestUser();

        if ($isGuest) {
            $progressKey = $materialId . '_' . $questionId;
            $guestProgress = $this->getGuestProgress($request);
            $attempts = isset($guestProgress[$progressKey]) ? $guestProgress[$progressKey]['attempt_number'] : 0;
        } else {
            $attempts = $this->progressService->getAttemptCount(auth()->id(), $materialId, $questionId);
        }

        return response()->json(['attempts' => $attempts]);
    }

    /**
     * Check answer submission.
     */
    public function checkAnswer(int $materialId, int $questionId, Request $request)
    {
        try {
            $material = $this->materialService->getMaterialById($materialId);
            $question = $this->questionService->getQuestionById($questionId);
            
            if (!$material || !$question) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Material atau soal tidak ditemukan'
                ], 404);
            }
            
            $userId = $this->getUserId();
            $isGuest = $this->isGuestUser();

            if (!$isGuest) {
                // Authenticated users: use adaptive system
                $result = $this->handleAdaptiveCheck($material, $question, $userId, $request->all());
                return response()->json($result);
            }
            
            // Guests: simple answer checking
            $result = $this->questionAnswerService->checkAnswer(
                array_merge($request->all(), ['material_id' => $materialId]),
                $userId,
                $isGuest
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle the complete adaptive quiz checking flow. (Previously in QuizOrchestratorService)
     */
    protected function handleAdaptiveCheck(Material $material, Question $question, int $userId, array $data): array
    {
        $isCorrect = $this->questionAnswerService->determineCorrectness($question, $data);
        $usedHint = $data['used_hint'] ?? false;
        $timeSpent = $data['time_spent'] ?? 0;

        // 1. Update student performance
        $studentState = $this->performanceService->updateStudentPerformance($userId, $isCorrect, $timeSpent, $usedHint);
        
        // 2. Calculate rewards
        $rewardResult = $isCorrect
            ? $this->rewardService->calculateCorrectAnswerReward($studentState->toArray(), $usedHint)
            : $this->rewardService->processWrongAnswer($studentState->toArray());
        
        // Apply rewards
        $gamification = $studentState->gamification_data;
        $gamification['global_xp'] = $rewardResult['updates']['global_xp'] ?? ($gamification['global_xp'] ?? 0);
        $studentState->gamification_data = $gamification;
        
        // Check streak bonus
        $streakBonus = $this->streakService->checkStreakBonus($studentState->toArray());
        if ($streakBonus) {
            $gamification = $studentState->gamification_data;
            $gamification['global_xp'] = ($gamification['global_xp'] ?? 0) + ($streakBonus['updates']['global_xp'] ?? 0);
            $studentState->gamification_data = $gamification;
        }

        // 2.5 Update student state
        $studentState->save();
        
        // 3. Gather facts
        $facts = $this->factGathering->gatherFacts(
            studentState: $studentState,
            isCorrect: $isCorrect,
            usedHint: $usedHint,
            score: $isCorrect ? 100 : 0,
            timeSpent: $timeSpent,
            difficulty: $question->difficulty,
            questionId: $question->id,
            materialId: $material->id,
            moduleId: $material->module_id ?? null
        );

        // 4. Evaluate adaptive rules
        $adaptiveResult = $this->adaptiveEngine->evaluate($facts, $studentState->toArray(), [
            'is_correct' => $isCorrect,
            'used_hint' => $usedHint,
            'score' => $isCorrect ? 100 : 0,
            'time_spent' => $timeSpent,
            'difficulty' => $question->difficulty,
            'question_id' => $question->id,
            'material_id' => $material->id,
            'module_id' => $material->module_id ?? null,
        ]);

        // 5. Apply adaptive state changes
        $finalState = $adaptiveResult['new_state'] ?? [];
        $studentState->fill($finalState);
        
        // Add time metrics to adaptive state
        $adaptiveState = $studentState->adaptive_state;
        if (is_string($adaptiveState)) {
            $adaptiveState = json_decode($adaptiveState, true) ?? [];
        }
        $adaptiveState = $adaptiveState ?? [];
        
        $adaptiveState['time_metrics'] = [
            'avg_time_per_question' => $this->performanceService->calculateAverageTimeSpent($userId, $material->id),
            'total_time_spent' => $this->performanceService->calculateTotalTimeSpent($userId, $material->id),
        ];
        $studentState->adaptive_state = $adaptiveState;
        $studentState->save();
        
        // 6. Save progress log
        // Handle answer_id based on question type
        $answerId = null;
        $userResponse = null;
        
        if ($question->question_type === 'multiple_choice' || $question->question_type === 'radio_button') {
            // For MC/Radio: answer_id is the selected option ID
            $answerId = $data['answer'] ?? null;
        } elseif ($question->question_type === 'fill_in_the_blank') {
            // For fill-in-the-blank: store text response, not answer_id
            $userResponse = $data['fill_in_the_blank_answer'] ?? null;
        } elseif ($question->question_type === 'drag_and_drop') {
            // For drag-and-drop: store arrangement data
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
            'attributes' => $finalState,
        ]);
        
        if ($timeSpent > 0) {
            $savedProgress->setTimeSpent($timeSpent);
            $savedProgress->save();
        }
        
        // 7. Resolve next action
        $nextActionData = $this->nextActionResolver->resolve($finalState['next_action'] ?? 'NEXT_QUESTION', $material, $question);
        
        return [
            'status' => $isCorrect ? 'success' : 'error',
            'message' => $finalState['message'] ?? ($isCorrect ? 'Jawaban benar!' : 'Jawaban salah, coba lagi.'),
            'hasNextQuestion' => true,
            'nextUrl' => $nextActionData['url'],
            'adaptiveResult' => [
                'triggered_rule' => $adaptiveResult['triggered_rule'],
                'facts' => $adaptiveResult['facts'],
                'global_xp_earned' => $rewardResult['global_xp_earned'] ?? 0,
                'streak_bonus' => $streakBonus ? $streakBonus['message'] : null,
                'new_state' => array_merge($studentState->toArray(), [
                    'recommendation' => $finalState['recommendation'] ?? null,
                    'next_action' => $nextActionData['label'],
                    'next_action_data' => $nextActionData,
                    'message' => $finalState['message'] ?? null,
                    'certification' => $finalState['certification'] ?? null,
                ]),
            ],
        ];
    }
}
