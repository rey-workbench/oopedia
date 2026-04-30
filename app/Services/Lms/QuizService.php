<?php

declare(strict_types=1);

namespace App\Services\Lms;

use App\Contracts\Repositories\AnswerRepositoryInterface;
use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\QuestionRepositoryInterface;
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
use App\Exceptions\Domain\QuestionNotFoundException;
use App\Helpers\ProgressHelper;
use App\Models\AdaptiveAction;
use App\Models\AdaptiveExecutionLog;
use App\Models\AdaptiveFact;
use App\Models\Material;
use App\Models\Question;
use App\Models\StudentState;
use App\Rules\Adaptive\Constants\AdaptiveMetadataKeys;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;

final class QuizService implements QuizServiceInterface
{
    public function __construct(
        private readonly QuestionRepositoryInterface $questionRepo,
        private readonly AnswerRepositoryInterface $answerRepo,
        private readonly MaterialRepositoryInterface $materialRepo,
        private readonly ProgressRepositoryInterface $progressRepo,
        private readonly PerformanceServiceInterface $performanceService,
        private readonly AdaptiveEngineServiceInterface $adaptiveEngineService,
        private readonly GuestProgressServiceInterface $guestProgressService,
    ) {}

    // =========================================================================
    // QUESTION MANAGEMENT (CRUD)
    // =========================================================================

    public function getFilteredQuestions(
        ?string $search = null,
        ?QuestionDifficulty $difficulty = null,
        ?string $materialId = null,
    ): LengthAwarePaginator {
        $difficultyString = $difficulty ? $difficulty->value : null;
        $questions        = $this->questionRepo->getFilteredQuestions($search, $difficultyString, $materialId);

        return $questions->through(function ($question) {
            $question->formatted_type = match ($question->question_type) {
                QuestionType::FILL_IN_THE_BLANK => 'Fill in the Blank',
                QuestionType::RADIO_BUTTON      => 'Radio Button',
                QuestionType::DRAG_AND_DROP     => 'Drag and Drop',
                default                         => $question->question_type,
            };

            return $question;
        });
    }

    public function getQuestionById(string $id): ?Question
    {
        return $this->questionRepo->find($id);
    }

    public function getQuestionWithAnswers(string $id): ?Question
    {
        return $this->questionRepo->findWithAnswers($id);
    }

    public function createQuestion(QuestionCreateDTO $dto): Question
    {
        return DB::transaction(function () use ($dto) {
            $question = $this->questionRepo->create([
                'question_text' => $dto->question_text,
                'question_type' => $dto->question_type,
                'difficulty'    => $dto->difficulty,
                'material_id'   => $dto->material_id,
                'created_by'    => $dto->created_by,
            ]);

            $this->createAnswers($question->id, $dto->answers);

            return $question;
        });
    }

    public function updateQuestion(string $questionId, QuestionUpdateDTO $dto): Question
    {
        $question = $this->questionRepo->find($questionId);
        if (! $question) {
            throw new QuestionNotFoundException($questionId);
        }

        return DB::transaction(function () use ($question, $dto) {
            $question->update([
                'question_text' => $dto->question_text,
                'question_type' => $dto->question_type,
                'difficulty'    => $dto->difficulty,
                'material_id'   => $dto->material_id,
            ]);

            // Sync answers
            $this->answerRepo->deleteByQuestionId($question->id);
            $this->createAnswers($question->id, $dto->answers);

            return $question->fresh(['answers']);
        });
    }

    public function deleteQuestion(string $questionId): void
    {
        $question = $this->questionRepo->find($questionId);
        if ($question) {
            DB::transaction(function () use ($question) {
                $this->answerRepo->deleteByQuestionId($question->id);
                $this->questionRepo->delete($question->id);
            });
        }
    }

    protected function createAnswers(string $questionId, array $answersData): void
    {
        foreach ($answersData as $answer) {
            $this->answerRepo->create([
                'question_id'    => $questionId,
                'answer_text'    => $answer['answer_text']    ?? null,
                'is_correct'     => $answer['is_correct']     ?? 0,
                'explanation'    => $answer['explanation']    ?? null,
                'drag_source'    => $answer['drag_source']    ?? null,
                'drag_target'    => $answer['drag_target']    ?? null,
                'blank_position' => $answer['blank_position'] ?? null,
            ]);
        }
    }

    // =========================================================================
    // QUIZ LISTING & DATA
    // =========================================================================

