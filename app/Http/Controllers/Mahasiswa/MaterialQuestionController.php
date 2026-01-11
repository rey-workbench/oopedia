<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Services\Lms\Question\QuestionListingService;
use App\Services\Lms\Question\QuestionAnswerService;
use App\Repositories\MaterialRepository;
use App\Repositories\ProgressRepository;
use App\Services\Gamification\QuizRewardService;
use App\Services\Gamification\StreakService;
use App\Services\User\PerformanceService;
use App\Services\Adaptive\FactGatheringService;
use App\Services\Adaptive\AdaptiveEngineService;

class MaterialQuestionController extends Controller
{
    public function __construct(
        protected QuestionListingService $materialQuestionService,
        protected QuestionAnswerService $questionAnswerService,
        protected QuizRewardService $rewardService,
        protected StreakService $streakService,
        protected PerformanceService $performanceService,
        protected FactGatheringService $factGathering,
        protected AdaptiveEngineService $adaptiveEngine,
        protected MaterialRepository $materialRepo,
        protected ProgressRepository $progressRepo
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

        $materials = $this->materialQuestionService->getMaterialsListWithStudentCount($userId, $isGuest, $guestProgress);

        return view('mahasiswa.materials.questions.index', compact('materials', 'isGuest'));
    }

    /**
     * Display questions for a specific material.
     */
    public function show(Material $material, Request $request)
    {
        $isGuest = $this->isGuestUser();
        $difficulty = $request->query('difficulty', $isGuest ? 'beginner' : 'all');
        $guestProgress = $this->getGuestProgress($request);
        $userId = $this->getUserId();

        $data = $this->materialQuestionService->getQuizData($material, $difficulty, $userId, $isGuest, $guestProgress);

        return view('mahasiswa.materials.questions.show', $data);
    }

    /**
     * Display difficulty levels progress for a material.
     */
    public function levels(Material $material, Request $request)
    {
        $materials = $this->materialRepo->getAllOrdered();
        $difficulty = 'all';
        $isGuest = $this->isGuestUser();
        $userId = $this->getUserId();
        $guestProgress = $this->getGuestProgress($request);
        
        $answeredQuestionIds = $isGuest
            ? $this->materialQuestionService->getGuestAnsweredQuestionIds($material->id, $guestProgress)
            : $this->progressRepo->getAnsweredQuestionIds($userId, $material->id);

        $levels = $this->materialQuestionService->getLevelProgress($material, $difficulty, $answeredQuestionIds);

        return view('mahasiswa.materials.questions.levels', compact(
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
        $material = Material::with(['questions.answers'])->findOrFail($id);
        $materials = $this->materialRepo->getAllOrdered();
        $difficulty = $request->query('difficulty', 'all');
        $isGuest = $this->isGuestUser();
        $userId = $this->getUserId();
        $guestProgress = $this->getGuestProgress($request);

        $questions = $this->materialQuestionService->getReviewQuestions($material, $difficulty, $userId, $isGuest, $guestProgress);

        if ($request->ajax()) {
            return view('mahasiswa.partials.question-review-filtered', [
                'material' => $material,
                'questions' => $questions,
                'difficulty' => $difficulty
            ])->render();
        }

        return view('mahasiswa.materials.questions.review', [
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
    public function getAttempts(Material $material, Question $question, Request $request)
    {
        $isGuest = $this->isGuestUser();

        if ($isGuest) {
            $progressKey = $material->id . '_' . $question->id;
            $guestProgress = $this->getGuestProgress($request);
            $attempts = isset($guestProgress[$progressKey]) ? $guestProgress[$progressKey]['attempt_number'] : 0;
        } else {
            $attempts = $this->progressRepo->getAttemptCount(auth()->id(), $material->id, $question->id);
        }

        return response()->json(['attempts' => $attempts]);
    }

    /**
     * Check answer submission.
     */
    public function checkAnswer(Material $material, Question $question, Request $request)
    {
        try {
            $userId = $this->getUserId();
            $isGuest = $this->isGuestUser();

            if (!$isGuest) {
                // Authenticated users: use adaptive system
                $result = $this->handleAdaptiveCheck($material, $question, $userId, $request->all());
                return response()->json($result);
            }
            
            // Guests: simple answer checking
            $result = $this->questionAnswerService->checkAnswer(
                array_merge($request->all(), ['material_id' => $material->id]),
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
        $savedProgress = $this->progressRepo->saveProgress([
            'user_id' => $userId,
            'material_id' => $material->id,
            'question_id' => $question->id,
            'answer_id' => $data['answer_id'] ?? null,
            'is_correct' => $isCorrect,
            'is_answered' => true,
            'attributes' => $finalState,
        ]);
        
        if ($timeSpent > 0) {
            $savedProgress->setTimeSpent($timeSpent);
            $savedProgress->save();
        }
        
        // 7. Resolve next action
        $nextActionData = $this->resolveDynamicNextAction($finalState['next_action'] ?? 'NEXT_QUESTION', $material);
        
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

    /**
     * Resolve dynamic next action command into URL and metadata.
     */
    protected function resolveDynamicNextAction(string $actionCommand, Material $material): array
    {
        return match ($actionCommand) {
            'STUDY_MATERIAL' => [
                'label' => 'Ulas Materi: ' . $material->title,
                'url' => route('mahasiswa.materials.show', $material->id),
                'type' => 'material'
            ],
            'REDUCE_DIFFICULTY' => $this->resolveReduceDifficulty($material),
            'INCREASE_DIFFICULTY' => $this->resolveIncreaseDifficulty($material),
            'FINISH_MATERIAL' => [
                'label' => 'Selesaikan Modul',
                'url' => route('mahasiswa.dashboard'),
                'type' => 'navigation'
            ],
            'ISSUE_CERTIFICATE' => [
                'label' => 'Klaim Sertifikat',
                'url' => route('mahasiswa.dashboard'),
                'type' => 'certificate'
            ],
            default => [
                'label' => 'Soal Berikutnya',
                'url' => route('mahasiswa.materials.questions.show', ['material' => $material->id]),
                'type' => 'question'
            ],
        };
    }

    protected function resolveReduceDifficulty(Material $material): array
    {
        $hasBeginner = Question::where('material_id', $material->id)
            ->where('difficulty', 'beginner')
            ->exists();
        
        return [
            'label' => $hasBeginner ? 'Coba Soal Pemula' : 'Ulas Materi Dasar',
            'url' => $hasBeginner 
                ? route('mahasiswa.materials.questions.show', ['material' => $material->id, 'difficulty' => 'beginner'])
                : route('mahasiswa.materials.show', $material->id),
            'type' => $hasBeginner ? 'question' : 'material'
        ];
    }

    protected function resolveIncreaseDifficulty(Material $material): array
    {
        $hasHard = Question::where('material_id', $material->id)
            ->where('difficulty', 'hard')
            ->exists();
        
        return [
            'label' => $hasHard ? 'Tantangan Menantang' : 'Lanjut ke Materi Baru',
            'url' => $hasHard
                ? route('mahasiswa.materials.questions.show', ['material' => $material->id, 'difficulty' => 'hard'])
                : route('mahasiswa.dashboard'),
            'type' => $hasHard ? 'question' : 'navigation'
        ];
    }
}
