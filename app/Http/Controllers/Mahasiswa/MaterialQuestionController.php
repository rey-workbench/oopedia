<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Services\GuestProgressServiceInterface;
use App\Contracts\Services\MaterialServiceInterface;
use App\Contracts\Services\PerformanceServiceInterface;
use App\Contracts\Services\QuizServiceInterface;
use App\Enums\Lms\QuestionDifficulty;
use App\Http\Controllers\Controller;
use App\Http\Requests\Question\CheckAnswerRequest;
use App\Http\Requests\Question\ReviewQuestionRequest;
use App\Services\Adaptive\AdaptiveResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

/**
 * Material Question Controller.
 * Handles the student's journey through quiz questions.
 * Follows Clean Code: Orchestrates services without business logic leak.
 */
final class MaterialQuestionController extends Controller
{
    public function __construct(
        private readonly MaterialServiceInterface $materialService,
        private readonly QuizServiceInterface $quizService,
        private readonly ProgressRepositoryInterface $progressRepo,
        private readonly PerformanceServiceInterface $performanceService,
        private readonly GuestProgressServiceInterface $guestProgressService,
        private readonly AdaptiveResponseService $adaptiveResponseService,
    ) {}

    public function index(): Response
    {
        $userId  = (string) Auth::id();
        $isGuest = Auth::guest();

        $unlockedModules = $isGuest ? [] : ($this->performanceService->getStudentState($userId)->unlocked_modules ?? []);

        $data = $this->quizService->getMaterialsListWithStudentCount(
            userId: $userId,
            isGuest: $isGuest,
            guestProgress: $isGuest ? $this->guestProgressService->getProgress() : [],
            unlockedModules: $unlockedModules,
        );

        return $this->render('Mahasiswa/Materials/Questions/Index', ['materials' => $data]);
    }

    public function levels(string $materialId): Response
    {
        $material = $this->getMaterialOrAbort($materialId);
        $userId   = (string) Auth::id();
        $isGuest  = Auth::guest();

        $unlockedModules = $isGuest ? [] : ($this->performanceService->getStudentState($userId)->unlocked_modules ?? []);
        $answeredIds     = $isGuest
            ? $this->quizService->getGuestAnsweredQuestionIds($material->id, $this->guestProgressService->getProgress())
            : $this->progressRepo->getAnsweredQuestionIds($userId, $material->id);

        return $this->render('Mahasiswa/Materials/Questions/Levels/Index', [
            'material'  => $material,
            'levels'    => $this->quizService->getLevelProgress($material, null, $answeredIds, $isGuest),
            'materials' => $this->quizService->getMaterialsListWithStudentCount($userId, $isGuest, $isGuest ? $this->guestProgressService->getProgress() : [], $unlockedModules),
        ]);
    }

    public function show(string $materialId, ?string $difficulty = null): Response|RedirectResponse
    {
        $material = $this->getMaterialOrAbort($materialId);
        $userId   = (string) Auth::id();
        $isGuest  = Auth::guest();

        // Clean Context Management: Move reset/sync logic to Service
        $targetDifficulty = null;
        if (! $isGuest) {
            $state            = $this->performanceService->syncMaterialContext($userId, $materialId);
            $targetDifficulty = $state->target_difficulty ? QuestionDifficulty::tryFrom((string) $state->target_difficulty) : null;
        }

        $quizData = $this->quizService->getQuizData(
            material: $material,
            difficulty: QuestionDifficulty::tryFrom((string) $difficulty),
            userId: $userId,
            isGuest: $isGuest,
            guestProgress: $isGuest ? $this->guestProgressService->getProgress() : [],
            subMaterialId: QuestionDifficulty::tryFrom((string) $difficulty) ? null : $difficulty,
            targetDifficulty: $targetDifficulty,
        );

        if ($quizData['currentQuestion'] === null && $quizData['answeredCount'] > 0) {
            return redirect()->route('mahasiswa.materials.questions.review', ['material' => $materialId, 'difficulty' => $difficulty]);
        }

        return $this->render('Mahasiswa/Materials/Questions/Show/Index', $quizData);
    }

    public function checkAnswer(CheckAnswerRequest $request, string $materialId, string $questionId): JsonResponse
    {
        $this->getMaterialOrAbort($materialId);

        $result = $this->quizService->handleSubmission(
            userId: (string) Auth::id(),
            materialId: $materialId,
            questionId: $questionId,
            validatedData: $request->validated(),
        );

        $isCorrect = $result['is_correct'];
        $ui        = $this->adaptiveResponseService->resolveUiResponse($result['engine_result'], $materialId, $isCorrect);

        return $this->json([
            'status'         => $isCorrect ? 'success' : 'error',
            'message'        => $result['engine_result']['triggered_rule']['message'] ?? ($isCorrect ? 'Jawaban Benar!' : 'Belum Tepat'),
            'nextUrl'        => $ui['url'],
            'xpEarned'       => $result['score'],
            'isCorrect'      => $isCorrect,
            'adaptiveResult' => $result['engine_result'],
            'ui'             => $ui,
            'studentState'   => Auth::guest() ? null : $this->performanceService->getStudentSessionState((string) Auth::id()),
        ]);
    }

    public function review(ReviewQuestionRequest $request, string $materialId): Response
    {
        $material = $this->getMaterialOrAbort($materialId);

        $questions = $this->quizService->getReviewQuestions(
            material: $material,
            difficulty: QuestionDifficulty::tryFrom((string) $request->difficulty),
            userId: (string) Auth::id(),
            isGuest: Auth::guest(),
            guestProgress: Auth::guest() ? $this->guestProgressService->getProgress() : [],
        );

        return $this->render('Mahasiswa/Materials/Questions/Review/Index', [
            'material'   => $material,
            'questions'  => $questions,
            'difficulty' => $request->difficulty ?? 'all',
        ]);
    }

    private function getMaterialOrAbort(string $id)
    {
        $material = $this->materialService->getMaterialById($id);
        if (! $material) {
            abort(404);
        }

        return $material;
    }

    public function getTargetDifficulty(int|string $materialId): JsonResponse
    {
        $userId = (string) Auth::id();
        $state  = $this->performanceService->getStudentState($userId);

        if (! $state) {
            return $this->json(['target_difficulty' => null]);
        }

        // Auto-sync context also available here
        $state = $this->performanceService->syncMaterialContext($userId, (string) $materialId);

        return $this->json(['target_difficulty' => $state->target_difficulty]);
    }
}
