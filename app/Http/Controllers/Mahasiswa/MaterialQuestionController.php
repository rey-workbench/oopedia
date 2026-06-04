<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Services\GuestProgressServiceInterface;
use App\Contracts\Services\PerformanceServiceInterface;
use App\Contracts\Services\QuizServiceInterface;
use App\DTOs\Quiz\MaterialProgressDTO;
use App\DTOs\Quiz\QuizContextDTO;
use App\Enums\Lms\QuestionDifficulty;
use App\Http\Controllers\Controller;
use App\Http\Requests\Question\ReviewQuestionRequest;
use App\Http\Resources\MaterialResource;
use App\Models\Material;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

/**
 * Material Question Controller.
 * Handles the student's journey through quiz questions (Page Rendering).
 */
final class MaterialQuestionController extends Controller
{
    public function __construct(
        private readonly QuizServiceInterface $quizService,
        private readonly ProgressRepositoryInterface $progressRepository,
        private readonly PerformanceServiceInterface $performanceService,
        private readonly GuestProgressServiceInterface $guestProgressService,
        private readonly MaterialRepositoryInterface $materialRepository,
    ) {}

    public function index(): Response
    {
        $userId  = (string) $this->getUserId();
        $isGuest = $this->isGuest();

        $data = $this->quizService->getMaterialsListWithStudentCount(
            new MaterialProgressDTO(
                userId: $userId,
                isGuest: $isGuest,
                guestProgress: $isGuest ? $this->getGuestProgress() : [],
            ),
        );

        return $this->render('Mahasiswa/Materials/Questions/Index', ['materials' => $data]);
    }

    public function levels(string $materialId): Response
    {
        $material = $this->getMaterialOrAbort($materialId);
        $userId   = (string) $this->getUserId();
        $isGuest  = $this->isGuest();

        $answeredIds = $isGuest
            ? $this->quizService->getGuestAnsweredQuestionIds($material->id, $this->getGuestProgress(), true)
            : $this->progressRepository->getAnsweredQuestionIds($userId, $material->id);

        return $this->render('Mahasiswa/Materials/Questions/Levels/Index', [
            'material'  => new MaterialResource($material)->resolve(),
            'levels'    => $this->quizService->getLevelProgress($material, null, $answeredIds, $isGuest),
            'materials' => $this->quizService->getMaterialsListWithStudentCount(
                new MaterialProgressDTO(
                    userId: $userId,
                    isGuest: $isGuest,
                    guestProgress: $isGuest ? $this->guestProgressService->getProgress() : [],
                ),
            ),
        ]);
    }

    public function show(string $materialId, ?string $difficulty = null): Response|RedirectResponse
    {
        $material = $this->getMaterialOrAbort($materialId);
        $userId   = (string) $this->getUserId();
        $isGuest  = $this->isGuest();

        $targetDifficulty = null;

        if (! $isGuest) {
            $state            = $this->performanceService->syncMaterialContext($userId, $materialId);
            $targetDifficulty = $state->target_difficulty ? QuestionDifficulty::tryFrom((string) $state->target_difficulty) : null;
        }

        $quizContextDTO = new QuizContextDTO(
            material: $material,
            difficulty: QuestionDifficulty::tryFrom((string) $difficulty),
            userId: $userId,
            isGuest: $isGuest,
            guestProgress: $isGuest ? $this->guestProgressService->getProgress() : [],
            targetDifficulty: $targetDifficulty,
        );

        $quizData = $this->quizService->getQuizData($quizContextDTO);

        if ($quizData['current_question'] === null && $quizData['answered_count'] > 0) {
            return to_route('mahasiswa.materials.questions.review', ['material' => $materialId, 'difficulty' => $difficulty]);
        }

        return $this->render('Mahasiswa/Materials/Questions/Show/Index', $quizData);
    }

    public function review(ReviewQuestionRequest $reviewQuestionRequest, string $materialId): Response
    {
        $material = $this->getMaterialOrAbort($materialId);

        $quizContextDTO = new QuizContextDTO(
            material: $material,
            difficulty: QuestionDifficulty::tryFrom((string) $reviewQuestionRequest->difficulty),
            userId: (string) $this->getUserId(),
            isGuest: $this->isGuest(),
            guestProgress: $this->isGuest() ? $this->getGuestProgress() : [],
        );

        $questions = $this->quizService->getReviewQuestions($quizContextDTO);

        return $this->render('Mahasiswa/Materials/Questions/Review/Index', [
            'material'   => new MaterialResource($material)->resolve(),
            'questions'  => $questions,
            'difficulty' => $reviewQuestionRequest->difficulty ?? 'all',
        ]);
    }

    private function getMaterialOrAbort(string $id): Material
    {
        $material = $this->materialRepository->find($id);
        if (! $material instanceof Material) {
            abort(404);
        }

        return $material;
    }
}
