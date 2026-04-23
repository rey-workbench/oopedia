<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Services\AdaptiveEngineServiceInterface;
use App\Contracts\Services\FactGatheringServiceInterface;
use App\Contracts\Services\GuestProgressServiceInterface;
use App\Contracts\Services\MaterialServiceInterface;
use App\Contracts\Services\PerformanceServiceInterface;
use App\Contracts\Services\QuestionAnswerServiceInterface;
use App\Contracts\Services\QuestionListingServiceInterface;
use App\Enums\Lms\QuestionDifficulty;
use App\Http\Controllers\Controller;
use App\Http\Requests\Question\CheckAnswerRequest;
use App\Http\Requests\Question\ReviewQuestionRequest;
use App\Models\Question;
use App\Models\StudentState;
use App\Rules\Adaptive\Constants\AdaptiveConstants as AC;
use App\Traits\HandlesAdaptiveState;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

final class MaterialQuestionController extends Controller
{
    use HandlesAdaptiveState;

    public function __construct(
        protected MaterialServiceInterface $materialService,
        protected QuestionListingServiceInterface $questionListingService,
        protected ProgressRepositoryInterface $progressRepo,
        protected PerformanceServiceInterface $performanceService,
        protected FactGatheringServiceInterface $factGatheringService,
        protected AdaptiveEngineServiceInterface $adaptiveEngineService,
        protected GuestProgressServiceInterface $guestProgressService,
        protected QuestionAnswerServiceInterface $questionAnswerService,
    ) {}

    protected function getPerformanceService(): PerformanceServiceInterface
    {
        return $this->performanceService;
    }

    protected function getGuestProgressService(): GuestProgressServiceInterface
    {
        return $this->guestProgressService;
    }

    public function index(): Response
    {
        $isGuest       = $this->isGuest();
        $userId        = $this->getUserId();
        $guestProgress = $this->getGuestProgress();

        $unlockedModules = [];
        if (! $isGuest) {
            $studentState    = $this->performanceService->getStudentState($userId);
            $unlockedModules = $studentState?->unlocked_modules ?? [];
        }

        $materials = $this->questionListingService->getMaterialsListWithStudentCount(
            $userId,
            $isGuest,
            $guestProgress,
            $unlockedModules,
        );

        return $this->render('Mahasiswa/Materials/Questions/Index', compact('materials', 'isGuest'));
    }

    public function show(
        int|string $materialId,
        ?string $sub_material = null,
    ): Response|RedirectResponse {
        $material = $this->materialService->getMaterialById((string) $materialId);
        if (! $material) {
            return redirect()->route('mahasiswa.materials.questions.index')
                ->with('error', 'Material tidak ditemukan');
        }

        $isGuest          = $this->isGuest();
        $guestProgress    = $this->getGuestProgress();
        $userId           = $this->getUserId();
        $targetDifficulty = null;

        $studentStateData = $this->resolveStudentStateData(
            $isGuest,
            $userId,
            $materialId,
            $targetDifficulty,
        );

        $data = $this->questionListingService->getQuizData(
            material: $material,
            difficulty: null,
            userId: $userId,
            isGuest: $isGuest,
            guestProgress: $guestProgress,
            subMaterialId: $sub_material,
            targetDifficulty: $targetDifficulty,
        );

        return $this->render('Mahasiswa/Materials/Questions/Show/Index', array_merge($data, [
            'isGuest'      => $isGuest,
            'studentState' => $studentStateData,
        ]));
    }

    public function levels(int|string $materialId): Response|RedirectResponse
    {
        $material = $this->materialService->getMaterialById((string) $materialId);
        if (! $material) {
            return redirect()->route('mahasiswa.materials.questions.index')
                ->with('error', 'Material tidak ditemukan');
        }

        $isGuest       = $this->isGuest();
        $userId        = $this->getUserId();
        $guestProgress = $this->getGuestProgress();

        $answeredQuestionIds = $isGuest
            ? $this->questionListingService->getGuestAnsweredQuestionIds(
                $material->id,
                $guestProgress,
            )
            : $this->progressRepo->getAnsweredQuestionIds($userId, $material->id);

        $levels = $this->questionListingService->getLevelProgress(
            $material,
            null,
            $answeredQuestionIds,
            $isGuest,
        );

        return $this->render('Mahasiswa/Materials/Questions/Levels/Index', compact('material', 'levels'));
    }

    public function review(int|string $materialId, ReviewQuestionRequest $request): Response
    {
        $material         = $this->materialService->getMaterialWithQuestionsAndAnswers((string) $materialId);
        $materials        = $this->materialService->getAllOrdered();
        $difficulty       = QuestionDifficulty::tryFrom($request->validated('difficulty')) ?? null;

        $isGuest       = $this->isGuest();

        $questionsData = $this->questionListingService->getReviewQuestions(
            $material,
            $difficulty,
            $this->getUserId(),
            $isGuest,
            $this->getGuestProgress(),
        );

        return $this->render('Mahasiswa/Materials/Questions/Review/Index', [
            'questions'  => $questionsData,
            'material'   => $material,
            'materials'  => $materials,
            'difficulty' => $difficulty ? $difficulty->value : 'all',
            'isGuest'    => $isGuest,
        ]);
    }

    public function getAttempts(
        int|string $materialId,
        int|string $questionId,
    ): JsonResponse {
        $isGuest = $this->isGuest();

        if ($isGuest) {
            $progressKey   = $materialId . '_' . $questionId;
            $guestProgress = $this->getGuestProgress();
            $attempts      = $guestProgress[$progressKey]['attempt_number'] ?? 0;
        } else {
            $attempts = $this->progressRepo->getAttemptCount(
                Auth::id(),
                (string) $materialId,
                (string) $questionId,
            );
        }

        return $this->json(['attempts' => $attempts]);
    }

