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
        $difficulty = $isGuest ? $request->query('difficulty', 'beginner') : 'all';
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
        $isCorrect = $this->determineCorrectness($question, $request);
        $usedHint = $request->boolean('used_hint', false);
        $timeSpent = $request->integer('time_spent', 0);

        // 1. Get current state via Service (Strict Service Layer)
        $studentState = $this->personalizationService->updateStudentPerformance($userId, $isCorrect, $timeSpent);
        
        // Prepare state for Rules Engine
        $state = $studentState->toArray();
        
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
            $difficulty,
            'quiz' // materialType default
        );

        // Merge all results
        $finalState = $personalizationResult['new_state'];

        // 3. Save progress
        $savedProgress = $this->progressRepo->saveProgress([
            'user_id' => $userId,
            'material_id' => $material->id,
            'question_id' => $question->id,
            'is_correct' => $isCorrect,
            'is_answered' => true,
            'attributes' => $finalState,
        ]);

        // Set time spent
        if ($timeSpent > 0) {
            $savedProgress->setTimeSpent($timeSpent);
            $savedProgress->save();
        }

        // 4. Prepare response
        $adaptiveResult = [
            'status' => 'success',
            'is_correct' => $isCorrect,
            'xp_earned' => $rewardResult['xp_earned'] ?? 0,
            'points_earned' => $rewardResult['points_earned'] ?? 0,
            'streak_bonus' => $streakBonus ? $streakBonus['message'] : null,
            'fast_track_active' => $finalState['fast_track_active'] ?? 0,
            'state_summary' => $this->rewardService->getStateSummary($finalState),
            'triggered_rules' => $personalizationResult['triggered_rules'],
            'new_state' => \Illuminate\Support\Arr::only($finalState, [
                'xp', 'points', 'current_level', 
                'total_questions_answered', 'correct_count', 'wrong_count', 
                'current_streak', 'wrong_streak', 
                'hints_used', 'hints_available', 
                'avg_time_spent', 'total_time_spent', 
                'fast_track_active',
                'recommendation', 'next_action', 'message', 'certification' // Dynamic fields from rules
            ]),
        ];

        // Prepare response
        $responseData = [
            'status' => $isCorrect ? 'success' : 'error',
            'message' => $isCorrect ? 'Jawaban benar!' : 'Jawaban salah, coba lagi.',
            'hasNextQuestion' => true,
            'nextUrl' => route('mahasiswa.materials.questions.show', ['material' => $material->id]),
            'adaptiveResult' => $adaptiveResult
        ];

        if (!$isCorrect) {
            $responseData['nextUrl'] = route('mahasiswa.materials.questions.show', [
                'material' => $material->id,
                'question' => $question->id
            ]);
        }

        return response()->json($responseData);
    }

    protected function determineCorrectness($question, $request)
    {
        if ($question->question_type === 'multiple_choice' || $question->question_type === 'radio_button') {
            $selectedAnswer = Answer::findOrFail($request->answer);
            return $selectedAnswer->is_correct;
        } elseif ($question->question_type === 'fill_in_the_blank') {
            $answer = trim(strtolower($request->fill_in_the_blank_answer));
            $correctAnswer = trim(strtolower($question->correct_answer));
            return $answer === $correctAnswer;
        } elseif ($question->question_type === 'true_false') {
            $selectedAnswer = ($request->answer === 'true');
            return $selectedAnswer === $question->is_true;
        }

        return false;
    }

}