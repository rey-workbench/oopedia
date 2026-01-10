<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;
use App\Services\QuizRewardService;
use App\Services\PersonalizationService;
use App\Services\PersonalizationRulesService;
use App\Services\MaterialQuestionService;
use App\Services\QuestionAnswerService;
use App\Repositories\MaterialRepository;
use App\Repositories\ProgressRepository;
class MaterialQuestionController extends Controller
{
    protected $rewardService;
    protected $personalizationService;
    protected $rulesService;
    protected $materialQuestionService;
    protected $questionAnswerService;
    protected $materialRepo;
    protected $progressRepo;

    public function __construct(
        QuizRewardService $rewardService,
        PersonalizationService $personalizationService,
        PersonalizationRulesService $rulesService,
        MaterialQuestionService $materialQuestionService,
        QuestionAnswerService $questionAnswerService,
        MaterialRepository $materialRepo,
        ProgressRepository $progressRepo
    ) {
        $this->rewardService = $rewardService;
        $this->personalizationService = $personalizationService;
        $this->rulesService = $rulesService;
        $this->materialQuestionService = $materialQuestionService;
        $this->questionAnswerService = $questionAnswerService;
        $this->materialRepo = $materialRepo;
        $this->progressRepo = $progressRepo;
    }

    /**
     * Helper: Check if current user is a guest
     */
    protected function isGuestUser(): bool
    {
        return !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);
    }

    /**
     * Helper: Get user ID (session ID for guests, auth ID for logged in users)
     */
    protected function getUserId(): string|int
    {
        return $this->isGuestUser() ? session()->getId() : auth()->id();
    }

    /**
     * Helper: Get guest progress from cookie
     */
    protected function getGuestProgress(Request $request): array
    {
        if (!$this->isGuestUser()) {
            return [];
        }
        
        $guestProgressJson = $request->cookie('guest_progress', '[]');
        return json_decode($guestProgressJson, true) ?? [];
    }

    public function index(Request $request)
    {
        $isGuest = $this->isGuestUser();
        $userId = $this->getUserId();
        $guestProgress = $this->getGuestProgress($request);

        $materials = $this->materialQuestionService->getMaterialsListWithStudentCount($userId, $isGuest, $guestProgress);

        return view('mahasiswa.materials.questions.index', compact('materials', 'isGuest'));
    }

    public function show(Material $material, Request $request)
    {
        $materials = $this->materialRepo->getAllOrdered();
        $isGuest = $this->isGuestUser();
        $difficulty = $request->query('difficulty', $isGuest ? 'beginner' : 'all');
        $guestProgress = $this->getGuestProgress($request);
        $userId = $this->getUserId();

        // Get filtered questions
        $result = $this->materialQuestionService->getFilteredQuestions($material, $difficulty, $isGuest);
        $questions = $result['questions'];
        $totalFilteredQuestions = $result['totalFilteredQuestions'];

        // Get answered questions
        $answeredQuestionIds = $isGuest
            ? $this->materialQuestionService->getGuestAnsweredQuestionIds($material->id, $guestProgress)
            : collect($this->progressRepo->getAnsweredQuestionIds($userId, $material->id));

        // Get current question
        $currentQuestion = $this->materialQuestionService->getCurrentQuestion($questions, $answeredQuestionIds, null);

        // Calculate current question number
        $answeredCount = $answeredQuestionIds->count();
        $currentQuestionNumber = min($answeredCount + 1, $totalFilteredQuestions);

        return view('mahasiswa.materials.questions.show', [
            'material' => $material,
            'materials' => $materials,
            'questions' => $questions,
            'difficulty' => $difficulty,
            'isGuest' => $isGuest,
            'currentQuestion' => $currentQuestion,
            'currentQuestionNumber' => $currentQuestionNumber,
            'totalFilteredQuestions' => $totalFilteredQuestions
        ]);
    }

    public function levels(Material $material, Request $request)
    {
        $materials = $this->materialRepo->getAllOrdered();
        $difficulty = 'all';
        $isGuest = $this->isGuestUser();
        $userId = $this->getUserId();
        $guestProgress = $this->getGuestProgress($request);
        
        $answeredQuestionIds = $isGuest
            ? $this->materialQuestionService->getGuestAnsweredQuestionIds($material->id, $guestProgress)
            : collect($this->progressRepo->getAnsweredQuestionIds($userId, $material->id));

        $levels = $this->materialQuestionService->getLevelProgress($material, $difficulty, $answeredQuestionIds);

        return view('mahasiswa.materials.questions.levels', compact(
            'material',
            'materials',
            'levels',
            'difficulty',
            'isGuest'
        ));
    }

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

    public function checkAnswer(Material $material, Question $question, Request $request)
    {
        try {
            $userId = $this->getUserId();
            $isGuest = $this->isGuestUser();
            $difficulty = $isGuest ? $request->input('difficulty', 'beginner') : 'all';

            // Check answer using different logic for auth users vs guests
            if (!$isGuest) {
                // For authenticated users: use adaptive quiz service
                return $this->checkAnswerWithAdaptive($material, $question, $request, $userId, $difficulty);
            } else {
                // For guests: use simple answer checking
                $result = $this->questionAnswerService->checkAnswer(
                    array_merge($request->all(), ['material_id' => $material->id]),
                    $userId,
                    $isGuest
                );

                return response()->json($result);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function checkAnswerWithAdaptive($material, $question, $request, $userId, $difficulty)
    {
        $isCorrect = $this->questionAnswerService->determineCorrectness($question, $request->all());
        $usedHint = $request->boolean('used_hint', false);
        $timeSpent = $request->integer('time_spent', 0);

        // 1. Get current state via Service (Strict Service Layer)
        $studentState = $this->personalizationService->updateStudentPerformance($userId, $isCorrect, $timeSpent, $usedHint);
        
        // Prepare state for Rules Engine - use attributes directly 
        $state = $studentState->getAttributes();
        
        // 2. ORCHESTRATION IN CONTROLLER: Call each service independently
        
        // 2a. Calculate rewards (non-adaptive)
        if ($isCorrect) {
            $rewardResult = $this->rewardService->calculateCorrectAnswerReward($state, $usedHint);
        } else {
            $rewardResult = $this->rewardService->processWrongAnswer($state);
        }

        // Apply reward updates to state
        foreach ($rewardResult['updates'] as $key => $value) {
            $state[$key] = $value;
        }

        // Check streak bonus
        $streakBonus = $this->rewardService->checkStreakBonus($state);
        if ($streakBonus) {
            foreach ($streakBonus['updates'] as $key => $value) {
                $state[$key] = $value;
            }
        }

        // 2b. Update personalization metrics
        $state['current_level'] = $state['current_level'] ?? 'Pemula';
        $state['avg_time_spent'] = $this->personalizationService->calculateAverageTimeSpent($userId, $material->id);
        $state['total_time_spent'] = $this->personalizationService->calculateTotalTimeSpent($userId, $material->id);

        // 2c. Evaluate personalization rules (forward chaining) with new Spec
        $personalizationResult = $this->rulesService->evaluate(
            $state, 
            $isCorrect, 
            $usedHint, 
            $isCorrect ? 100 : 0, // Score
            $timeSpent,
            $question->difficulty,
            'quiz' // materialType default
        );

        // Merge all results
        $finalState = $personalizationResult['new_state'];
        
        // 3. Save final state to persistence
        $studentState->fill($finalState);
        $studentState->save();

        // Save question progress log
        $savedProgress = $this->progressRepo->saveProgress([
            'user_id' => $userId,
            'material_id' => $material->id,
            'question_id' => $question->id,
            'answer_id' => $request->answer, // Capture which option was selected
            'is_correct' => $isCorrect,
            'is_answered' => true,
            'attributes' => $finalState,
        ]);

        // Set time spent on log
        if ($timeSpent > 0) {
            $savedProgress->setTimeSpent($timeSpent);
            $savedProgress->save();
        }

        // 5. RESOLVE DYNAMIC NEXT ACTION (Database Aware)
        $nextActionData = $this->resolveDynamicNextAction($finalState['next_action'] ?? 'NEXT_QUESTION', $material);
        $nextUrl = $nextActionData['url'];

        // 4. Prepare response
        $adaptiveResult = [
            'status' => 'success',
            'is_correct' => $isCorrect,
            'global_xp_earned' => $rewardResult['global_xp_earned'] ?? 0,
            'streak_bonus' => $streakBonus ? $streakBonus['message'] : null,
            'fast_track_active' => $finalState['fast_track_active'] ?? 0,
            'state_summary' => $this->rewardService->getStateSummary($finalState),
            'triggered_rules' => $personalizationResult['triggered_rules'],
            'new_state' => [
                'global_xp' => (int)($finalState['global_xp'] ?? 0),
                'current_level' => $finalState['current_level'] ?? 'Pemula',
                'total_questions_answered' => (int)($finalState['total_questions_answered'] ?? 0),
                'correct_count' => (int)($finalState['correct_count'] ?? 0),
                'wrong_count' => (int)($finalState['wrong_count'] ?? 0),
                'current_streak' => (int)($finalState['current_streak'] ?? 0),
                'wrong_streak' => (int)($finalState['wrong_streak'] ?? 0),
                'hints_used_count' => (int)($finalState['hints_used_count'] ?? 0),
                'hints_available' => (int)($finalState['hints_available'] ?? 3),
                'avg_time_spent' => $finalState['avg_time_spent'] ?? 0,
                'total_time_spent' => $finalState['total_time_spent'] ?? 0,
                'fast_track_active' => $finalState['fast_track_active'] ?? 0,
                'recommendation' => $finalState['recommendation'] ?? null,
                'next_action_data' => $nextActionData, 
                'next_action' => $nextActionData['label'], // For backward-compat with simple string if needed
                'message' => $finalState['message'] ?? null,
                'certification' => $finalState['certification'] ?? null
            ],
        ];

        // Prepare response
        $responseData = [
            'status' => $isCorrect ? 'success' : 'error',
            'message' => $isCorrect ? 'Jawaban benar!' : 'Jawaban salah, coba lagi.',
            'hasNextQuestion' => true,
            'nextUrl' => $nextUrl,
            'adaptiveResult' => $adaptiveResult
        ];

        if (!$isCorrect && ($finalState['next_action'] ?? null) !== 'STUDY_MATERIAL' && ($finalState['next_action'] ?? null) !== 'REDUCE_DIFFICULTY') {
            $responseData['nextUrl'] = route('mahasiswa.materials.questions.show', [
                'material' => $material->id,
                'question' => $question->id
            ]);
        }

        return response()->json($responseData);
    }

    /**
     * Resolve a dynamic next action command into real DB-based data.
     */
    protected function resolveDynamicNextAction($actionCommand, $material)
    {
        $materialId = $material->id;

        switch ($actionCommand) {
            case 'STUDY_MATERIAL':
                return [
                    'label' => 'Ulas Materi: ' . $material->title,
                    'url' => route('mahasiswa.materials.show', $materialId),
                    'type' => 'material'
                ];
            
            case 'REDUCE_DIFFICULTY':
                $hasBeginner = Question::where('material_id', $materialId)->where('difficulty', 'beginner')->exists();
                return [
                    'label' => $hasBeginner ? 'Coba Soal Pemula' : 'Ulas Materi Dasar',
                    'url' => $hasBeginner 
                        ? route('mahasiswa.materials.questions.show', ['material' => $materialId, 'difficulty' => 'beginner'])
                        : route('mahasiswa.materials.show', $materialId),
                    'type' => $hasBeginner ? 'question' : 'material'
                ];
                
            case 'INCREASE_DIFFICULTY':
                 $hasHard = Question::where('material_id', $materialId)->where('difficulty', 'hard')->exists();
                 return [
                    'label' => $hasHard ? 'Tantangan Menantang' : 'Lanjut ke Materi Baru',
                    'url' => $hasHard
                        ? route('mahasiswa.materials.questions.show', ['material' => $materialId, 'difficulty' => 'hard'])
                        : route('mahasiswa.dashboard'),
                    'type' => $hasHard ? 'question' : 'navigation'
                ];

            case 'FINISH_MATERIAL':
                return [
                    'label' => 'Selesaikan Modul',
                    'url' => route('mahasiswa.dashboard'),
                    'type' => 'navigation'
                ];

            case 'ISSUE_CERTIFICATE':
                return [
                    'label' => 'Klaim Sertifikat',
                    'url' => route('mahasiswa.dashboard'), 
                    'type' => 'certificate'
                ];

            case 'NEXT_QUESTION':
            default:
                return [
                    'label' => 'Soal Berikutnya',
                    'url' => route('mahasiswa.materials.questions.show', ['material' => $materialId]),
                    'type' => 'question'
                ];
        }
    }



}