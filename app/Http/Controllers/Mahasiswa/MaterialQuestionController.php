<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Services\GuestProgressServiceInterface;
use App\Contracts\Services\MaterialServiceInterface;
use App\Contracts\Services\PerformanceServiceInterface;
use App\Contracts\Services\QuizServiceInterface;
use App\DTOs\Quiz\QuizContextDTO;
use App\DTOs\Quiz\QuizSubmissionDTO;
use App\Enums\Lms\QuestionDifficulty;
use App\Http\Controllers\Controller;
use App\Http\Requests\Question\CheckAnswerRequest;
use App\Http\Requests\Question\ReviewQuestionRequest;
use App\Models\AdaptiveRule;
use App\Models\Material;
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
        private readonly ProgressRepositoryInterface $progressRepository,
        private readonly PerformanceServiceInterface $performanceService,
        private readonly GuestProgressServiceInterface $guestProgressService,
    ) {}

    public function index(): Response
    {
        $userId  = (string) Auth::id();
        $isGuest = Auth::guest();

        $data = $this->quizService->getMaterialsListWithStudentCount(
            userId: $userId,
            isGuest: $isGuest,
            guestProgress: $isGuest ? $this->guestProgressService->getProgress() : [],
        );

        return $this->render('Mahasiswa/Materials/Questions/Index', ['materials' => $data]);
    }

    public function levels(string $materialId): Response
    {
        $material = $this->getMaterialOrAbort($materialId);
        $userId   = (string) Auth::id();
        $isGuest  = Auth::guest();

        $answeredIds = $isGuest
            ? $this->quizService->getGuestAnsweredQuestionIds($material->id, $this->guestProgressService->getProgress(), true)
            : $this->progressRepository->getAnsweredQuestionIds($userId, $material->id);

        return $this->render('Mahasiswa/Materials/Questions/Levels/Index', [
            'material'  => $material,
            'levels'    => $this->quizService->getLevelProgress($material, null, $answeredIds, $isGuest),
            'materials' => $this->quizService->getMaterialsListWithStudentCount($userId, $isGuest, $isGuest ? $this->guestProgressService->getProgress() : []),
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
            return redirect()->route('mahasiswa.materials.questions.review', ['material' => $materialId, 'difficulty' => $difficulty]);
        }

        return $this->render('Mahasiswa/Materials/Questions/Show/Index', $quizData);
    }

    public function checkAnswer(CheckAnswerRequest $checkAnswerRequest, string $materialId, string $questionId): RedirectResponse
    {
        $this->getMaterialOrAbort($materialId);

        $quizSubmissionDTO = QuizSubmissionDTO::fromRequest(
            userId: (string) Auth::id(),
            materialId: $materialId,
            questionId: $questionId,
            data: $checkAnswerRequest->validated(),
        );

        $result = $this->quizService->handleSubmission($quizSubmissionDTO);

        $isCorrect    = $result['is_correct'];
        $engineResult = $result['engine_result'];

        // 1. Determine primary adaptive action and next navigation
        $rawActionIds = array_map(
            fn ($r) => is_array($r) ? $r['id'] : $r,
            $result['raw_recommendations'] ?? [],
        );

        $primaryActionId  = $rawActionIds === [] ? 'FEEDBACK' : $rawActionIds[0];
        $hasCertification = in_array('CERTIFICATION', $rawActionIds);
        $shouldRemedial   = in_array('REMEDIAL', $rawActionIds);

        $studentStateData = Auth::guest()
            ? $this->guestProgressService->getStudentState()->toArray()
            : $this->performanceService->getStudentSessionState((string) Auth::id());

        // 2. Resolve Next Navigation Target
        if ($hasCertification) {
            $nextUrl = route('mahasiswa.certificates.index');
            $uiLabel = 'Lihat Sertifikat';
            $uiType  = 'success';
        } elseif ($shouldRemedial) {
            $nextUrl = route('mahasiswa.materials.show', $materialId);
            $uiLabel = 'Pelajari Materi';
            $uiType  = 'warning';
        } else {
            $nextUrl = route('mahasiswa.materials.questions.show', [
                'material'   => $materialId,
                'difficulty' => $studentStateData['target_difficulty'] ?? ($checkAnswerRequest->difficulty ?? 'all'),
            ]);
            $uiLabel = 'Lanjut';
            $uiType  = $isCorrect ? 'success' : 'info';
        }

        // 3. Build triggered_rule
        $ruleChain     = $engineResult['engine_metadata']['rule_chain'] ?? [];
        $finalRuleId   = array_last($ruleChain) ?: ($engineResult['id'] ?? null);
        $triggeredRule = null;

        if ($finalRuleId) {
            $ruleModel = AdaptiveRule::find($finalRuleId);
            if ($ruleModel) {
                // Map Seeder Variant to Frontend FeedbackModal Variant
                $uiVariant = match ($primaryActionId) {
                    'CERTIFICATION' => 'certificate',
                    'REMEDIAL'      => 'backtrack',
                    'INCREASE_DIFF' => 'acceleration',
                    'REDUCE_DIFF'   => 'intervention',
                    'NEW_CHALLENGE' => 'acceleration',
                    default         => 'result'
                };

                $triggeredRule = [
                    'id'       => $ruleModel->id,
                    'name'     => $ruleModel->name,
                    'action'   => $primaryActionId,
                    'priority' => $ruleModel->priority,
                    'variant'  => $uiVariant,
                    'message'  => $ruleModel->recommendation,
                    'title'    => $isCorrect ? 'Berhasil!' : 'Belum Tepat',
                ];
            }
        }

        $feedback = [
            'status'          => $isCorrect ? 'success' : 'error',
            'message'         => $isCorrect ? 'Jawaban Benar!' : 'Belum Tepat',
            'xp_earned'       => $result['score'],
            'is_correct'      => $isCorrect,
            'adaptive_result' => array_merge($engineResult, [
                'triggered_rule'     => $triggeredRule,
                'recommendation_ids' => $rawActionIds,
            ]),
            'next_url'        => $nextUrl,
            'ui'              => [
                'label'   => $uiLabel,
                'type'    => $uiType,
                'message' => $shouldRemedial ? 'Kamu perlu mengulas materi ini kembali.' : null,
            ],
        ];

        return back()->with('feedback', $feedback)->with('student_state', $studentStateData);
    }

    public function review(ReviewQuestionRequest $reviewQuestionRequest, string $materialId): Response
    {
        $material = $this->getMaterialOrAbort($materialId);

        $quizContextDTO = new QuizContextDTO(
            material: $material,
            difficulty: QuestionDifficulty::tryFrom((string) $reviewQuestionRequest->difficulty),
            userId: (string) Auth::id(),
            isGuest: Auth::guest(),
            guestProgress: Auth::guest() ? $this->guestProgressService->getProgress() : [],
        );

        $questions = $this->quizService->getReviewQuestions($quizContextDTO);

        return $this->render('Mahasiswa/Materials/Questions/Review/Index', [
            'material'   => $material,
            'questions'  => $questions,
            'difficulty' => $reviewQuestionRequest->difficulty ?? 'all',
        ]);
    }

    private function getMaterialOrAbort(string $id)
    {
        $material = $this->materialService->getMaterialById($id);
        if (! $material instanceof Material) {
            abort(404);
        }

        return $material;
    }

    public function useHint(): RedirectResponse
    {
        $userId  = (string) Auth::id();
        $isGuest = Auth::guest();

        if ($isGuest) {
            return back()->with('student_state');
        }

        $this->performanceService->decrementHint($userId);

        return back()->with('student_state', $this->performanceService->getStudentSessionState($userId));
    }
}
