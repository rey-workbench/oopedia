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
use App\Services\AdaptiveQuizService;
use App\Services\MaterialQuestionService;
use App\Services\QuestionAnswerService;
use App\Repositories\Interfaces\MaterialRepositoryInterface;
use App\Repositories\Interfaces\ProgressRepositoryInterface;
use App\Repositories\Interfaces\QuestionRepositoryInterface;
use App\Repositories\Interfaces\AnswerRepositoryInterface;
use Illuminate\Support\Facades\Cookie;
class MaterialQuestionController extends Controller
{
    protected $adaptiveService;
    protected $materialQuestionService;
    protected $questionAnswerService;
    protected $materialRepo;
    protected $progressRepo;
    protected $questionRepo;
    protected $answerRepo;

    public function __construct(
        AdaptiveQuizService $adaptiveService,
        MaterialQuestionService $materialQuestionService,
        QuestionAnswerService $questionAnswerService,
        MaterialRepositoryInterface $materialRepo,
        ProgressRepositoryInterface $progressRepo,
        QuestionRepositoryInterface $questionRepo,
        AnswerRepositoryInterface $answerRepo
    ) {
        $this->adaptiveService = $adaptiveService;
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

        // Process with adaptive service
        $adaptiveResult = $this->adaptiveService->processAttempt(
            $userId,
            $material->id,
            $question,
            $isCorrect,
            $request->boolean('used_hint', false)
        );

        // Save progress (Cookie for Guest, DB for Authenticated)
        if ($isGuest = !auth()->check()) {
            // Read existing progress from cookie
            $guestProgressJson = $request->cookie('guest_progress', '[]');
            $guestProgress = json_decode($guestProgressJson, true) ?? [];
            
            $progressKey = $material->id . '_' . $question->id;
            
            $guestProgress[$progressKey] = [
                'is_correct' => $isCorrect,
                'xp_earned' => $adaptiveResult['xp_earned'],
                'points_earned' => $adaptiveResult['points_earned'],
                'attempt_number' => ($guestProgress[$progressKey]['attempt_number'] ?? 0) + 1,
                'last_attempt_at' => now()->toDateTimeString()
            ];
            
            // Encode back to JSON
             $updatedCookieValue = json_encode($guestProgress);
             
             // Create cookie valid for 30 days (43200 minutes)
             $cookie = cookie('guest_progress', $updatedCookieValue, 43200, null, null, false, false); // HttpOnly false so JS can read if needed
             
             // We don't need 'guest_progress.material_id' hierarchy anymore if we parse keys in service
        } else {
            // Create progress record in DB with Snapshot
            Progress::create([
                'user_id' => $userId,
                'material_id' => $material->id,
                'question_id' => $question->id,
                'is_correct' => $isCorrect,
                'is_answered' => true,
                'attempt_number' => 1, 
                'attributes' => $adaptiveResult['new_state'] 
            ]);
            
            Cache::forget('leaderboard_data');
        }

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

        $response = response()->json($responseData);
        
        // Attach cookie if guest
        if (isset($cookie)) {
            $response->withCookie($cookie);
        }
        
        return $response;
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