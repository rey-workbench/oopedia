<?php

declare(strict_types=1);

namespace App\Services\Lms;

use App\Contracts\Repositories\AnswerRepositoryInterface;
use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Contracts\Services\AdaptiveActionProcessorInterface;
use App\Contracts\Services\AdaptiveEngineServiceInterface;
use App\Contracts\Services\GuestProgressServiceInterface;
use App\Contracts\Services\PerformanceServiceInterface;
use App\Contracts\Services\QuizServiceInterface;
use App\DTOs\Adaptive\StudentStateDTO;
use App\DTOs\Question\QuestionCreateDTO;
use App\DTOs\Question\QuestionUpdateDTO;
use App\DTOs\Quiz\InteractionDTO;
use App\DTOs\Quiz\QuizContextDTO;
use App\DTOs\Quiz\QuizSubmissionDTO;
use App\Enums\Lms\QuestionDifficulty;
use App\Enums\Lms\QuestionType;
use App\Enums\User\RoleName;
use App\Exceptions\Domain\QuestionNotFoundException;
use App\Helpers\ProgressHelper;
use App\Http\Resources\MaterialResource;
use App\Http\Resources\QuestionResource;
use App\Models\AdaptiveAction;
use App\Models\AdaptiveExecutionLog;
use App\Models\AdaptiveFact;
use App\Models\Material;
use App\Models\Question;
use App\Models\StudentState;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;

