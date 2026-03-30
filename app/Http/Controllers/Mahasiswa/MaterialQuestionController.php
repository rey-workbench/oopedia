<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Services\AdaptiveQuizFlowServiceInterface;
use App\Contracts\Services\GuestProgressServiceInterface;
use App\Contracts\Services\MaterialServiceInterface;
use App\Contracts\Services\PerformanceServiceInterface;
use App\Contracts\Services\QuestionAnswerServiceInterface;
use App\Contracts\Services\QuestionListingServiceInterface;
use App\Contracts\Services\QuestionServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
        protected ProgressRepositoryInterface $progressRepo,
        protected PerformanceServiceInterface $performanceService,
        protected AdaptiveQuizFlowServiceInterface $adaptiveQuizFlowService,
        protected GuestProgressServiceInterface $guestProgressService,
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

        return $this->guestProgressService->getProgress();
    }

    // ==================== PUBLIC ROUTES ====================

    /**
     * Display list of all materials with progress.
     */
    public function index(Request $request): Response
    {
        $isGuest       = $this->isGuestUser();
        $userId        = $this->getUserId();
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

        $difficulty    = 'all';
        $guestProgress = $this->getGuestProgress($request);
        $userId        = $this->getUserId();

        // Fetch student state for detailed stats
        $studentStateData = [];
        $targetDifficulty = null;
        if (! $isGuest) {
            $studentState = $this->performanceService->getStudentState($userId);
            if ($studentState) {
                $adaptiveState = $studentState->adaptive_state ?? [];
                if (is_string($adaptiveState)) {
                    $adaptiveState = json_decode($adaptiveState, true) ?? [];
                }

                // ── MATERIAL CHANGE DETECTION ──────────────────────────────
                // If the student is opening a different material than the last
                // one tracked in adaptive_state, reset all material-scoped
                // flags so the previous material's context does not bleed in.
                $lastMaterialId = $adaptiveState['current_material_id'] ?? null;
                if ($lastMaterialId !== null && (int) $lastMaterialId !== (int) $materialId) {
                    $adaptiveState['target_difficulty'] = null;
                    $adaptiveState['fast_track_active'] = false;
                    $adaptiveState['last_rule']         = null;

                    // Also reset wrong_streak so crisis rules don't fire on the
                    // first question of a new material due to carry-over streak.
                    $metrics                           = $studentState->performance_metrics ?? [];
                    $metrics['wrong_streak']           = 0;
                    $studentState->performance_metrics = $metrics;

                    $studentState->adaptive_state = $adaptiveState;
                    $studentState->save();
                }
                // ────────────────────────────────────────────────────────────

                $targetDifficulty = $adaptiveState['target_difficulty'] ?? null;

                $studentStateData = [
                    'gamification'     => $studentState->gamification_data,
                    'performance'      => $studentState->performance_metrics,
                    'learning_profile' => $studentState->learning_profile,
                ];
            }
        } else {
            // Mock state for guests from session to ensure continuity
            $studentStateData = [
                'gamification' => [
                    'global_xp'      => $this->guestProgressService->getGamificationState()['xp'],
                    'current_streak' => $this->guestProgressService->getGamificationState()['streak'],
                    'current_level'  => 'Tamu',
                ],
                'performance' => [
                    'hints_available'          => 3,
                    'total_questions_answered' => count($this->guestProgressService->getProgress()),
                ],
                'learning_profile' => [],
            ];
        }

        $data = $this->questionListingService->getQuizData($material, $difficulty, $userId, $isGuest, $guestProgress, $targetDifficulty);

        return Inertia::render('Mahasiswa/Materials/Questions/Show/Index', array_merge($data, [
            'isGuest'      => $isGuest,
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

        $isGuest       = $this->isGuestUser();
        $userId        = $this->getUserId();
        $guestProgress = $this->getGuestProgress($request);

        $answeredQuestionIds = $isGuest
            ? $this->questionListingService->getGuestAnsweredQuestionIds($material->id, $guestProgress)
            : $this->progressRepo->getAnsweredQuestionIds($userId, $material->id);

        $levels = $this->questionListingService->getLevelProgress($material, 'all', $answeredQuestionIds, $isGuest);

        return Inertia::render('Mahasiswa/Materials/Questions/Levels/Index', compact(
            'material',
            'levels',
        ));
    }

    /**
     * Display review page for answered questions.
     */
    public function review(int|string $id, Request $request): Response
    {
        $material      = $this->materialService->getMaterialWithQuestionsAndAnswers((int) $id);
        $materials     = $this->materialService->getAllOrdered();
        $difficulty    = $request->query('difficulty', 'all');
        $isGuest       = $this->isGuestUser();
        $userId        = $this->getUserId();
        $guestProgress = $this->getGuestProgress($request);

        $questions = $this->questionListingService->getReviewQuestions($material, $difficulty, $userId, $isGuest, $guestProgress);

        return Inertia::render('Mahasiswa/Materials/Questions/Review/Index', [
            'material'   => $material,
            'materials'  => $materials,
            'questions'  => $questions,
            'difficulty' => $difficulty,
            'isGuest'    => $isGuest,
        ]);
    }

    /**
     * Get attempt count for a specific question.
     */
    public function getAttempts(int|string $materialId, int|string $questionId, Request $request): JsonResponse
    {
        $isGuest = $this->isGuestUser();

        if ($isGuest) {
            $progressKey   = $materialId . '_' . $questionId;
            $guestProgress = $this->getGuestProgress($request);
            $attempts      = isset($guestProgress[$progressKey]) ? $guestProgress[$progressKey]['attempt_number'] : 0;
        } else {
            $attempts = $this->progressRepo->getAttemptCount(Auth::id(), (int) $materialId, (int) $questionId);
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
                'status'  => 'error',
                'message' => 'Material atau soal tidak ditemukan',
            ], 404);
        }

        $userId  = $this->getUserId();
        $isGuest = $this->isGuestUser();

        Log::debug('[MaterialQuestionController] Request data for checkAnswer:', [
            'material_id' => $materialId,
            'question_id' => $questionId,
            'user_id'     => $userId,
            'is_guest'    => $isGuest,
            'payload'     => $request->all(),
        ]);

        if (! $isGuest) {
            // Use Adaptive Flow Service
            $result = $this->adaptiveQuizFlowService->processAdaptiveAttempt($material, $question, $userId, $request->all());

            Log::debug('[MaterialQuestionController] Result for checkAnswer (Auth):', $result);

            return response()->json($result);
        }

        $result = $this->questionAnswerService->checkAnswer(
            array_merge($request->all(), ['material_id' => $materialId]),
            $userId,
            $isGuest,
        );

        Log::debug('[MaterialQuestionController] Result for checkAnswer (Guest):', $result);

        return response()->json($result);
    }
}
