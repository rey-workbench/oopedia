<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Contracts\Database\Query\Builder;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\StudentStateRepositoryInterface;
use App\Enums\Lms\QuestionDifficulty;
use App\Enums\User\RoleName;
use App\Models\Material;
use App\Models\User;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Models\StudentState;
use App\Rules\Adaptive\Constants\StudentStateSchema;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final readonly class ProgressRepository implements ProgressRepositoryInterface
{
    public function __construct(
        private StudentStateRepositoryInterface $studentStateRepository,
    ) {
    }

    public function getUserProgressStats(?string $userId): Collection
    {
        if (is_null($userId) || $userId === RoleName::GUEST->value) {
            return collect();
        }

        return Material::select('id as material_id')
            ->withCount([
                'attemptedQuestions as answered_questions' => function ($q) use ($userId): void {
                    $q->where('quiz_attempts.user_id', $userId)->distinct();
                },
                'attemptedQuestions as correct_answers' => function ($q) use ($userId): void {
                    $q->where('quiz_attempts.user_id', $userId)
                        ->where('quiz_attempts.is_correct', true)
                        ->distinct();
                },
            ])
            ->get();
    }

    public function getUserMaterialProgress(?string $userId): Collection
    {
        if (is_null($userId) || $userId === RoleName::GUEST->value) {
            return collect();
        }

        return Material::select('id as material_id')
            ->withCount([
                'attemptedQuestions as total_answered' => function ($q) use ($userId): void {
                    $q->where('quiz_attempts.user_id', $userId)->distinct();
                },
                'quizAttempts as correct_answers' => function ($q) use ($userId): void {
                    $q->where('user_id', $userId)->where('is_correct', true);
                },
            ])
            ->get();
    }

    public function getRecentActivities(?string $userId, int $limit = 5): Collection
    {
        if (is_null($userId) || $userId === RoleName::GUEST->value) {
            return collect();
        }

        $attempts = QuizAttempt::with(['question.material'])
            ->where('user_id', $userId)
            ->where('is_correct', true)->latest()
            ->take($limit)
            ->get();

        $correctCountsByMaterial = QuizAttempt::where('user_id', $userId)
            ->where('is_correct', true)
            ->with('question')
            ->get()
            ->groupBy(fn($attempt) => $attempt->question?->material_id)
            ->map(fn($group) => $group->unique('question_id')->count());

        return $attempts->map(function ($attempt) use ($correctCountsByMaterial) {
            $materialId = $attempt->question->material_id ?? 0;
            $difficulty = $attempt->question?->difficulty instanceof QuestionDifficulty ? $attempt->question->difficulty->value : $attempt->question?->difficulty;
            $previousHardCount = 0;
            if ($difficulty === 'hard') {
                $previousHardCount = QuizAttempt::where('user_id', $attempt->user_id)
                    ->whereRelation('question', 'material_id', $materialId)
                    ->whereRelation('question', 'difficulty', QuestionDifficulty::HARD)
                    ->where('is_correct', true)
                    ->where('created_at', '<', $attempt->created_at)
                    ->distinct('question_id')
                    ->count('question_id');
            }

            return (object) [
                'material_title' => $attempt->question->material->title ?? 'Unknown',
                'material_id' => $materialId,
                'difficulty' => $difficulty,
                'created_at' => $attempt->created_at,
                'is_correct' => $attempt->is_correct,
                'previous_hard_count' => $previousHardCount,
                'total_correct' => $correctCountsByMaterial->get($materialId, 0),
            ];
        })->pipe(fn($c): Collection => collect($c));
    }

    /** @return Collection<int, mixed> */
    public function getDetailedUserProgress(?string $userId): Collection
    {
        if (is_null($userId) || $userId === RoleName::GUEST->value) {
            return collect();
        }

        return QuizAttempt::where('user_id', $userId)
            ->with('question')
            ->get()
            ->groupBy(function ($attempt): string {
                $question = $attempt->question;
                $materialId = $question?->material_id ?? 'unknown';
                $difficulty = $question?->difficulty;
                $diffKey = $difficulty instanceof QuestionDifficulty ? $difficulty->value : ($difficulty ?? 'unknown');

                return sprintf('%s-%s', $materialId, $diffKey);
            })
            ->map(function ($attempts, $key) {
                $parts = explode('-', (string) $key, 2);
                $materialId = $parts[0] ?? 'unknown';
                $difficulty = $parts[1] ?? 'unknown';
                $totalAnswered = $attempts->unique('question_id')->count();
                $correctAnswers = $attempts->where('is_correct', true)->unique('question_id')->count();

                return (object) [
                    'material_id' => $materialId,
                    'difficulty' => $difficulty,
                    'total_answered' => $totalAnswered,
                    'correct_answers' => $correctAnswers,
                ];
            })
            ->filter()
            ->values()
            ->pipe(fn($c): Collection => collect($c));
    }

    public function getCorrectAnswersWithAttempts(string $roleName = 'mahasiswa'): Collection
    {
        return QuizAttempt::where('is_correct', 1)
            ->whereHas('user.role', fn($q) => $q->where('role_name', $roleName))
            ->with(['question', 'user'])
            ->get()
            ->groupBy(fn($attempt): string => sprintf('%s-%s', $attempt->user_id, $attempt->question_id))
            ->map(function ($attempts, $key) {
                [$userId, $questionId] = explode('-', $key, 2);
                $firstAttempt = $attempts->sortBy('attempt_number')->first();

                return (object) [
                    'user_id' => $userId,
                    'question_id' => $questionId,
                    'difficulty' => $firstAttempt->question?->difficulty,
                    'attempts_needed' => $firstAttempt->attempt_number,
                ];
            })
            ->values()
            ->pipe(fn($c): Collection => collect($c));
    }

    public function getLeaderboardStats(string $roleName = 'mahasiswa'): Collection
    {
        return User::whereHas('role', fn(Builder $q) => $q->where('role_name', $roleName))
            ->withCount([
                'attemptedQuestions as total_correct_questions' => function ($q): void {
                    $q->where('quiz_attempts.is_correct', true)->distinct();
                },
                'attemptedQuestions as total_attempted' => function ($q): void {
                    $q->distinct();
                },
                'quizAttempts as correct_answers' => function ($q): void {
                    $q->where('is_correct', true);
                },
                'quizAttempts as total_attempts',
                'attemptedQuestions as beginner_completed' => function ($q): void {
                    $q->where('quiz_attempts.is_correct', true)->where('questions.difficulty', QuestionDifficulty::BEGINNER);
                },
                'attemptedQuestions as medium_completed' => function ($q): void {
                    $q->where('quiz_attempts.is_correct', true)->where('questions.difficulty', QuestionDifficulty::MEDIUM);
                },
                'attemptedQuestions as hard_completed' => function ($q): void {
                    $q->where('quiz_attempts.is_correct', true)->whereIn('questions.difficulty', [QuestionDifficulty::HARD, QuestionDifficulty::FINAL]);
                },
            ])
            ->select('id', 'name', 'email')
            ->get()
            ->map(function ($user) {
                $user->completion_date = QuizAttempt::where('user_id', $user->id)->max('updated_at');

                return $user;
            });
    }

    public function saveProgress(array $data): QuizAttempt
    {
        if ($data['user_id'] === RoleName::GUEST->value) {
            return new QuizAttempt($data);
        }

        if (!isset($data['attempt_number'])) {
            $data['attempt_number'] = DB::transaction(fn(): int|float => QuizAttempt::where('user_id', $data['user_id'])
                ->where('question_id', $data['question_id'])
                ->lockForUpdate()
                ->count() + 1);
        }

        $attempt = QuizAttempt::create([
            'user_id' => $data['user_id'],
            'question_id' => $data['question_id'],
            'answer_id' => $data['answer_id'] ?? null,
            'user_response' => $data['user_response'] ?? null,
            'is_correct' => $data['is_correct'] ?? false,
            'score' => $data['attributes']['score'] ?? $data['score'] ?? ($data['is_correct'] ? 100 : 0),
            'attempt_number' => $data['attempt_number'],
            'time_spent' => $data['attributes']['time_spent'] ?? $data['time_spent'] ?? 0,
        ]);

        if (isset($data['attributes']) && is_array($data['attributes'])) {
            $this->updateStudentState($data['user_id'], $data['attributes']);
        }

        return $attempt;
    }

    private function updateStudentState(?string $userId, array $attributes): void
    {
        if (is_null($userId) || $userId === RoleName::GUEST->value) {
            return;
        }

        $this->studentStateRepository->update($userId, [
            StudentStateSchema::XP => $attributes[StudentStateSchema::XP] ?? null,
            StudentStateSchema::LEVEL => $attributes[StudentStateSchema::LEVEL] ?? null,
            StudentStateSchema::STREAK => $attributes[StudentStateSchema::STREAK] ?? null,
            StudentStateSchema::MAX_STREAK => $attributes[StudentStateSchema::MAX_STREAK] ?? null,
            StudentStateSchema::TOTAL_ANSWERED => $attributes[StudentStateSchema::TOTAL_ANSWERED] ?? null,
            StudentStateSchema::CORRECT_COUNT => $attributes[StudentStateSchema::CORRECT_COUNT] ?? null,
            StudentStateSchema::WRONG_COUNT => $attributes[StudentStateSchema::WRONG_COUNT] ?? null,
            StudentStateSchema::HINTS_USED => $attributes[StudentStateSchema::HINTS_USED] ?? null,
            StudentStateSchema::HINTS_AVAILABLE => $attributes[StudentStateSchema::HINTS_AVAILABLE] ?? null,
            'last_active_at' => now(),
        ]);
    }

    public function getLatestAttemptsForQuestions(string $userId, array $questionIds): Collection
    {
        if ($questionIds === []) {
            return collect();
        }

        return QuizAttempt::where('user_id', $userId)
            ->whereIn('question_id', $questionIds)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('question_id')
            ->map(fn($attempts) => $attempts->first())
            ->pipe(fn($c): Collection => collect($c));
    }

    public function getAnsweredQuestionIds(string $userId, string $materialId): Collection
    {
        if ($userId === RoleName::GUEST->value) {
            return collect([]);
        }

        return QuizAttempt::where('user_id', $userId)
            ->whereRelation('question', 'material_id', $materialId)
            ->where('is_correct', true)
            ->distinct()
            ->pluck('question_id');
    }

    public function getAttemptedQuestionIds(string $userId, string $materialId): Collection
    {
        if ($userId === RoleName::GUEST->value) {
            return collect([]);
        }

        return QuizAttempt::where('user_id', $userId)
            ->whereRelation('question', 'material_id', $materialId)
            ->distinct()
            ->pluck('question_id');
    }

    public function resetProgress(string $userId, string $materialId): void
    {
        $questionIds = Question::where('material_id', $materialId)->pluck('id');

        DB::transaction(function () use ($userId, $questionIds): void {
            // 1. Delete attempts
            QuizAttempt::where('user_id', $userId)
                ->whereIn('question_id', $questionIds)
                ->delete();

            // 2. Reset StudentState context for this material
            $state = StudentState::where('user_id', $userId)->first();
            if ($state) {
                $adaptiveState = $state->adaptive_state ?? [];

                // Clear material-specific flags
                unset($adaptiveState['needs_remedial']);
                unset($adaptiveState['remedial_material_id']);
                unset($adaptiveState['forced_easy_count']);
                unset($adaptiveState['last_diagnosis']);

                $state->adaptive_state = $adaptiveState;
                $state->current_session = null; // Reset session buffer
                $state->save();
            }
        });
    }

    public function getStudentCountByMaterial(): Collection
    {
        return QuizAttempt::where('is_correct', true)
            ->with('question')
            ->get()
            ->groupBy(fn($attempt) => $attempt->question?->material_id)
            ->map(fn($attempts, $materialId) => (object) [
                'material_id' => $materialId,
                'student_count' => $attempts->unique('user_id')->count(),
            ])
            ->filter()
            ->values()
            ->pipe(fn($c): Collection => collect($c));
    }

    public function getLastAccessTime(?string $userId, string $materialId): ?string
    {
        if (is_null($userId) || $userId === RoleName::GUEST->value) {
            return null;
        }

        return QuizAttempt::where('user_id', $userId)
            ->whereRelation('question', 'material_id', $materialId)
            ->max('created_at');
    }

    public function getRecentSystemProgress(int $limit): Collection
    {
        return QuizAttempt::with(['user', 'question.material'])->latest()
            ->limit($limit)
            ->get();
    }

    public function getMaterialPerformanceStats(): Collection
    {
        return QuizAttempt::where('is_correct', true)
            ->with(['question', 'user'])
            ->get()
            ->map(fn($attempt) => (object) [
                'material_id' => $attempt->question?->material_id,
                'user_id' => $attempt->user_id,
                'question_id' => $attempt->question_id,
            ])->pipe(fn($c): Collection => collect($c));
    }

    public function getPopularMaterials(int $limit): Collection
    {
        return Material::select('id', 'title')
            ->withCount([
                'questions',
                'quizAttempts as students_count' => function ($q): void {
                    $q->where('is_correct', true)->distinct('user_id');
                },
                'quizAttempts as total_attempts',
            ])
            ->orderByDesc('students_count')
            ->take($limit)
            ->get()
            ->map(function ($material): object {
                $material->completion_rate = $material->total_attempts > 0
                    ? round(($material->students_count / $material->total_attempts) * 100, 1)
                    : 0;

                return $material;
            });
    }

    public function getOrCreateStudentState(?string $userId): StudentState
    {
        $defaults = StudentStateSchema::defaults();

        if (is_null($userId) || $userId === RoleName::GUEST->value) {
            return new StudentState(array_merge($defaults, ['user_id' => RoleName::GUEST->value]));
        }

        return StudentState::firstOrCreate(['user_id' => $userId], array_merge($defaults, [
            'last_active_at' => now(),
        ]));
    }
}