    public function getQuizData(QuizContextDTO $context): array
    {
        $material         = $context->material;
        $difficulty       = $context->difficulty;
        $userId           = $context->userId;
        $isGuest          = $context->isGuest;
        $guestProgress    = $context->guestProgress;
        $targetDifficulty = $context->targetDifficulty;

        $answeredQuestionIds = $isGuest
            ? $this->getGuestAnsweredQuestionIds($material->id, $guestProgress, true)
            : $this->progressRepo->getAnsweredQuestionIds($userId, $material->id);

        $filteredData           = $this->getFilteredQuestionsForQuiz($material, $difficulty, $isGuest);
        $questions              = $filteredData['questions'];
        $allQuestions           = $questions;
        $totalFilteredQuestions = $filteredData['totalFilteredQuestions'];

        $appliedTargetFilter         = false;
        $shouldApplyTargetDifficulty = $difficulty === null && $targetDifficulty && ! $isGuest;

        if ($shouldApplyTargetDifficulty) {
            $difficultyOrder = Question::DIFFICULTY_ORDER;
            $targetLevel     = $difficultyOrder[$targetDifficulty->value] ?? 1;

            $answeredArray = $answeredQuestionIds->toArray();
            
            $attemptedIds = $isGuest 
                ? $this->getGuestAnsweredQuestionIds($material->id, $guestProgress, false)->toArray()
                : $this->progressRepo->getAttemptedQuestionIds($userId, $material->id)->toArray();

            $questions = $questions->reject(function ($q) use ($answeredArray, $attemptedIds, $targetLevel, $difficultyOrder) {
                $qLevel = $difficultyOrder[$q->difficulty->value] ?? 1;
                $isCorrect = in_array($q->id, $answeredArray);
                $isAttempted = in_array($q->id, $attemptedIds);

                if ($isCorrect) return true;
                if (!$isAttempted && $qLevel < $targetLevel) return true;

                return false;
            });
            $appliedTargetFilter = true;
        }

        // R02 Spec: "tampilkan 5 soal level mudah" — forced_easy_count override
        if (! $isGuest) {
            $state         = $this->performanceService->getStudentState($userId);
            $adaptiveState = $state->adaptive_state ?? [];

            if (($adaptiveState['forced_easy_count'] ?? 0) > 0) {
                $answeredArray  = $answeredQuestionIds->toArray();
                $easyUnanswered = $allQuestions
                    ->where('difficulty', QuestionDifficulty::BEGINNER)
                    ->reject(fn ($q) => in_array($q->id, $answeredArray))
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

        if ($appliedTargetFilter && $currentQuestion === null) {
            $fallbackQuestion = $this->getCurrentQuestion($allQuestions, $answeredQuestionIds);
            if ($fallbackQuestion !== null) {
                $questions       = $allQuestions;
                $currentQuestion = $fallbackQuestion;
            }
        }

        $levelProgress     = $this->getLevelProgress($material, $difficulty, $answeredQuestionIds, $isGuest, $questions);
        $shuffledQuestions = $questions->groupBy('difficulty')->map(fn ($g) => $g->shuffle())->flatten(1);
        $questions         = new Collection($shuffledQuestions->all());

        if ($currentQuestion === null) {
            $currentQuestion = $this->getCurrentQuestion($questions, $answeredQuestionIds);
        }

        $answeredArray       = $answeredQuestionIds->toArray();
        $actualAnsweredCount = $allQuestions->filter(fn ($q) => in_array($q->id, $answeredArray))->count();

        return [
            'material'                => $material,
            'questions'               => $questions,
            'current_question'        => $currentQuestion,
            'current_question_number' => $actualAnsweredCount + 1,
            'total_questions'         => $totalFilteredQuestions,
            'answered_count'          => $actualAnsweredCount,
            'material_answered_count' => $answeredQuestionIds->count(),
            'level_progress'          => $levelProgress,
            'difficulty'              => $difficulty ? $difficulty->value : 'all',
            'is_guest'                => $isGuest,
            'student_state'           => $isGuest ? null : $this->performanceService->getStudentSessionState($userId),
        ];
    }

    public function getMaterialsListWithStudentCount(
        string $userId,
        bool $isGuest,
        array $guestProgress = [],
    ): Collection {
        $progressStats = $isGuest ? collect([]) : $this->progressRepo->getUserProgressStats($userId);
        $allMaterials  = $this->materialRepo->getAllWithQuestions();

        if ($isGuest) {
            $allMaterials = $allMaterials->take((int) ceil($allMaterials->count() / 2));
        }

        $studentCounts = $this->progressRepo->getStudentCountByMaterial();

        return $allMaterials->map(function ($material) use ($progressStats, $isGuest, $studentCounts, $guestProgress) {
            $configuredTotalQuestions = ProgressHelper::calculateMaterialQuestionCounts($material, $isGuest)['total'];

            if ($isGuest) {
                $answeredCount = 0;
                foreach ($guestProgress as $key => $progress) {
                    if (strpos($key, $material->id . '_') === 0 && (isset($progress['is_correct']) || isset($progress['attempt_number']))) {
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

            return $material;
        });
    }

    public function getReviewQuestions(QuizContextDTO $context): Collection
    {
        $material      = $context->material;
        $difficulty    = $context->difficulty;
        $userId        = $context->userId;
        $isGuest       = $context->isGuest;
        $guestProgress = $context->guestProgress;

        $questions = $material->questions;
        if ($difficulty !== null) {
            $questions = $questions->where('difficulty', $difficulty->value);
        }

        if ($isGuest) {
            $answeredQuestionIds = $this->getGuestAnsweredQuestionIds($material->id, $guestProgress);
            $questions           = $questions->whereIn('id', $answeredQuestionIds->toArray());
            foreach ($questions as $q) {
                $key = $material->id . '_' . $q->id;
                if (isset($guestProgress[$key])) {
                    $q->user_attempt = $guestProgress[$key];
                }
            }
        } else {
            $answeredQuestionIds = $this->progressRepo->getAnsweredQuestionIds($userId, $material->id);
            $questions           = $questions->whereIn('id', $answeredQuestionIds->toArray());
            $latestAttempts      = $this->progressRepo->getLatestAttemptsForQuestions($userId, $answeredQuestionIds->toArray());
            foreach ($questions as $q) {
                $attempt = $latestAttempts->get($q->id);
                if ($attempt) {
                    $q->user_attempt = [
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

        return $questions->values();
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

            $parts = explode('_', $key);
            if (count($parts) < 2 || $parts[0] != $materialId) {
                continue;
            }
            if (! $answeredQuestionIds->contains($parts[1])) {
                $answeredQuestionIds->push($parts[1]);
            }
        }

        return $answeredQuestionIds;
    }

    public function getLevelProgress(
        Material $material,
        ?QuestionDifficulty $difficulty,
        SupportCollection|Collection $answeredQuestionIds,
        bool $isGuest = false,
        ?Collection $preloadedQuestions = null,
    ): array {
        $questions = $preloadedQuestions !== null ? $preloadedQuestions : $this->questionRepo->getByMaterialAndDifficulty($material->id, $difficulty ? $difficulty->value : 'all');
        if ($preloadedQuestions === null && $isGuest) {
            $questions = $questions->take($difficulty === null ? 9 : 3);
        }

        $answeredArray = $answeredQuestionIds->toArray();
        $completed = $questions->filter(fn ($item) => in_array($item->id, $answeredArray))->values();
        $remaining = $questions->reject(fn ($item) => in_array($item->id, $answeredArray))->values();

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
            if (empty($answer)) {
                return false;
            }

            return $question->answers()->where('is_correct', true)->get()->contains(fn ($ans) => trim(strtolower($ans->answer_text)) === $answer);
        }

        if ($question->question_type === QuestionType::DRAG_AND_DROP) {
            $userAnswersStr = $data['drag_and_drop_answers'] ?? '[]';
            $userAnswers    = is_array($userAnswersStr) ? $userAnswersStr : json_decode($userAnswersStr, true);
            if (empty($userAnswers)) {
                return false;
            }
            $correctAnswers = $question->answers()->whereNotNull('drag_target')->get();
            if ($correctAnswers->isEmpty()) {
                return false;
            }
            foreach ($correctAnswers as $correctAns) {
                if (trim($userAnswers[$correctAns->drag_target] ?? '') !== trim($correctAns->answer_text)) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    public function handleSubmission(QuizSubmissionDTO $submission): array
    {
        return DB::transaction(function () use ($submission) {
            $question  = Question::with('material')->findOrFail($submission->questionId);
            $isCorrect = $this->determineCorrectness($question, $submission->toArray());

            $score = $this->performanceService->calculateScore(
                isCorrect: $isCorrect,
                usedHint: $submission->usedHint,
                timeSpent: $submission->timeSpent,
                difficulty: $question->difficulty,
            );

            $userResponse = null;
            $answerId     = null;
            if ($question->question_type === QuestionType::RADIO_BUTTON) {
                $answerId = $submission->answer;
            } elseif ($question->question_type === QuestionType::FILL_IN_THE_BLANK) {
                $userResponse = $submission->fillInTheBlankAnswer;
            } elseif ($question->question_type === QuestionType::DRAG_AND_DROP) {
                $userResponse = is_array($submission->dragAndDropAnswers) ? json_encode($submission->dragAndDropAnswers) : $submission->dragAndDropAnswers;
            }

            $this->progressRepo->saveProgress([
                'user_id'       => $submission->userId ?: 'guest',
                'material_id'   => $submission->materialId,
                'question_id'   => $submission->questionId,
                'is_correct'    => $isCorrect,
                'score'         => $score,
                'time_spent'    => $submission->timeSpent,
                'used_hint'     => $submission->usedHint,
                'answer_id'     => $answerId,
                'user_response' => $userResponse,
            ]);

            // Save Guest Progress in Session for immediate context availability
            if ($submission->userId === '' || $submission->userId === 'guest') {
                $this->guestProgressService->saveProgress(
                    ['material_id' => $submission->materialId],
                    $isCorrect,
                    $submission->questionId
                );
            }

            // Update Performance Metrics
            $interaction = new InteractionDTO(
                userId: $submission->userId,
                questionId: $submission->questionId,
                isCorrect: $isCorrect,
                timeSpent: $submission->timeSpent,
                difficulty: $question->difficulty,
                usedHint: $submission->usedHint,
                score: $score,
            );

            $studentState = $this->performanceService->updateMetricsFromInteraction($interaction);

            // Evaluate Adaptive Engine
            $engineResult = $this->adaptiveEngineService->evaluate(
                StudentStateDTO::fromArray($studentState->toArray()),
            );

            // 3. Log and Apply Result
            $this->logAndApplyAdaptiveResult($submission->userId, $submission->materialId, $engineResult->toArray(), $studentState);

            // 4. Transform for UI Feedback
            $diagnosisFact     = AdaptiveFact::find($engineResult->diagnosis);
            $friendlyDiagnosis = $diagnosisFact ? $diagnosisFact->name : 'Progres Normal';

            $friendlyRecommendations = [];
            foreach ($engineResult->recommendations as $rec) {
                $actionId = is_array($rec) ? $rec['id'] : $rec;
                $action   = AdaptiveAction::find($actionId);
                if ($action) {
                    $friendlyRecommendations[] = $action->name;
                }
            }

            return [
                'is_correct'          => $isCorrect,
                'score'               => $score,
                'raw_recommendations' => $engineResult->recommendations,
                'engine_result'       => array_merge($engineResult->toArray(), [
                    'diagnosis'       => $friendlyDiagnosis,
                    'recommendations' => $friendlyRecommendations,
                ]),
            ];
        });
    }

    private function logAndApplyAdaptiveResult(string $userId, string $materialId, array $result, StudentState $state): void
    {
        // 1. Log the execution
        AdaptiveExecutionLog::create([
            'user_id'           => $userId,
            'rule_id'           => $result['id'],
            'action_id'         => implode(', ', array_map(fn ($r) => is_array($r) ? $r['id'] : $r, $result['recommendations'])),
            'trigger_facts'     => $result['facts'] ?? [],
            'state_deltas'      => [],
            'new_state'         => $state->toArray(),
            'execution_context' => [
                'material_id' => $materialId,
                'timestamp'   => $result['timestamp'],
            ],
        ]);

        // 2. Prepare Adaptive State
        $adaptiveState                         = $state->adaptive_state ?? [];
        $adaptiveState['last_diagnosis']       = $result['diagnosis'];
        $adaptiveState['active_interventions'] = array_map(fn ($r) => is_array($r) ? $r['id'] : $r, $result['recommendations']);

        // 3. Apply Layer 4: Aksi Sistem (State Transitions)
        $metaKeys = AdaptiveMetadataKeys::class;

        $difficultyOrder = ['beginner', 'medium', 'hard', 'final'];
        $currentDiff     = $state->target_difficulty ?? 'beginner';
        $currentIndex    = array_search($currentDiff, $difficultyOrder);
        if ($currentIndex === false) {
            $currentIndex = 0;
        }

        foreach ($result['recommendations'] as $recConfig) {
            $recommendation = is_array($recConfig) ? $recConfig['id'] : $recConfig;
            $metadata       = is_array($recConfig) ? ($recConfig['metadata'] ?? []) : [];

            switch ($recommendation) {
                case 'REDUCE_DIFF':
                    // Spec: "turunkan 1 level kesulitan"
                    if ($currentIndex > 0) {
                        $state->target_difficulty = $difficultyOrder[$currentIndex - 1];
                        $currentIndex--;
                    }
                    break;

                case 'INCREASE_DIFF':
                    $steps                    = $metadata[$metaKeys::DIFFICULTY_STEPS] ?? 1;
                    $newIndex                 = min(2, $currentIndex + $steps);
                    $state->target_difficulty = $difficultyOrder[$newIndex];
                    $currentIndex             = $newIndex;
                    break;

                case 'STREAK_BONUS':
                    $state->xp += 50;
                    $badges                  = $adaptiveState['badges'] ?? [];
                    $badges[]                = 'streak_' . ($state->streak ?? 0);
                    $adaptiveState['badges'] = array_unique($badges);
                    break;

                case 'REMEDIAL':
                    $adaptiveState['needs_remedial']       = true;
                    $adaptiveState['remedial_material_id'] = $materialId;

                    if ($metadata[$metaKeys::NOTIFY_TEACHER] ?? false) {
                        $adaptiveState['notify_teacher']      = true;
                        $adaptiveState['notify_teacher_type'] = $metadata[$metaKeys::NOTIFY_TYPE] ?? $metaKeys::TYPE_CRISIS;
                    }

                    if (isset($metadata[$metaKeys::FORCED_EASY_COUNT])) {
                        $state->target_difficulty           = $metadata[$metaKeys::TARGET_DIFFICULTY] ?? 'beginner';
                        $currentIndex                       = array_search($state->target_difficulty, $difficultyOrder);
                        $adaptiveState['forced_easy_count'] = $metadata[$metaKeys::FORCED_EASY_COUNT];
                        $adaptiveState['show_motivation']   = $metadata[$metaKeys::SHOW_MOTIVATION] ?? false;
                    }
                    break;

                case 'SCAFFOLD_REDUCTION':
                    if ($metadata[$metaKeys::GRADUAL_SCAFFOLD_REDUCTION] ?? false) {
                        $currentMax                             = $adaptiveState['max_hints_per_session'] ?? 3;
                        $adaptiveState['max_hints_per_session'] = max(0, $currentMax - 1);
                        $state->hints_available                 = min($state->hints_available, $adaptiveState['max_hints_per_session']);
                    } else {
                        $state->hints_available                 = min(2, $state->hints_available);
                        $adaptiveState['max_hints_per_session'] = 2;
                    }
                    $adaptiveState['scaffold_mode'] = 'minimal';
                    break;

                case 'NEW_CHALLENGE':
                    $state->xp += 100;
                    $adaptiveState['challenge_active'] = true;

                    if ($metadata[$metaKeys::CHECK_CERTIFICATION] ?? false) {
                        $adaptiveState['check_certification'] = true;
                    }

                    if ($metadata[$metaKeys::CROSS_TOPIC_CHALLENGE] ?? false) {
                        $adaptiveState['cross_topic_challenge'] = true;
                    }
                    break;

                case 'CERTIFICATION':
                    $certs                                = $adaptiveState['certifications'] ?? [];
                    $certs[]                              = 'GOLD';
                    $adaptiveState['certifications']      = array_unique($certs);
                    $adaptiveState['notify_teacher']      = true;
                    $adaptiveState['notify_teacher_type'] = $metadata[$metaKeys::NOTIFY_TYPE] ?? $metaKeys::TYPE_CERTIFICATION;
                    $adaptiveState['unlock_advanced']     = true;
                    break;
            }
        }

        $state->adaptive_state = $adaptiveState;
        $state->save();
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    private function getFilteredQuestionsForQuiz(Material $material, ?QuestionDifficulty $difficulty, bool $isGuest): array
    {
        $questions = $this->questionRepo->getByMaterialAndDifficulty($material->id, $difficulty ? $difficulty->value : 'all');
        if ($isGuest) {
            if ($difficulty === null) {
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

    private function getCurrentQuestion(Collection $questions, SupportCollection $answeredQuestionIds): ?Question
    {
        $answeredArray = $answeredQuestionIds->toArray();
        $current       = $questions->reject(fn ($q) => in_array($q->id, $answeredArray))->first();
        if ($current instanceof Question && $current->question_type !== QuestionType::FILL_IN_THE_BLANK) {
            if (! $current->relationLoaded('answers')) {
                $current->load('answers');
            }
            $current->setRelation('answers', $current->answers->shuffle());
        }

        return $current;
    }
}
