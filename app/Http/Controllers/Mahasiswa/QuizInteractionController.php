<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Services\GuestProgressServiceInterface;
use App\Contracts\Services\PerformanceServiceInterface;
use App\Contracts\Services\QuizServiceInterface;
use App\DTOs\Quiz\QuizSubmissionDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Question\CheckAnswerRequest;
use App\Http\Resources\AdaptiveRuleResource;
use App\Models\AdaptiveRule;
use App\Models\Material;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;

final class QuizInteractionController extends Controller
{
    public function __construct(
        private readonly QuizServiceInterface $quizService,
        private readonly PerformanceServiceInterface $performanceService,
        private readonly GuestProgressServiceInterface $guestProgressService,
    ) {}

    /**
     * Handle question submission and process adaptive logic.
     */
    public function submit(CheckAnswerRequest $request, Material $material, Question $question): RedirectResponse
    {
        if ((string) $question->material_id !== (string) $material->id) {
            abort(403, 'Aksi tidak sah: Pertanyaan tidak cocok dengan materi.');
        }

        $userId  = (string) $this->getUserId();
        $isGuest = $this->isGuest();

        $submissionDTO = QuizSubmissionDTO::fromRequest(
            userId: $userId,
            materialId: (string) $material->id,
            questionId: (string) $question->id,
            data: $request->validated(),
        );

        $result       = $this->quizService->handleSubmission($submissionDTO);
        $isCorrect    = $result['is_correct'];
        $engineResult = $result['engine_result'];

        // 1. Prepare Student State for Frontend
        $studentStateData = $isGuest
            ? $this->guestProgressService->getStudentSessionState()
            : $this->performanceService->getStudentSessionState($userId);

        // 2. Determine UI Behavior from State (populated by AdaptiveActionProcessor)
        $nextUrl = $studentStateData['adaptive_engine']['adaptive_state']['next_url'] ?? null;

        // 3. Build triggered_rule for the modal
        $ruleChain     = $engineResult['engine_metadata']['rule_chain'] ?? [];
        $finalRuleId   = array_last($ruleChain) ?: ($engineResult['id'] ?? null);
        $triggeredRule = null;

        if ($finalRuleId) {
            $ruleModel = AdaptiveRule::find($finalRuleId);
            if ($ruleModel) {
                $triggeredRule = [
                    'rule'    => new AdaptiveRuleResource($ruleModel)->resolve(),
                    'actions' => $engineResult['actions'], // Already hydrated by QuizService
                    'title'   => $isCorrect ? 'Berhasil!' : 'Belum Tepat',
                ];
            }
        }

        $triggeredRules = [];
        if (! empty($ruleChain)) {
            $rulesModels = AdaptiveRule::whereIn('id', $ruleChain)->get()->keyBy('id');
            foreach ($ruleChain as $id) {
                if (isset($rulesModels[$id])) {
                    $triggeredRules[] = [
                        'rule'    => new AdaptiveRuleResource($rulesModels[$id])->resolve(),
                        'actions' => [], // Actions are usually only for the final rule
                    ];
                }
            }
        }

        $feedback = [
            'status'          => $isCorrect ? 'success' : 'error',
            'message'         => $result['explanation'] ?? ($isCorrect ? 'Jawaban Benar!' : 'Belum Tepat'),
            'xp_earned'       => $result['score'],
            'is_correct'      => $isCorrect,
            'adaptive_result' => array_merge($engineResult, [
                'triggered_rule'  => $triggeredRule,
                'triggered_rules' => $triggeredRules,
            ]),
            'challenge_question' => $studentStateData['adaptive_engine']['adaptive_state']['challenge_question'] ?? null,
            'next_url'           => $nextUrl,
        ];

        return back()->with('feedback', $feedback)->with('student_state', $studentStateData);
    }

    /**
     * Handle hint usage.
     */
    public function useHint(string $materialId, string $questionId): RedirectResponse
    {
        $userId  = (string) $this->getUserId();
        $isGuest = $this->isGuest();

        if ($isGuest) {
            return back();
        }

        $question = Question::find($questionId);

        if ($question && ! empty($question->hint)) {
            $this->performanceService->decrementHint($userId);
        }

        return back()->with('student_state', $this->performanceService->getStudentSessionState($userId));
    }
}