final readonly class QuizService implements QuizServiceInterface
{
    public function __construct(
        private QuestionRepositoryInterface $questionRepository,
        private AnswerRepositoryInterface $answerRepository,
        private MaterialRepositoryInterface $materialRepository,
        private ProgressRepositoryInterface $progressRepository,
        private PerformanceServiceInterface $performanceService,
        private AdaptiveEngineServiceInterface $adaptiveEngineService,
        private AdaptiveActionProcessorInterface $adaptiveActionProcessor,
        private GuestProgressServiceInterface $guestProgressService,
    ) {}

    // =========================================================================
    // QUESTION MANAGEMENT (CRUD)
    // =========================================================================

    public function getFilteredQuestions(
        ?string $search = null,
        ?QuestionDifficulty $questionDifficulty = null,
        ?string $materialId = null,
    ): LengthAwarePaginator {
        $difficultyString = $questionDifficulty instanceof QuestionDifficulty ? $questionDifficulty->value : null;
        $questions        = $this->questionRepository->getFilteredQuestions($search, $difficultyString, $materialId);

        return $questions->through(fn ($question) => new QuestionResource($question)->resolve());
    }

    public function getQuestionById(string $id): ?array
    {
        $question = $this->questionRepository->find($id);

        return $question instanceof Question ? new QuestionResource($question)->resolve() : null;
    }

    public function getQuestionWithAnswers(string $id): ?array
    {
        $question = $this->questionRepository->findWithAnswers($id);

        return $question ? new QuestionResource($question)->resolve() : null;
    }

    public function createQuestion(QuestionCreateDTO $questionCreateDTO): Question
    {
        return DB::transaction(function () use ($questionCreateDTO): Question {
            $question = $this->questionRepository->create([
                'question_text' => $questionCreateDTO->question_text,
                'question_type' => $questionCreateDTO->question_type,
                'difficulty'    => $questionCreateDTO->difficulty,
                'material_id'   => $questionCreateDTO->material_id,
                'created_by'    => $questionCreateDTO->created_by,
            ]);

            $this->createAnswers($question->id, $questionCreateDTO->answers);

            return $question;
        });
    }

    public function updateQuestion(string $questionId, QuestionUpdateDTO $questionUpdateDTO): Question
    {
        $question = $this->questionRepository->find($questionId);
        if (! $question instanceof Question) {
            throw new QuestionNotFoundException($questionId);
        }

        return DB::transaction(function () use ($question, $questionUpdateDTO) {
            $question->update([
                'question_text' => $questionUpdateDTO->question_text,
                'question_type' => $questionUpdateDTO->question_type,
                'difficulty'    => $questionUpdateDTO->difficulty,
                'material_id'   => $questionUpdateDTO->material_id,
            ]);

            // Sync answers
            $this->answerRepository->deleteByQuestionId($question->id);
            $this->createAnswers($question->id, $questionUpdateDTO->answers);

            return $question->fresh(['answers']);
        });
    }

    public function deleteQuestion(string $questionId): void
    {
        $question = $this->questionRepository->find($questionId);
        if ($question instanceof Question) {
            DB::transaction(function () use ($question): void {
                $this->answerRepository->deleteByQuestionId($question->id);
                $this->questionRepository->delete($question->id);
            });
        }
    }

    private function createAnswers(string $questionId, array $answersData): void
    {
        foreach ($answersData as $answerData) {
            $this->answerRepository->create([
                'question_id'    => $questionId,
                'answer_text'    => $answerData['answer_text']    ?? null,
                'is_correct'     => $answerData['is_correct']     ?? 0,
                'explanation'    => $answerData['explanation']    ?? null,
                'drag_source'    => $answerData['drag_source']    ?? null,
                'drag_target'    => $answerData['drag_target']    ?? null,
                'blank_position' => $answerData['blank_position'] ?? null,
            ]);
        }
    }

    // =========================================================================
    // QUIZ LISTING & DATA
    // =========================================================================

    public function getQuizData(QuizContextDTO $quizContextDTO): array
    {
        $material         = $quizContextDTO->material;
        $difficulty       = $quizContextDTO->difficulty;
        $userId           = $quizContextDTO->userId;
        $isGuest          = $quizContextDTO->isGuest;
        $guestProgress    = $quizContextDTO->guestProgress;
        $targetDifficulty = $quizContextDTO->targetDifficulty;

        $answeredQuestionIds = $isGuest
            ? $this->getGuestAnsweredQuestionIds($material->id, $guestProgress, true)
            : $this->progressRepository->getAnsweredQuestionIds($userId, $material->id);

        $filteredData           = $this->getFilteredQuestionsForQuiz($material, $difficulty, $isGuest);
        $questions              = $filteredData['questions'];
        $allQuestions           = $questions;
        $totalFilteredQuestions = $filteredData['totalFilteredQuestions'];

        $appliedTargetFilter         = false;
        $shouldApplyTargetDifficulty = ! $difficulty instanceof QuestionDifficulty && $targetDifficulty instanceof QuestionDifficulty && ! $isGuest;

        if ($shouldApplyTargetDifficulty) {
            $difficultyOrder = Question::DIFFICULTY_ORDER;
            $targetLevel     = $difficultyOrder[$targetDifficulty->value] ?? 1;

            $answeredArray = $answeredQuestionIds->toArray();

            $attemptedIds = $isGuest
                ? $this->getGuestAnsweredQuestionIds($material->id, $guestProgress, false)->toArray()
                : $this->progressRepository->getAttemptedQuestionIds($userId, $material->id)->toArray();

            $questions = $questions->reject(function ($q) use ($answeredArray, $attemptedIds, $targetLevel, $difficultyOrder): bool {
                $qLevel      = $difficultyOrder[$q->difficulty->value] ?? 1;
                $isCorrect   = in_array($q->id, $answeredArray);
                $isAttempted = in_array($q->id, $attemptedIds);
                if ($isCorrect) {
                    return true;
                }

                return ! $isAttempted && $qLevel < $targetLevel;
            });
            $appliedTargetFilter = true;
        }

        // R02 Spec: "tampilkan 5 soal level mudah" — forced_easy_count override
        if (! $isGuest) {
            $state         = $this->performanceService->findOrCreateStudentState($userId);
            $adaptiveState = $state->adaptive_state ?? [];

            if (($adaptiveState['forced_easy_count'] ?? 0) > 0) {
                $answeredArray  = $answeredQuestionIds->toArray();
                $easyUnanswered = $allQuestions
                    ->where('difficulty', QuestionDifficulty::BEGINNER)
                    ->reject(fn ($q): bool => in_array($q->id, $answeredArray))
                    ->take($adaptiveState['forced_easy_count']);

                if ($easyUnanswered->isNotEmpty()) {
                    $questions           = $easyUnanswered;
                    $appliedTargetFilter = true;
                }

                // Decrement counter so it expires after consumption
                $adaptiveState['forced_easy_count'] = max(0, $adaptiveState['forced_easy_count'] - 1);
                $state->adaptive_state              = $adaptiveState;
                $state->save();
            }
        }

        $currentQuestion = $this->getCurrentQuestion($questions, $answeredQuestionIds);

        if ($appliedTargetFilter && ! $currentQuestion instanceof Question) {
            $fallbackQuestion = $this->getCurrentQuestion($allQuestions, $answeredQuestionIds);
            if ($fallbackQuestion instanceof Question) {
                $questions       = $allQuestions;
                $currentQuestion = $fallbackQuestion;
            }
        }

        $levelProgress     = $this->getLevelProgress($material, $difficulty, $answeredQuestionIds, $isGuest, $questions);
        $shuffledQuestions = $questions->groupBy('difficulty')->map(fn ($g) => $g->shuffle())->flatten(1);
        $questions         = new Collection($shuffledQuestions->all());

        if (! $currentQuestion instanceof Question) {
            $currentQuestion = $this->getCurrentQuestion($questions, $answeredQuestionIds);
        }

        $answeredArray       = $answeredQuestionIds->toArray();
        $actualAnsweredCount = $allQuestions->filter(fn ($q): bool => in_array($q->id, $answeredArray))->count();

        return [
            'material'                => new MaterialResource($material)->resolve(),
            'questions'               => QuestionResource::collection($questions)->resolve(),
            'current_question'        => $currentQuestion instanceof Question ? new QuestionResource($currentQuestion)->resolve() : null,
            'current_question_number' => $actualAnsweredCount + 1,
            'total_questions'         => $totalFilteredQuestions,
            'answered_count'          => $actualAnsweredCount,
            'material_answered_count' => $answeredQuestionIds->count(),
            'level_progress'          => $levelProgress,
            'difficulty'              => $difficulty instanceof QuestionDifficulty ? $difficulty->value : 'all',
            'is_guest'                => $isGuest,
            'student_state'           => $isGuest ? $this->guestProgressService->getStudentSessionState() : $this->performanceService->getStudentSessionState($userId),
        ];
    }

    public function getMaterialsListWithStudentCount(
        string $userId,
        bool $isGuest,
        array $guestProgress = [],
    ): SupportCollection {
        $progressStats = $isGuest ? collect([]) : $this->progressRepository->getUserProgressStats($userId);
        $allMaterials  = $this->materialRepository->getAllWithQuestions();

        if ($isGuest) {
            $allMaterials = $allMaterials->take((int) ceil($allMaterials->count() / 2));
        }

        $studentCounts = $this->progressRepository->getStudentCountByMaterial();

        return $allMaterials->map(function ($material) use ($progressStats, $isGuest, $studentCounts, $guestProgress): array {
            $configuredTotalQuestions = ProgressHelper::calculateMaterialQuestionCounts($material, $isGuest)['total'];

            if ($isGuest) {
                $answeredCount = 0;
                foreach ($guestProgress as $key => $progress) {
                    if (str_starts_with($key, $material->id . '_') && (isset($progress['is_correct']) || isset($progress['attempt_number']))) {
                        $answeredCount++;
                    }
                }
            } else {
                $materialProgress = $progressStats->firstWhere('material_id', $material->id);
                $answeredCount    = $materialProgress ? $materialProgress->answered_questions : 0;
            }

            $material->progress_percentage = ProgressHelper::calculateProgressPercentage($answeredCount, $configuredTotalQuestions);
            $material->total_questions     = $configuredTotalQuestions;
            $material->completed_questions = $answeredCount;
            $material->student_count       = $studentCounts->firstWhere('material_id', $material->id)?->student_count ?? 0;
            $material->is_locked           = false; // No module gating

            return new MaterialResource($material)->resolve();
        });
    }

    public function getReviewQuestions(QuizContextDTO $quizContextDTO): SupportCollection
    {
        $material      = $quizContextDTO->material;
        $difficulty    = $quizContextDTO->difficulty;
        $userId        = $quizContextDTO->userId;
        $isGuest       = $quizContextDTO->isGuest;
        $guestProgress = $quizContextDTO->guestProgress;

        $questions = $material->questions;
        if ($difficulty instanceof QuestionDifficulty) {
            $questions = $questions->where('difficulty', $difficulty->value);
        }

        if ($isGuest) {
            $answeredQuestionIds = $this->getGuestAnsweredQuestionIds($material->id, $guestProgress);
            $questions           = $questions->whereIn('id', $answeredQuestionIds->toArray());
            foreach ($questions as $question) {
                $key = $material->id . '_' . $question->id;
                if (isset($guestProgress[$key])) {
                    $question->user_attempt = $guestProgress[$key];
                }
            }
        } else {
            $answeredQuestionIds = $this->progressRepository->getAnsweredQuestionIds($userId, $material->id);
            $questions           = $questions->whereIn('id', $answeredQuestionIds->toArray());
            $latestAttempts      = $this->progressRepository->getLatestAttemptsForQuestions($userId, $answeredQuestionIds->toArray());
            foreach ($questions as $question) {
                $attempt = $latestAttempts->get($question->id);
                if ($attempt) {
                    $question->user_attempt = [
                        'score'          => $attempt->score,
                        'is_correct'     => $attempt->is_correct,
                        'answer_id'      => $attempt->answer_id,
                        'user_response'  => $attempt->user_response,
                        'attempt_number' => $attempt->attempt_number,
                        'time_spent'     => $attempt->time_spent,
                    ];
                }
            }
        }

        return collect(QuestionResource::collection($questions->values())->resolve());
    }

    public function getGuestAnsweredQuestionIds(string $materialId, array $guestProgress = [], bool $onlyCorrect = false): SupportCollection
    {
        $answeredQuestionIds = collect([]);
        foreach ($guestProgress as $key => $progress) {
            if (! is_array($progress)) {
                continue;
            }

            // If $onlyCorrect is true, skip failed attempts
            if ($onlyCorrect && ! ($progress['is_correct'] ?? false)) {
                continue;
            }

            // Ensure it has at least attempt_number or is_correct
            if (! isset($progress['is_correct']) && ! isset($progress['attempt_number'])) {
                continue;
            }

            $parts = explode('_', (string) $key);
            if (count($parts) < 2) {
                continue;
            }

            if ($parts[0] != $materialId) {
                continue;
            }

            if ($answeredQuestionIds->doesntContain($parts[1])) {
                $answeredQuestionIds->push($parts[1]);
            }
        }

        return $answeredQuestionIds;
    }

    public function getLevelProgress(
        Material $material,
        ?QuestionDifficulty $questionDifficulty,
        SupportCollection|Collection $answeredQuestionIds,
        bool $isGuest = false,
        ?Collection $preloadedQuestions = null,
    ): array {
        $questions = $preloadedQuestions ?? $this->questionRepository->getByMaterialAndDifficulty($material->id, $questionDifficulty instanceof QuestionDifficulty ? $questionDifficulty->value : 'all');
        if (! $preloadedQuestions instanceof Collection && $isGuest) {
            $questions = $questions->take($questionDifficulty instanceof QuestionDifficulty ? 3 : 9);
        }

        $answeredArray = $answeredQuestionIds->toArray();
        $completed     = $questions->filter(fn ($item): bool => in_array($item->id, $answeredArray))->values();
        $remaining     = $questions->reject(fn ($item): bool => in_array($item->id, $answeredArray))->values();

        $levels = [];
        $index  = 1;

        /** @var Question $question */
        foreach ($completed as $question) {
            $levels[] = [
                'level'       => $index++,
                'question_id' => $question->id,
                'status'      => 'completed',
            ];
        }

        $isFirst = true;
        /** @var Question $questionItem */
        foreach ($remaining as $questionItem) {
            $levels[] = [
                'level'       => $index++,
                'question_id' => $questionItem->id,
                'status'      => $isFirst ? 'unlocked' : 'locked',
            ];
            $isFirst = false;
        }

        return $levels;
    }

    // =========================================================================
    // ANSWER LOGIC & ORCHESTRATION
    // =========================================================================

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
            $question  = Question::with('material')->findOrFail($quizSubmissionDTO->questionId);
            $isCorrect = $this->determineCorrectness($question, $quizSubmissionDTO->toArray());

            $score = $this->performanceService->calculateScore(
                isCorrect: $isCorrect,
                usedHint: $quizSubmissionDTO->usedHint,
                timeSpent: $quizSubmissionDTO->timeSpent,
                difficulty: $question->difficulty,
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

            // Save Guest Progress in Session for immediate context availability
            if ($quizSubmissionDTO->userId === '' || $quizSubmissionDTO->userId === RoleName::GUEST->value) {
                $this->guestProgressService->saveProgress(
                    ['material_id' => $quizSubmissionDTO->materialId],
                    $isCorrect,
                    $quizSubmissionDTO->questionId,
                );
            }

            // Update Performance Metrics
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

            // Evaluate Adaptive Engine
            $engineResult = $this->adaptiveEngineService->evaluate(
                StudentStateDTO::fromArray($studentState->toArray()),
            );

            // 3. Log and Apply Result
            $this->logAndApplyAdaptiveResult($quizSubmissionDTO->userId, $quizSubmissionDTO->materialId, $engineResult->toArray(), $studentState, $isCorrect);

            // 4. Transform for UI Feedback
            $diagnosisFact     = AdaptiveFact::find($engineResult->diagnosis);
            $friendlyDiagnosis = $diagnosisFact ? $diagnosisFact->name : $engineResult->diagnosis;

            // Hydrate Actions
            $actionModels    = AdaptiveAction::whereIn('id', $engineResult->actions)->get()->keyBy('id');
            $hydratedActions = array_map(fn ($id): array => [
                'id'          => $id,
                'name'        => $actionModels->get($id)?->name    ?? str_replace('_', ' ', $id),
                'variant'     => $actionModels->get($id)?->variant ?? 'feedback',
            ], $engineResult->actions);

            return [
                'is_correct'          => $isCorrect,
                'score'               => $score,
                'challenge_question'  => null, // Handled via adaptive_state
                'engine_result'       => array_merge($engineResult->toArray(), [
                    'diagnosis'       => $friendlyDiagnosis,
                    'actions'         => $hydratedActions,
                    'show_guidance'   => in_array('SHOW_GUIDANCE', $engineResult->actions),
                ]),
            ];
        });
    }

    private function logAndApplyAdaptiveResult(string $userId, string $materialId, array $result, StudentState $studentState, bool $isCorrect): void
    {
        // 1. Log the execution (only for registered users)
        if ($userId !== RoleName::GUEST->value) {
            AdaptiveExecutionLog::create([
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

        // 2. Process and Apply Actions via the new Dedicated Service
        $this->adaptiveActionProcessor->process($studentState, $result['actions'], $materialId, $isCorrect);

        // Update the last diagnosis in the student state
        $adaptiveState                   = $studentState->adaptive_state ?? [];
        $adaptiveState['last_diagnosis'] = $result['diagnosis'];
        $studentState->adaptive_state    = $adaptiveState;

        if ($userId !== RoleName::GUEST->value) {
            $studentState->save();
        }
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    private function getFilteredQuestionsForQuiz(Material $material, ?QuestionDifficulty $questionDifficulty, bool $isGuest): array
    {
        $questions = $this->questionRepository->getByMaterialAndDifficulty($material->id, $questionDifficulty instanceof QuestionDifficulty ? $questionDifficulty->value : 'all');
        if ($isGuest) {
            if (! $questionDifficulty instanceof QuestionDifficulty) {
                $questions = $questions->where('difficulty', QuestionDifficulty::BEGINNER->value)->take(3)
                    ->concat($questions->where('difficulty', QuestionDifficulty::MEDIUM->value)->take(3))
                    ->concat($questions->where('difficulty', QuestionDifficulty::HARD->value)->take(3));
                $total = 9;
            } else {
                $questions = $questions->take(3);
                $total     = 3;
            }
        } else {
            $total = $questions->count();
        }

        return ['questions' => $questions, 'totalFilteredQuestions' => $total];
    }

    private function getCurrentQuestion(Collection $questions, SupportCollection $supportCollection): ?Question
    {
        $answeredArray = $supportCollection->toArray();
        $current       = $questions->reject(fn ($q): bool => in_array($q->id, $answeredArray))->first();
        if ($current instanceof Question && $current->question_type !== QuestionType::FILL_IN_THE_BLANK) {
            if (! $current->relationLoaded('answers')) {
                $current->load('answers');
            }

            $current->setRelation('answers', $current->answers->shuffle());
        }

        return $current;
    }
}
