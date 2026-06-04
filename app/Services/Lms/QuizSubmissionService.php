<?php

declare(strict_types=1);

namespace App\Services\Lms;

use App\Contracts\Repositories\AdaptiveActionRepositoryInterface;
use App\Contracts\Repositories\AdaptiveExecutionLogRepositoryInterface;
use App\Contracts\Repositories\AdaptiveFactRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Contracts\Services\AdaptiveActionProcessorInterface;
use App\Contracts\Services\AdaptiveEngineServiceInterface;
use App\Contracts\Services\GuestProgressServiceInterface;
use App\Contracts\Services\PerformanceServiceInterface;
use App\Contracts\Services\QuizSubmissionServiceInterface;
use App\DTOs\Adaptive\StudentStateDTO;
use App\DTOs\Quiz\InteractionDTO;
use App\DTOs\Quiz\QuizSubmissionDTO;
use App\DTOs\User\PerformanceScoreDTO;
use App\Enums\Lms\QuestionType;
use App\Enums\User\RoleName;
use App\Models\AdaptiveFact;
use App\Models\Question;
use App\Models\StudentState;
use Illuminate\Support\Facades\DB;

final readonly class QuizSubmissionService implements QuizSubmissionServiceInterface
{
    public function __construct(
        private QuestionRepositoryInterface $questionRepository,
        private ProgressRepositoryInterface $progressRepository,
        private PerformanceServiceInterface $performanceService,
        private AdaptiveEngineServiceInterface $adaptiveEngineService,
        private AdaptiveActionProcessorInterface $adaptiveActionProcessor,
        private GuestProgressServiceInterface $guestProgressService,
        private AdaptiveFactRepositoryInterface $adaptiveFactRepository,
        private AdaptiveActionRepositoryInterface $adaptiveActionRepository,
        private AdaptiveExecutionLogRepositoryInterface $adaptiveExecutionLogRepository,
    ) {}

    public function determineCorrectness(Question $question, array $data): bool
    {
        if ($question->question_type === QuestionType::RADIO_BUTTON) {
            if (! isset($data['answer'])) {
                return false;
            }

            $selected = $question->answers()->where('id', $data['answer'])->first();

            return $selected && $selected->is_correct;
        }

        if ($question->question_type === QuestionType::FILL_IN_THE_BLANK) {
            $answer = trim(strtolower($data['fill_in_the_blank_answer'] ?? ''));
            if ($answer === '' || $answer === '0') {
                return false;
            }

            return $question->answers()->where('is_correct', true)->get()->contains(fn ($ans): bool => trim(strtolower((string) $ans->answer_text)) === $answer);
        }

        if ($question->question_type === QuestionType::DRAG_AND_DROP) {
            $userAnswersStr = $data['drag_and_drop_answers'] ?? '[]';
            $userAnswers    = is_array($userAnswersStr) ? $userAnswersStr : json_decode((string) $userAnswersStr, true);
            if (empty($userAnswers)) {
                return false;
            }

            $correctAnswers = $question->answers()->whereNotNull('drag_target')->get();
            if ($correctAnswers->isEmpty()) {
                return false;
            }

            foreach ($correctAnswers as $correctAnswer) {
                if (trim($userAnswers[$correctAnswer->drag_target] ?? '') !== trim((string) $correctAnswer->answer_text)) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    public function handleSubmission(QuizSubmissionDTO $quizSubmissionDTO): array
    {
        return DB::transaction(function () use ($quizSubmissionDTO): array {
            $question  = $this->questionRepository->findWithMaterial($quizSubmissionDTO->questionId);
            if (! $question instanceof Question) {
                abort(404, 'Pertanyaan tidak ditemukan.');
            }

            $isCorrect = $this->determineCorrectness($question, $quizSubmissionDTO->toArray());

            $score = $this->performanceService->calculateScore(
                new PerformanceScoreDTO(
                    isCorrect: $isCorrect,
                    usedHint: $quizSubmissionDTO->usedHint,
                    timeSpent: $quizSubmissionDTO->timeSpent,
                    difficulty: $question->difficulty,
                ),
            );

            $userResponse = null;
            $answerId     = null;
            if ($question->question_type === QuestionType::RADIO_BUTTON) {
                $answerId = $quizSubmissionDTO->answer;
            } elseif ($question->question_type === QuestionType::FILL_IN_THE_BLANK) {
                $userResponse = $quizSubmissionDTO->fillInTheBlankAnswer;
            } elseif ($question->question_type === QuestionType::DRAG_AND_DROP) {
                $userResponse = is_array($quizSubmissionDTO->dragAndDropAnswers) ? json_encode($quizSubmissionDTO->dragAndDropAnswers) : $quizSubmissionDTO->dragAndDropAnswers;
            }

            $this->progressRepository->saveProgress([
                'user_id'       => $quizSubmissionDTO->userId ?: RoleName::GUEST->value,
                'material_id'   => $quizSubmissionDTO->materialId,
                'question_id'   => $quizSubmissionDTO->questionId,
                'is_correct'    => $isCorrect,
                'score'         => $score,
                'time_spent'    => $quizSubmissionDTO->timeSpent,
                'used_hint'     => $quizSubmissionDTO->usedHint,
                'answer_id'     => $answerId,
                'user_response' => $userResponse,
            ]);

            if ($quizSubmissionDTO->userId === '' || $quizSubmissionDTO->userId === RoleName::GUEST->value) {
                $this->guestProgressService->saveProgress(
                    ['material_id' => $quizSubmissionDTO->materialId],
                    $isCorrect,
                    $quizSubmissionDTO->questionId,
                );
            }

            $interactionDTO = new InteractionDTO(
                userId: $quizSubmissionDTO->userId,
                questionId: $quizSubmissionDTO->questionId,
                isCorrect: $isCorrect,
                timeSpent: $quizSubmissionDTO->timeSpent,
                difficulty: $question->difficulty,
                usedHint: $quizSubmissionDTO->usedHint,
                score: $score,
            );

            $studentState = $this->performanceService->updateMetricsFromInteraction($interactionDTO);

            $engineResult = $this->adaptiveEngineService->evaluate(
                StudentStateDTO::fromArray($studentState->toArray()),
            );

            $this->logAndApplyAdaptiveResult($quizSubmissionDTO->userId, $quizSubmissionDTO->materialId, $engineResult->toArray(), $studentState, $isCorrect);

            $diagnosisFact     = $this->adaptiveFactRepository->find($engineResult->diagnosis);
            $friendlyDiagnosis = $diagnosisFact instanceof AdaptiveFact ? $diagnosisFact->name : $engineResult->diagnosis;

            $actionModels    = $this->adaptiveActionRepository->findByIds($engineResult->actions);
            $hydratedActions = array_map(fn ($id): array => [
                'id'      => $id,
                'name'    => $actionModels->get($id)?->name       ?? str_replace('_', ' ', $id),
                'variant' => $actionModels->get($id)?->variant    ?? 'feedback',
            ], $engineResult->actions);

            $explanation = null;
            if ($question->question_type === QuestionType::RADIO_BUTTON && $answerId) {
                $selectedAnswer = $question->answers->firstWhere('id', $answerId);
                $explanation    = $selectedAnswer?->explanation;
            } elseif ($question->question_type === QuestionType::FILL_IN_THE_BLANK) {
                $correctAnswer = $question->answers->where('is_correct', true)->first();
                $explanation   = $correctAnswer?->explanation;
            } elseif ($question->question_type === QuestionType::DRAG_AND_DROP) {
                $explanation = $question->answers->first()?->explanation;
            }

            $correctAnswerData = null;
            if (! $isCorrect && $question->question_type === QuestionType::RADIO_BUTTON) {
                $correctOption = $question->answers->where('is_correct', true)->first();
                if ($correctOption) {
                    $correctAnswerData = [
                        'id'   => $correctOption->id,
                        'text' => $correctOption->answer_text,
                    ];
                }
            }

            return [
                'is_correct'         => $isCorrect,
                'score'              => $score,
                'explanation'        => $explanation,
                'correct_answer'     => $correctAnswerData,
                'challenge_question' => null,
                'engine_result'      => array_merge($engineResult->toArray(), [
                    'diagnosis'     => $friendlyDiagnosis,
                    'actions'       => $hydratedActions,
                    'show_guidance' => in_array('SHOW_GUIDANCE', $engineResult->actions),
                    'guidance_data' => in_array('SHOW_GUIDANCE', $engineResult->actions) ? $this->generateGuidanceData($question) : null,
                ]),
            ];
        });
    }

    private function logAndApplyAdaptiveResult(string $userId, string $materialId, array $result, StudentState $studentState, bool $isCorrect): void
    {
        if ($userId !== RoleName::GUEST->value) {
            $this->adaptiveExecutionLogRepository->create([
                'user_id'           => $userId,
                'rule_id'           => $result['id'],
                'action_id'         => implode(', ', array_map(fn ($r) => is_array($r) ? $r['id'] : $r, $result['actions'])),
                'trigger_facts'     => $result['facts'] ?? [],
                'state_deltas'      => [],
                'new_state'         => $studentState->toArray(),
                'execution_context' => [
                    'material_id' => $materialId,
                    'timestamp'   => $result['timestamp'],
                ],
            ]);
        }

        $this->adaptiveActionProcessor->process($studentState, $result['actions'], $materialId, $isCorrect);

        $adaptiveState                   = $studentState->adaptive_state ?? [];
        $adaptiveState['last_diagnosis'] = $result['diagnosis'];
        $studentState->adaptive_state    = $adaptiveState;

        if ($userId !== RoleName::GUEST->value) {
            $studentState->save();
        }
    }

    private function generateGuidanceData(Question $question): ?array
    {
        if (! $question->relationLoaded('answers')) {
            $question->load('answers');
        }

        if ($question->question_type === QuestionType::RADIO_BUTTON) {
            $wrongAnswer = $question->answers->where('is_correct', false)->random();

            return [
                'type'      => 'remove_option',
                'remove_id' => $wrongAnswer?->id,
            ];
        }

        if ($question->question_type === QuestionType::FILL_IN_THE_BLANK) {
            $rawCorrect = $question->answers->where('is_correct', true)->first()?->answer_text;
            if (! $rawCorrect) {
                return null;
            }

            $correctAns = trim((string) $rawCorrect);
            $characters = [];
            for ($i = 0; $i < mb_strlen($correctAns); $i++) {
                $char = mb_substr($correctAns, $i, 1);
                if ($char === ' ') {
                    $characters[] = ['char' => ' ', 'revealed' => true];
                } elseif ($i % 4 === 0 || $i === mb_strlen($correctAns) - 1) {
                    $characters[] = ['char' => $char, 'revealed' => true];
                } else {
                    $characters[] = ['char' => $char, 'revealed' => false];
                }
            }

            return [
                'type'       => 'tts_hint',
                'characters' => $characters,
            ];
        }

        if ($question->question_type === QuestionType::DRAG_AND_DROP) {
            $firstCorrect = $question->answers->where('is_correct', true)->first();

            return [
                'type'        => 'auto_fill',
                'drag_source' => $firstCorrect?->answer_text,
                'drag_target' => $firstCorrect?->drag_target,
            ];
        }

        return null;
    }
}