    public function checkAnswer(
        int|string $materialId,
        int|string $questionId,
        CheckAnswerRequest $request,
    ): JsonResponse {
        $material = $this->materialService->getMaterialById((string) $materialId);
        if (! $material) {
            return $this->json(['error' => 'Material tidak ditemukan'], 404);
        }

        $userId = $this->getUserId();

        // 0. Ambil Question & Validasi Jawaban di Server
        $question = Question::findOrFail($questionId);
        $isCorrect = $this->questionAnswerService->determineCorrectness($question, $request->validated());
        $score     = $isCorrect ? 100 : 0; // Default score logic

        // 1. Simpan Attempt
        $this->progressRepo->saveProgress([
            'user_id'     => $userId,
            'material_id' => (string) $materialId,
            'question_id' => (string) $questionId,
            'score'       => $score,
            'time_spent'  => (int) ($request->validated('time_spent') ?? 0),
            'is_correct'  => $isCorrect,
            'difficulty'  => (string) ($request->validated('difficulty') ?? 'beginner'),
            'used_hint'   => (bool) ($request->validated('used_hint') ?? false),
        ]);

        // 2. Update Student State Metrics
        $this->performanceService->updateStudentPerformance(
            userId: (string) $userId,
            isCorrect: $isCorrect,
            timeSpent: (int) $request->validated('time_spent'),
            usedHint: (bool) $request->validated('used_hint'),
        );

        // 3. Pencari Fakta
        $studentState = StudentState::where('user_id', $userId)->first() ?? new StudentState(['user_id' => $userId]);
        $facts        = $this->factGatheringService->gatherFacts(
            studentState: $studentState,
            isCorrect: $isCorrect,
            usedHint: (bool) $request->validated('used_hint'),
            score: $score,
            timeSpent: (int) $request->validated('time_spent'),
            difficulty: (string) $request->validated('difficulty'),
            questionId: (string) $questionId,
            materialId: (string) $materialId,
            moduleId: (string) $material->module_id,
        );

        // 4. Aksi (Engine Evaluation)
        $engineResult = $this->adaptiveEngineService->evaluate(
            $facts,
            $studentState->toArray(),
            [
                'material_id' => (string) $materialId,
                'is_correct'  => $isCorrect,
            ],
        );

        // 5. Update State di DB (Map flat columns)
        if (! empty($engineResult['new_state'])) {
            $studentState->fill($engineResult['new_state']);
            $studentState->save();
        }

        // 6. Main Orchestrator: Resolve UI Action
        $action     = $engineResult['triggered_rule']['action'] ?? $engineResult['new_state']['next_action'] ?? AC::ACTION_NEXT_QUESTION;
        $uiResponse = $this->resolveAdaptiveAction($action, (string) $materialId, $engineResult, $isCorrect);

        return $this->json([
            'status'         => $isCorrect ? 'success' : 'error',
            'message'        => $engineResult['triggered_rule']['message'] ?? ($isCorrect ? 'Jawaban Benar!' : 'Belum Tepat'),
            'nextUrl'        => $uiResponse['url'] ?? route('mahasiswa.materials.questions.show', $materialId),
            'xpEarned'       => $score,
            'isCorrect'      => $isCorrect,
            'adaptiveResult' => $engineResult,
            'ui'             => $uiResponse,
        ]);
    }

    private function resolveAdaptiveAction(string $action, string $materialId, array $engineResult, bool $isCorrect): array
    {
        $material = $this->materialService->getMaterialById($materialId);
        $inst     = $engineResult['triggered_rule'] ?? [];

        $base = match ($action) {
            AC::ACTION_FINISH_MATERIAL => [
                'type'  => 'redirect',
                'url'   => route('mahasiswa.materials.questions.index'),
                'label' => $inst['label'] ?? 'Selesai Material',
            ],
            AC::ACTION_REVISE_PROJECT => [
                'type'  => 'modal',
                'url'   => route('mahasiswa.materials.questions.levels', $material),
                'label' => $inst['label'] ?? 'Revisi Materi',
            ],
            AC::ACTION_ISSUE_CERTIFICATE => [
                'type'  => 'confetti',
                'url'   => route('mahasiswa.dashboard'),
                'label' => $inst['label'] ?? 'Selamat! Kamu dapat Sertifikat',
            ],
            default => [
                'type'  => 'continue',
                'url'   => route('mahasiswa.materials.questions.show', [
                    'material' => $material,
                    'difficulty' => $engineResult['new_state']['target_difficulty'] ?? null
                ]),
                'label' => $inst['label'] ?? 'Lanjut',
            ]
        };

        return array_merge($base, [
            'title'   => $inst['title'] ?? ($isCorrect ? 'Luar Biasa!' : 'Belum Tepat'),
            'message' => $inst['message'] ?? null,
        ]);
    }

    public function getTargetDifficulty(int|string $materialId): JsonResponse
    {
        $userId       = Auth::id();
        $studentState = $this->performanceService->getStudentState($userId);

        if (! $studentState) {
            return $this->json(['target_difficulty' => null]);
        }

        // Reset navigation if user switched material
        if ($studentState->current_material_id             !== null
            && (string) $studentState->current_material_id !== (string) $materialId
        ) {
            $this->performanceService->resetMaterialMetrics($userId);
            $studentState->target_difficulty   = null;
            $studentState->current_material_id = null;
        }

        return $this->json(['target_difficulty' => $studentState->target_difficulty]);
    }
}
