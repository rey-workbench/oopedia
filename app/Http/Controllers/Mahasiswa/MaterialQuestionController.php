<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Services\AdaptiveQuizFlowServiceInterface;
use App\Contracts\Services\GuestProgressServiceInterface;
use App\Contracts\Services\MaterialServiceInterface;
use App\Contracts\Services\PerformanceServiceInterface;
use App\Contracts\Services\QuestionListingServiceInterface;
use App\Enums\Lms\QuestionDifficulty;
use App\Http\Controllers\Controller;
use App\Http\Requests\Question\CheckAnswerRequest;
use App\Http\Requests\Question\ReviewQuestionRequest;
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
        protected AdaptiveQuizFlowServiceInterface $adaptiveQuizFlowService,
        protected GuestProgressServiceInterface $guestProgressService,
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
        $result = $this->adaptiveQuizFlowService->processAdaptiveAttemptByIds(
            (string) $materialId,
            (string) $questionId,
            $this->getUserId(),
            $request->validated(),
        );

        $statusCode = (int) ($result['status_code'] ?? 200);
        unset($result['status_code']);

        return $this->json($result, $statusCode);
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
