<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Progress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\QuizRewardService;
use App\Services\PersonalizationService;
use App\Services\PersonalizationRulesService;
use App\Services\MaterialQuestionService;
use App\Services\QuestionAnswerService;
use App\Repositories\Interfaces\MaterialRepositoryInterface;
use App\Repositories\Interfaces\ProgressRepositoryInterface;
use App\Repositories\Interfaces\QuestionRepositoryInterface;
use App\Repositories\Interfaces\AnswerRepositoryInterface;
use Illuminate\Support\Facades\Cookie;
class MaterialQuestionController extends Controller
{
    protected $rewardService;
    protected $personalizationService;
    protected $rulesService;
    protected $materialQuestionService;
    protected $questionAnswerService;
    protected $materialRepo;
    protected $progressRepo;
    protected $questionRepo;
    protected $answerRepo;

    public function __construct(
        QuizRewardService $rewardService,
        PersonalizationService $personalizationService,
        PersonalizationRulesService $rulesService,
        MaterialQuestionService $materialQuestionService,
        QuestionAnswerService $questionAnswerService,
        MaterialRepositoryInterface $materialRepo,
        ProgressRepositoryInterface $progressRepo,
        QuestionRepositoryInterface $questionRepo,
        AnswerRepositoryInterface $answerRepo
    ) {
        $this->rewardService = $rewardService;
        $this->personalizationService = $personalizationService;
        $this->rulesService = $rulesService;
        $this->materialQuestionService = $materialQuestionService;
        $this->questionAnswerService = $questionAnswerService;
        $this->materialRepo = $materialRepo;
        $this->progressRepo = $progressRepo;
        $this->questionRepo = $questionRepo;
        $this->answerRepo = $answerRepo;
    }

    public function index(Request $request)
    {
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);
        $userId = $isGuest ? session()->getId() : auth()->id();
        
        // Read guest progress from cookie
        $guestProgress = [];
        if ($isGuest) {
            $guestProgressJson = $request->cookie('guest_progress', '[]');
            $guestProgress = json_decode($guestProgressJson, true) ?? [];
        }

        $materials = $this->materialQuestionService->getMaterialsListWithStudentCount($userId, $isGuest, $guestProgress);

