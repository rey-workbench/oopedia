<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Services\AdaptiveQuizFlowServiceInterface;
use App\Contracts\Services\GuestProgressServiceInterface;
use App\Contracts\Services\MaterialServiceInterface;
use App\Contracts\Services\PerformanceServiceInterface;
use App\Contracts\Services\QuestionListingServiceInterface;
use App\Contracts\Services\QuestionServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Question;
use App\Traits\HandlesAdaptiveState;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

class MaterialQuestionController extends Controller
{
    use HandlesAdaptiveState;

    public function __construct(
        protected MaterialServiceInterface $materialService,
        protected QuestionServiceInterface $questionService,
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

    public function index(Request $request): Response
    {
        $isGuest       = $this->isGuest();
        $userId        = $this->getUserId();
        $guestProgress = $this->getGuestProgress($request);

        $materials = $this->questionListingService->getMaterialsListWithStudentCount(
            $userId,
            $isGuest,
            $guestProgress,
        );

        return $this->render('Mahasiswa/Materials/Questions/Index', compact('materials', 'isGuest'));
    }

    public function show(
        int|string $materialId,
        Request $request,
        ?string $sub_material = null,
    ): Response|RedirectResponse {
        $material = $this->materialService->getMaterialById((string) $materialId);
        if (! $material) {
            return redirect()->route('mahasiswa.materials.questions.index')
                ->with('error', 'Material tidak ditemukan');
        }

        $isGuest          = $this->isGuest();
        $guestProgress    = $this->getGuestProgress($request);
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
            difficulty: 'all',
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

    public function levels(int|string $materialId, Request $request): Response|RedirectResponse
    {
        $material = $this->materialService->getMaterialById((string) $materialId);
        if (! $material) {
            return redirect()->route('mahasiswa.materials.questions.index')
                ->with('error', 'Material tidak ditemukan');
        }

        $isGuest       = $this->isGuest();
        $userId        = $this->getUserId();
        $guestProgress = $this->getGuestProgress($request);

        $answeredQuestionIds = $isGuest
            ? $this->questionListingService->getGuestAnsweredQuestionIds(
                $material->id,
                $guestProgress,
            )
            : $this->progressRepo->getAnsweredQuestionIds($userId, $material->id);

        $levels = $this->questionListingService->getLevelProgress(
            $material,
            'all',
            $answeredQuestionIds,
            $isGuest,
        );

        return $this->render('Mahasiswa/Materials/Questions/Levels/Index', compact('material', 'levels'));
    }

    public function review(int|string $id, Request $request): Response
    {
        $material      = $this->materialService->getMaterialWithQuestionsAndAnswers((string) $id);
        $materials     = $this->materialService->getAllOrdered();
        $difficulty    = $request->query('difficulty', 'all');
        $isGuest       = $this->isGuest();
        $userId        = $this->getUserId();
        $guestProgress = $this->getGuestProgress($request);

        $questions = $this->questionListingService->getReviewQuestions(
            $material,
            $difficulty,
            $userId,
            $isGuest,
            $guestProgress,
        );

        return $this->render('Mahasiswa/Materials/Questions/Review/Index', [
            'material'   => $material,
            'materials'  => $materials,
            'questions'  => $questions,
            'difficulty' => $difficulty,
            'isGuest'    => $isGuest,
        ]);
    }

    public function getAttempts(
        int|string $materialId,
        int|string $questionId,
        Request $request,
    ): JsonResponse {
        $isGuest = $this->isGuest();

        if ($isGuest) {
            $progressKey   = $materialId . '_' . $questionId;
            $guestProgress = $this->getGuestProgress($request);
            $attempts      = isset($guestProgress[$progressKey])
                ? $guestProgress[$progressKey]['attempt_number']
                : 0;
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
        Request $request,
    ): JsonResponse {
        $material = Material::find($materialId);
        $question = Question::find($questionId);

        if (! $material || ! $question) {
            return $this->json([
                'status'  => 'error',
                'message' => 'Material atau soal tidak ditemukan',
            ], 404);
        }

        $result = $this->adaptiveQuizFlowService->processAdaptiveAttempt(
            $material,
            $question,
            Auth::id(),
            $request->all(),
        );

        return $this->json($result);
    }

    public function getTargetDifficulty(int|string $materialId): JsonResponse
    {
        $userId       = Auth::id();
        $studentState = $this->performanceService->getStudentState($userId);

        if (! $studentState) {
            return $this->json(['target_difficulty' => null]);
        }

        $adaptiveState = $studentState->adaptive_state ?? [];
        if (is_string($adaptiveState)) {
            $adaptiveState = json_decode($adaptiveState, true) ?? [];
        }

        $lastMaterialId = $adaptiveState['current_material_id'] ?? null;
        if ($lastMaterialId !== null && (string) $lastMaterialId !== (string) $materialId) {
            $this->resetMaterialScopedState($studentState, $adaptiveState);
        }

        $targetDifficulty = $adaptiveState['target_difficulty'] ?? null;

        return $this->json(['target_difficulty' => $targetDifficulty]);
    }
}
