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
        protected QuizServiceInterface $quizService,
        protected ProgressRepositoryInterface $progressRepo,
        protected PerformanceServiceInterface $performanceService,
        protected GuestProgressServiceInterface $guestProgressService,
    ) {
    }

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
        $userId = $this->getUserId();
        $isGuest = $this->isGuest();

        $unlockedModules = [];
        if (!$isGuest) {
            $studentState = $this->performanceService->getStudentState((string)$userId);
            $unlockedModules = $studentState->unlocked_modules ?? [];
        }

        $data = $this->quizService->getMaterialsListWithStudentCount(
            userId: (string) $userId,
            isGuest: $isGuest,
            guestProgress: $this->getGuestProgress(),
            unlockedModules: $unlockedModules
        );

        return $this->render('Mahasiswa/Materials/Questions/Index', [
            'materials' => $data,
        ]);
    }

    public function levels(string $materialId): Response
    {
        $material = $this->materialService->getMaterialById($materialId);
        $userId = $this->getUserId();
        $isGuest = $this->isGuest();

        $unlockedModules = [];
        if (!$isGuest) {
            $studentState = $this->performanceService->getStudentState((string)$userId);
            $unlockedModules = $studentState->unlocked_modules ?? [];
        }

        $answeredQuestionIds = $isGuest
            ? $this->quizService->getGuestAnsweredQuestionIds($material->id, $this->getGuestProgress())
            : $this->progressRepo->getAnsweredQuestionIds((string)$userId, $material->id);

        $levels = $this->quizService->getLevelProgress(
            material: $material,
            difficulty: null,
            answeredQuestionIds: $answeredQuestionIds,
            isGuest: $isGuest
        );

        $materials = $this->quizService->getMaterialsListWithStudentCount(
            userId: (string) $userId,
            isGuest: $isGuest,
            guestProgress: $this->getGuestProgress(),
            unlockedModules: $unlockedModules
        );

        return $this->render('Mahasiswa/Materials/Questions/Levels/Index', [
            'material' => $material,
            'levels' => $levels,
            'materials' => $materials,
        ]);
    }

    public function show(string $materialId, ?string $difficulty = null): Response|RedirectResponse
    {
        $material = $this->materialService->getMaterialById($materialId);
        $userId = (string) $this->getUserId();
        $isGuest = $this->isGuest();

        $targetDifficulty = null;
        if (!$isGuest) {
            $studentState = $this->performanceService->getStudentState($userId);
            
            if ($studentState->current_material_id !== $materialId) {
                $studentState = $this->performanceService->resetMaterialMetrics($userId);
                $studentState->current_material_id = $materialId;
                $studentState->save();
            }

            $targetDifficulty = $studentState->target_difficulty ? QuestionDifficulty::tryFrom((string) $studentState->target_difficulty) : null;
        }

        $diffEnum = QuestionDifficulty::tryFrom((string) $difficulty);
        $subMaterialId = $diffEnum ? null : $difficulty;
        $guestProgress = $this->getGuestProgress();

        $quizData = $this->quizService->getQuizData(
            material: $material,
            difficulty: $diffEnum,
            userId: $userId,
            isGuest: $isGuest,
            guestProgress: $guestProgress,
            subMaterialId: $subMaterialId,
            targetDifficulty: $targetDifficulty
        );

        if ($quizData['currentQuestion'] === null && $quizData['answeredCount'] > 0) {
            return redirect()->route('mahasiswa.materials.questions.review', [
                'material' => $materialId,
                'difficulty' => $difficulty,
            ]);
        }

        return $this->render('Mahasiswa/Materials/Questions/Show/Index', $quizData);
    }

    public function checkAnswer(CheckAnswerRequest $request, string $materialId, string $questionId): JsonResponse
    {
        $material = $this->materialService->getMaterialById($materialId);
        $userId = $this->getUserId();
        $isGuest = $this->isGuest();

        $result = $this->quizService->handleSubmission(
            userId: (string) $userId,
            materialId: (string) $materialId,
            questionId: (string) $questionId,
            validatedData: $request->validated()
        );

        $isCorrect = $result['is_correct'];
        $score = $result['score'];
        $engineResult = $result['engine_result'];

        $action = $engineResult['triggered_rule']['action'] ?? $engineResult['new_state']['next_action'] ?? AC::ACTION_NEXT_QUESTION;
        $uiResponse = $this->resolveAdaptiveAction($action, (string) $materialId, $engineResult, $isCorrect);

        return $this->json([
            'status' => $isCorrect ? 'success' : 'error',
            'message' => $engineResult['triggered_rule']['message'] ?? ($isCorrect ? 'Jawaban Benar!' : 'Belum Tepat'),
            'nextUrl' => $uiResponse['url'] ?? route('mahasiswa.materials.questions.show', $materialId),
            'xpEarned' => $score,
            'isCorrect' => $isCorrect,
            'adaptiveResult' => $engineResult,
            'ui' => $uiResponse,
            'studentState' => $isGuest ? null : $this->performanceService->getStudentSessionState((string) $userId),
        ]);
    }

    public function review(ReviewQuestionRequest $request, string $materialId): Response
    {
        $material = $this->materialService->getMaterialById($materialId);
        $userId = $this->getUserId();
        $isGuest = $this->isGuest();
        $difficulty = QuestionDifficulty::tryFrom((string) $request->difficulty);

        $questions = $this->quizService->getReviewQuestions(
            material: $material,
            difficulty: $difficulty,
            userId: (string) $userId,
            isGuest: $isGuest,
            guestProgress: $this->getGuestProgress()
        );

        return $this->render('Mahasiswa/Materials/Questions/Review/Index', [
            'material' => $material,
            'questions' => $questions,
            'difficulty' => $request->difficulty ?? 'all',
        ]);
    }

    private function resolveAdaptiveAction(string $action, string $materialId, array $engineResult, bool $isCorrect): array
    {
        $material = $this->materialService->getMaterialById($materialId);
        $inst = $engineResult['triggered_rule'] ?? [];

        $base = match ($action) {
            AC::ACTION_FINISH_MATERIAL => [
                'type'  => 'redirect',
                'url'   => route('mahasiswa.materials.questions.index'),
                'label' => $inst['label'] ?? 'Selesai Material',
            ],
            AC::ACTION_NEXT_MATERIAL => [
                'type'  => 'redirect',
                'url'   => route('mahasiswa.materials.index'),
                'label' => $inst['label'] ?? 'Materi Berikutnya',
            ],
            AC::ACTION_REVISE_PROJECT => [
                'type'  => 'modal',
                'url'   => route('mahasiswa.materials.questions.levels', $material),
                'label' => $inst['label'] ?? 'Revisi Materi',
            ],
            AC::ACTION_STUDY_VISUAL,
            AC::ACTION_STUDY_TEXTUAL,
            AC::ACTION_STUDY_THEORY,
            AC::ACTION_STUDY_SYNTAX,
            AC::ACTION_STUDY_MIXED,
            AC::ACTION_STUDY_MATERIAL => [
                'type'  => 'redirect',
                'url'   => route('mahasiswa.materials.show', $material),
                'label' => $inst['label'] ?? 'Lihat Materi',
            ],
            default => [
                'type' => 'continue',
                'url' => route('mahasiswa.materials.questions.show', [
                    'material' => $material,
                    'sub_material' => $engineResult['new_state']['target_difficulty'] ?? null
                ]),
                'label' => $inst['label'] ?? 'Lanjut',
            ]
        };

        return array_merge($base, [
            'title' => $inst['title'] ?? ($isCorrect ? 'Luar Biasa!' : 'Belum Tepat'),
            'message' => $inst['message'] ?? null,
        ]);
    }

    public function getTargetDifficulty(int|string $materialId): JsonResponse
    {
        $userId = Auth::id();
        $studentState = $this->performanceService->getStudentState((string)$userId);

        if (!$studentState) {
            return $this->json(['target_difficulty' => null]);
        }

        if ($studentState->current_material_id !== null && (string)$studentState->current_material_id !== (string)$materialId) {
            $this->performanceService->resetMaterialMetrics((string)$userId);
            $studentState->target_difficulty = null;
            $studentState->current_material_id = null;
        }

        return $this->json(['target_difficulty' => $studentState->target_difficulty]);
    }
}