        return view('mahasiswa.materials.questions.index', compact('materials', 'isGuest'));
    }

    public function show(Material $material, Request $request)
    {
        $materials = $this->materialRepo->getAllOrdered();
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);
        $difficulty = $isGuest ? $request->query('difficulty', 'beginner') : 'all';
        $questionId = null;

        // Read guest progress from cookie
        $guestProgress = [];
        if ($isGuest) {
             $guestProgressJson = $request->cookie('guest_progress', '[]');
             $guestProgress = json_decode($guestProgressJson, true) ?? [];
        }

        // Get filtered questions
        $result = $this->materialQuestionService->getFilteredQuestions($material, $difficulty, $isGuest);
        $questions = $result['questions'];
        $totalFilteredQuestions = $result['totalFilteredQuestions'];

        // Get answered questions
        $userId = $isGuest ? session()->getId() : auth()->id();
        $answeredQuestionIds = $isGuest
            ? $this->materialQuestionService->getGuestAnsweredQuestionIds($material->id, $guestProgress)
            : collect($this->progressRepo->getAnsweredQuestionIds($userId, $material->id));

        // Get current question
        $currentQuestion = $this->materialQuestionService->getCurrentQuestion($questions, $answeredQuestionIds, $questionId);

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
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);

        $userId = auth()->id() ?? session()->getId();
        
        $guestProgress = [];
        if ($isGuest) {
             $guestProgressJson = $request->cookie('guest_progress', '[]');
             $guestProgress = json_decode($guestProgressJson, true) ?? [];
        }
        
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
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);
        $userId = auth()->id();
        
        $guestProgress = [];
        if ($isGuest) {
             $guestProgressJson = $request->cookie('guest_progress', '[]');
             $guestProgress = json_decode($guestProgressJson, true) ?? [];
        }

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

    public function getAttempts(Material $material, Question $question)
    {
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);

        if ($isGuest) {
            $progressKey = $material->id . '_' . $question->id;
            $guestProgressJson = request()->cookie('guest_progress', '[]');
            $guestProgress = json_decode($guestProgressJson, true) ?? [];
            
            $attempts = isset($guestProgress[$progressKey]) ? $guestProgress[$progressKey]['attempt_number'] : 0;
        } else {
            $attempts = $this->progressRepo->getAttemptCount(auth()->id(), $material->id, $question->id);
        }

        return response()->json(['attempts' => $attempts]);
    }

    public function checkAnswer(Material $material, Question $question, Request $request)
    {
        try {
            $userId = auth()->id() ?? session()->getId();
            $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);

            $difficulty = $isGuest ? $request->input('difficulty', 'beginner') : 'all';

            // Check answer using different logic for auth users vs guests
            if (auth()->check() && auth()->user()->role_id !== 4) {
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

        // 1. Get current state from latest progress
        $latestProgress = $this->progressRepo->getLatestProgress($userId);
        $state = $latestProgress ? ($latestProgress->attributes ?? []) : [];
        $state = $this->initializeDefaults($state);

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
        $state['avg_time_spent'] = $this->personalizationService->calculateAverageTimeSpent($userId, $material->id);
        $state['total_time_spent'] = $this->personalizationService->calculateTotalTimeSpent($userId, $material->id);

        // 2c. Evaluate personalization rules (forward chaining)
        $personalizationResult = $this->rulesService->evaluate($state, $isCorrect, $usedHint);

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

        Cache::forget('leaderboard_data');

        // 4. Prepare response
        $adaptiveResult = [
            'status' => 'success',
            'is_correct' => $isCorrect,
            'xp_earned' => $rewardResult['xp_earned'] ?? 0,
            'points_earned' => $rewardResult['points_earned'] ?? 0,
            'streak_bonus' => $streakBonus ? $streakBonus['message'] : null,
            'fast_track_active' => $finalState['fast_track_active'] ?? 0,
            'show_fatigue_warning' => $finalState['show_fatigue_warning'] ?? 0,
            'personalization_type' => $finalState['personalization_type'] ?? null,
            'state_summary' => $this->rewardService->getStateSummary($finalState),
            'triggered_rules' => $personalizationResult['triggered_rules'],
            'new_state' => $finalState,
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

    protected function initializeDefaults(array $state): array
    {
        $defaults = [
            'xp' => 0,
            'points' => 0,
            'total_questions_answered' => 0,
            'correct_count' => 0,
            'wrong_count' => 0,
            'current_streak' => 0,
            'wrong_streak' => 0,
            'hints_used' => 0,
            'hints_available' => 0,
            'avg_time_spent' => 0,
            'total_time_spent' => 0,
            'fast_track_active' => 0,
            'show_fatigue_warning' => 0,
            'personalization_type' => null,
        ];

        return array_merge($defaults, $state);
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

    public function showLevels(Material $material, Request $request)
    {
        return $this->levels($material, $request);
    }

    public function debugGuestProgressIssue(Request $request, $materialId)
    {
        $sessionId = session()->getId();
        $guestProgress = session('guest_progress', []);
        $materialProgress = session('guest_progress.' . $materialId, []);

        $specificProgress = [];
        foreach ($guestProgress as $key => $progress) {
            if (strpos($key, $materialId . '_') === 0) {
                $specificProgress[$key] = $progress;
            }
        }

        $difficulty = $request->query('difficulty', 'beginner');
        $questions = Question::where('material_id', $materialId)
            ->where('difficulty', $difficulty)
            ->get(['id', 'difficulty']);

        return response()->json([
            'session_id' => $sessionId,
            'guest_progress' => $guestProgress,
            'material_progress' => $materialProgress,
            'specific_progress' => $specificProgress,
            'material_id' => $materialId,
            'difficulty' => $difficulty,
            'available_questions' => $questions->pluck('id')->toArray()
        ]);
    }
}