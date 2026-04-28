<?php

declare(strict_types=1);

namespace App\Services\Lms;

use App\Contracts\Repositories\AnswerRepositoryInterface;
use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Contracts\Services\AdaptiveEngineServiceInterface;
use App\Contracts\Services\PerformanceServiceInterface;

use App\Contracts\Services\QuizServiceInterface;
use App\Enums\Lms\QuestionDifficulty;
use App\Enums\Lms\QuestionType;
use App\Exceptions\Domain\QuestionNotFoundException;
use App\Helpers\ProgressHelper;
use App\Models\AdaptiveExecutionLog;
use App\Models\Material;
use App\Models\Question;
use App\Models\StudentState;
use App\Rules\Adaptive\Constants\ActionConstants;
use App\Rules\Adaptive\Constants\FactConstants;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Auth;
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

    public function createQuestion(array $data): Question
    {
        return DB::transaction(function () use ($data) {
            $question = $this->questionRepo->create([
                'question_text' => $data['question_text'],
                'question_type' => $data['question_type'],
                'difficulty'    => $data['difficulty'],
                'material_id'   => $data['material_id'],
                'created_by'    => Auth::id(),
            ]);

            $this->createAnswers($question->id, $data['answers']);

            return $question;
        });
    }

    public function updateQuestion(string $questionId, array $data): Question
    {
        $question = $this->questionRepo->find($questionId);
        if (! $question) {
            throw new QuestionNotFoundException($questionId);
        }

        return DB::transaction(function () use ($question, $data) {
            $this->questionRepo->update($question->id, [
                'question_text' => $data['question_text'],
                'question_type' => $data['question_type'],
                'difficulty'    => $data['difficulty'],
                'material_id'   => $data['material_id'],
                'updated_by'    => Auth::id(),
            ]);

            $this->answerRepo->deleteByQuestionId($question->id);
            $this->createAnswers($question->id, $data['answers']);

            return $question->fresh();
        });
    }

    public function deleteQuestion(string $questionId): void
    {
        $question = $this->questionRepo->find($questionId);
        if (! $question) {
            throw new QuestionNotFoundException($questionId);
        }

        DB::transaction(function () use ($question) {
            $this->answerRepo->deleteByQuestionId($question->id);
            $this->questionRepo->delete($question->id);
        });
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

    public function getQuizData(
        Material $material,
        ?QuestionDifficulty $difficulty,
        string $userId,
        bool $isGuest,
        array $guestProgress = [],
        ?QuestionDifficulty $targetDifficulty = null,
    ): array {
        $answeredQuestionIds = $isGuest
            ? $this->getGuestAnsweredQuestionIds($material->id, $guestProgress)
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
            $questions     = $questions->reject(
                fn ($q) => ! in_array($q->id, $answeredArray) && ($difficultyOrder[$q->difficulty->value] ?? 1) < $targetLevel,
            );
            $appliedTargetFilter = true;
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
            'material'              => $material,
            'questions'             => $questions,
            'currentQuestion'       => $currentQuestion,
            'currentQuestionNumber' => $actualAnsweredCount + 1,
            'totalQuestions'        => $totalFilteredQuestions,
            'answeredCount'         => $actualAnsweredCount,
            'materialAnsweredCount' => $answeredQuestionIds->count(),
            'levelProgress'         => $levelProgress,
            'difficulty'            => $difficulty ? $difficulty->value : 'all',
            'isGuest'               => $isGuest,
            'studentState'          => $isGuest ? null : $this->performanceService->getStudentSessionState($userId),
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

    public function getReviewQuestions(
        Material $material,
        ?QuestionDifficulty $difficulty,
        string $userId,
        bool $isGuest,
        array $guestProgress = [],
    ): Collection {
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

    public function getGuestAnsweredQuestionIds(string $materialId, array $guestProgress = []): SupportCollection
    {
        $answeredQuestionIds = collect([]);
        foreach ($guestProgress as $key => $progress) {
            if (! is_array($progress) || (! isset($progress['is_correct']) && ! isset($progress['attempt_number']))) {
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
        $completed     = $questions->filter(fn ($q) => in_array($q->id, $answeredArray));
        $remaining     = $questions->reject(fn ($q) => in_array($q->id, $answeredArray));

        $levels = [];
        $index  = 1;

        foreach ($completed as $question) {
            $levels[] = [
                'level'       => $index++,
                'question_id' => $question->id,
                'status'      => 'completed',
            ];
        }

        $isFirst = true;
        foreach ($remaining as $q) {
            $levels[] = [
                'level'       => $index++,
                'question_id' => $q->id,
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

    public function handleSubmission(string $userId, string $materialId, string $questionId, array $validatedData): array
    {
        return DB::transaction(function () use ($userId, $materialId, $questionId, $validatedData) {
            $question  = Question::with('material')->findOrFail($questionId);
            $isCorrect = $this->determineCorrectness($question, $validatedData);

            $score = $this->performanceService->calculateScore(
                isCorrect: $isCorrect,
                usedHint: (bool) ($validatedData['used_hint'] ?? false),
                timeSpent: (int) ($validatedData['time_spent'] ?? 0),
                difficulty: $question->difficulty,
            );

            $userResponse = null;
            $answerId     = null;
            if ($question->question_type === QuestionType::RADIO_BUTTON) {
                $answerId = $validatedData['answer'] ?? null;
            } elseif ($question->question_type === QuestionType::FILL_IN_THE_BLANK) {
                $userResponse = $validatedData['fill_in_the_blank_answer'] ?? null;
            } elseif ($question->question_type === QuestionType::DRAG_AND_DROP) {
                $userResponse = $validatedData['drag_and_drop_answers'] ?? null;
            }

            $this->progressRepo->saveProgress([
                'user_id'       => $userId,
                'material_id'   => (string) $materialId,
                'question_id'   => (string) $questionId,
                'answer_id'     => $answerId,
                'user_response' => $userResponse,
                'score'         => $score,
                'time_spent'    => (int) ($validatedData['time_spent'] ?? 0),
                'is_correct'    => $isCorrect,
                'difficulty'    => (string) ($validatedData['difficulty'] ?? 'beginner'),
                'used_hint'     => (bool) ($validatedData['used_hint'] ?? false),
            ]);

            // 1. Update Performance Metrics
            $studentState = $this->performanceService->updateMetricsFromInteraction(
                userId: $userId,
                questionId: $questionId,
                isCorrect: $isCorrect,
                usedHint: (bool) ($validatedData['used_hint'] ?? false),
                timeSpent: (int) ($validatedData['time_spent'] ?? 0),
                difficulty: $question->difficulty,
                score: $score,
            );

            // 2. Evaluate Adaptive Engine (Layer 3 & 4)
            $engineResult = $this->adaptiveEngineService->evaluate($studentState->toArray());

            // 3. Log and Apply Result (using raw technical data)
            $this->logAndApplyAdaptiveResult($userId, $materialId, $engineResult, $studentState);

            // 4. Transform for UI Feedback
            $friendlyDiagnosis = match ($engineResult['diagnosis'] ?? '') {
                FactConstants::V_CRISIS     => 'Krisis Pembelajaran',
                FactConstants::V_STRUGGLING => 'Sedang Kesulitan',
                FactConstants::V_OPTIMAL    => 'Performa Optimal',
                FactConstants::V_DEPENDENCY => 'Ketergantungan Bantuan',
                FactConstants::V_BOREDOM    => 'Potensi Kebosanan',
                default                     => 'Progres Normal'
            };

            $friendlyRecommendations = [];
            foreach ($engineResult['recommendations'] as $rec) {
                $label = match ($rec) {
                    ActionConstants::INCREASE_DIFF => 'LEVEL_UP',
                    ActionConstants::REDUCE_DIFF   => 'ADAPTIVE_HELP',
                    ActionConstants::STREAK_BONUS  => 'STREAK_BONUS',
                    ActionConstants::CERTIFICATION => 'ACHIEVEMENT',
                    ActionConstants::REMEDIAL      => 'REMEDIAL_MODE',
                    ActionConstants::NEW_CHALLENGE => 'NEW_CHALLENGE',
                    default                        => null
                };
                if ($label) {
                    $friendlyRecommendations[] = $label;
                }
            }

            return [
                'is_correct'    => $isCorrect,
                'score'         => $score,
                'engine_result' => array_merge($engineResult, [
                    'diagnosis'       => $friendlyDiagnosis,
                    'recommendations' => $friendlyRecommendations,
                    'triggered_rule'  => [
                        'id'     => $engineResult['id'],
                        'name'   => $friendlyDiagnosis,
                        'action' => $engineResult['recommendations'][0] ?? 'NEXT',
                    ],
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
            'action_id'         => implode(', ', $result['recommendations']),
            'trigger_facts'     => [$result['diagnosis']],
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
        $adaptiveState['active_interventions'] = $result['recommendations'];

        // 3. Apply Layer 4: Aksi Sistem (State Transitions)
        $currentDifficulty = $state->target_difficulty ?? 'beginner';
        $difficultyOrder   = ['beginner', 'medium', 'hard'];
        $currentIndex      = array_search($currentDifficulty, $difficultyOrder);

        foreach ($result['recommendations'] as $recommendation) {
            switch ($recommendation) {
                case ActionConstants::REDUCE_DIFF:
                    if ($currentIndex > 0) {
                        $state->target_difficulty = $difficultyOrder[$currentIndex - 1];
                    }
                    break;
                case ActionConstants::INCREASE_DIFF:
                    if ($currentIndex < 2) {
                        $state->target_difficulty = $difficultyOrder[$currentIndex + 1];
                    }
                    break;
                case ActionConstants::STREAK_BONUS:
                    $state->xp += 50;
                    break;
                case ActionConstants::REMEDIAL:
                    $adaptiveState['needs_remedial']       = true;
                    $adaptiveState['remedial_material_id'] = $materialId;
                    break;
                case ActionConstants::SCAFFOLD_REDUCTION:
                    // Reduce available hints for next session or disable them
                    $state->hints_available         = max(0, $state->hints_available - 1);
                    $adaptiveState['scaffold_mode'] = 'minimal';
                    break;
                case ActionConstants::NEW_CHALLENGE:
                    $state->xp += 100;
                    $adaptiveState['challenge_active'] = true;
                    break;
                case ActionConstants::CERTIFICATION:
                    $certs                           = $adaptiveState['certifications'] ?? [];
                    $certs[]                         = 'GOLD'; // R15 is Gold
                    $adaptiveState['certifications'] = array_unique($certs);
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
