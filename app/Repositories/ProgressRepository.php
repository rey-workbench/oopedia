<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Models\Material;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Models\StudentState;
use App\Schemas\StudentStateSchema;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProgressRepository implements ProgressRepositoryInterface
{
    /** @return Collection<int, mixed> */
    public function getUserProgressStats(?string $userId): Collection
    {
        if (is_null($userId)) {
            return new Collection();
        }

        return QuizAttempt::join('questions', 'quiz_attempts.question_id', '=', 'questions.id')
            ->select('questions.material_id')
            ->selectRaw('COUNT(DISTINCT quiz_attempts.question_id) as answered_questions')
            ->selectRaw(
                'COUNT(DISTINCT CASE WHEN quiz_attempts.is_correct = 1 '
                . 'THEN quiz_attempts.question_id END) as correct_answers',
            )
            ->where('quiz_attempts.user_id', $userId)
            ->groupBy('questions.material_id')
            ->get();
    }

    /** @return Collection<int, mixed> */
    public function getUserMaterialProgress(?string $userId): Collection
    {
        if (is_null($userId)) {
            return new Collection();
        }

        return QuizAttempt::join('questions', 'quiz_attempts.question_id', '=', 'questions.id')
            ->select('questions.material_id')
            ->selectRaw('COUNT(DISTINCT quiz_attempts.question_id) as total_answered')
            ->selectRaw(
                'SUM(CASE WHEN quiz_attempts.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers',
            )
            ->where('quiz_attempts.user_id', $userId)
            ->groupBy('questions.material_id')
            ->get();
    }

    public function getRecentActivities(?string $userId, int $limit = 5): \Illuminate\Support\Collection
    {
        if (is_null($userId)) {
            return collect();
        }

        $attempts = QuizAttempt::with(['question.material'])
            ->where('user_id', '=', $userId)
            ->where('is_correct', '=', true)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();

        $correctCountsByMaterial = QuizAttempt::join('questions', 'quiz_attempts.question_id', '=', 'questions.id')
            ->where('quiz_attempts.user_id', $userId)
            ->where('quiz_attempts.is_correct', true)
            ->select('questions.material_id')
            ->selectRaw('COUNT(DISTINCT quiz_attempts.question_id) as count')
            ->groupBy('questions.material_id')
            ->pluck('count', 'material_id');

        return $attempts->map(function ($attempt) use ($correctCountsByMaterial) {
            $materialId = $attempt->question->material_id ?? 0;
            $difficulty = $attempt->question->difficulty;

            $previousHardCount = 0;
            if ($difficulty === 'hard') {
                $previousHardCount = QuizAttempt::join('questions', 'quiz_attempts.question_id', '=', 'questions.id')
                    ->where('quiz_attempts.user_id', $attempt->user_id)
                    ->where('questions.material_id', $materialId)
                    ->where('questions.difficulty', 'hard')
                    ->where('quiz_attempts.is_correct', true)
                    ->where(
                        'quiz_attempts.created_at',
                        '<',
                        $attempt->created_at,
                    )
                    ->distinct('quiz_attempts.question_id')
                    ->count('*');
            }

            return (object) [
                'material_title'      => $attempt->question->material->title ?? 'Unknown',
                'material_id'         => $materialId,
                'difficulty'          => $difficulty,
                'created_at'          => $attempt->created_at,
                'is_correct'          => $attempt->is_correct,
                'previous_hard_count' => $previousHardCount,
                'total_correct'       => $correctCountsByMaterial->get($materialId, 0),
            ];
        });
    }

    /** @return Collection<int, mixed> */
    public function getDetailedUserProgress(?string $userId): Collection
    {
        if (is_null($userId)) {
            return new Collection();
        }

        return QuizAttempt::join('questions', 'quiz_attempts.question_id', '=', 'questions.id')
            ->select('questions.material_id', 'questions.difficulty')
            ->selectRaw('COUNT(DISTINCT quiz_attempts.question_id) as total_answered')
            ->selectRaw(
                'SUM(CASE WHEN quiz_attempts.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers',
            )
            ->where('quiz_attempts.user_id', $userId)
            ->groupBy('questions.material_id', 'questions.difficulty')
            ->get();
    }

    /** @return Collection<int, mixed> */
    public function getCorrectAnswersWithAttempts(string $roleName = 'mahasiswa'): Collection
    {
        return QuizAttempt::join('questions', 'quiz_attempts.question_id', '=', 'questions.id')
            ->join('users', 'quiz_attempts.user_id', '=', 'users.id')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->select(
                'quiz_attempts.user_id',
                'quiz_attempts.question_id',
                'questions.difficulty',
            )
            ->selectRaw('MIN(quiz_attempts.attempt_number) as attempts_needed')
            ->where('quiz_attempts.is_correct', 1)
            ->where('roles.role_name', $roleName)
            ->groupBy(
                'quiz_attempts.user_id',
                'quiz_attempts.question_id',
                'questions.difficulty',
            )
            ->get();
    }

    /** @return Collection<int, mixed> */
    public function getLeaderboardStats(string $roleName = 'mahasiswa'): Collection
    {
        return QuizAttempt::join('users', 'quiz_attempts.user_id', '=', 'users.id')
            ->leftJoin('questions', 'quiz_attempts.question_id', '=', 'questions.id')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
            )
            ->selectRaw(
                'COUNT(DISTINCT CASE WHEN quiz_attempts.is_correct = 1 '
                . 'THEN quiz_attempts.question_id END) as total_correct_questions',
            )
            ->selectRaw('COUNT(DISTINCT quiz_attempts.question_id) as total_attempted')
            ->selectRaw('SUM(CASE WHEN quiz_attempts.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers')
            ->selectRaw('MAX(quiz_attempts.updated_at) as completion_date')
            ->selectRaw(
                'COUNT(DISTINCT CASE WHEN quiz_attempts.is_correct = 1 '
                . 'AND questions.difficulty = "beginner" '
                . 'THEN quiz_attempts.question_id END) as beginner_completed',
            )
            ->selectRaw(
                'COUNT(DISTINCT CASE WHEN quiz_attempts.is_correct = 1 '
                . 'AND questions.difficulty = "medium" '
                . 'THEN quiz_attempts.question_id END) as medium_completed',
            )
            ->selectRaw(
                'COUNT(DISTINCT CASE WHEN quiz_attempts.is_correct = 1 '
                . 'AND questions.difficulty = "hard" '
                . 'THEN quiz_attempts.question_id END) as hard_completed',
            )
            ->selectRaw('COUNT(quiz_attempts.id) as total_attempts')
            ->where('roles.role_name', $roleName)
            ->groupBy('users.id', 'users.name', 'users.email')
            ->get();
    }

    public function getAttemptCount(string $userId, string $materialId, string $questionId): int
    {
        return QuizAttempt::where('user_id', '=', $userId)
            ->where('question_id', '=', $questionId)
            ->count('*');
    }

    public function saveProgress(array $data): QuizAttempt
    {
        if (! isset($data['attempt_number'])) {
            $data['attempt_number'] = DB::transaction(function () use ($data) {
                return QuizAttempt::where('user_id', '=', $data['user_id'])
                    ->where('question_id', '=', $data['question_id'])
                    ->lockForUpdate()
                    ->count('*') + 1;
            });
        }

        $attempt = QuizAttempt::create([
            'user_id'        => $data['user_id'],
            'question_id'    => $data['question_id'],
            'answer_id'      => $data['answer_id']           ?? null,
            'user_response'  => $data['user_response']       ?? null,
            'is_correct'     => $data['is_correct']          ?? false,
            'score'          => $data['attributes']['score'] ?? $data['score'] ?? ($data['is_correct'] ? 100 : 0),
            'attempt_number' => $data['attempt_number'],
            'time_spent'     => $data['attributes']['time_spent'] ?? $data['time_spent'] ?? 0,
        ]);

        if (isset($data['attributes']) && is_array($data['attributes'])) {
            $this->updateStudentState($data['user_id'], $data['attributes']);
        }

        return $attempt;
    }

    public function updateStudentState(?string $userId, array $attributes): void
    {
        if (is_null($userId)) {
            return;
        }

        $state = StudentState::firstOrNew(['user_id' => $userId]);

        $state->gamification_data = array_merge($state->gamification_data ?? [], [
            StudentStateSchema::KEY_GLOBAL_XP => $attributes[StudentStateSchema::KEY_GLOBAL_XP]
                ?? ($state->gamification_data[StudentStateSchema::KEY_GLOBAL_XP] ?? 0),
            StudentStateSchema::KEY_CURRENT_LEVEL => $attributes[StudentStateSchema::KEY_CURRENT_LEVEL]
                ?? ($state->gamification_data[StudentStateSchema::KEY_CURRENT_LEVEL] ?? 'Pemula'),
            StudentStateSchema::KEY_CURRENT_STREAK => $attributes[StudentStateSchema::KEY_CURRENT_STREAK]
                ?? ($state->gamification_data[StudentStateSchema::KEY_CURRENT_STREAK] ?? 0),
            StudentStateSchema::KEY_MAX_STREAK => $attributes[StudentStateSchema::KEY_MAX_STREAK]
                ?? ($state->gamification_data[StudentStateSchema::KEY_MAX_STREAK] ?? 0),
        ]);

        $state->performance_metrics = array_merge($state->performance_metrics ?? [], [
            StudentStateSchema::KEY_TOTAL_QUESTIONS_ANSWERED => $attributes[
                StudentStateSchema::KEY_TOTAL_QUESTIONS_ANSWERED
            ]
                ?? ($state->performance_metrics[StudentStateSchema::KEY_TOTAL_QUESTIONS_ANSWERED] ?? 0),
            StudentStateSchema::KEY_CORRECT_COUNT => $attributes[StudentStateSchema::KEY_CORRECT_COUNT]
                ?? ($state->performance_metrics[StudentStateSchema::KEY_CORRECT_COUNT] ?? 0),
            StudentStateSchema::KEY_WRONG_COUNT => $attributes[StudentStateSchema::KEY_WRONG_COUNT]
                ?? ($state->performance_metrics[StudentStateSchema::KEY_WRONG_COUNT] ?? 0),
            StudentStateSchema::KEY_WRONG_STREAK => $attributes[StudentStateSchema::KEY_WRONG_STREAK]
                ?? ($state->performance_metrics[StudentStateSchema::KEY_WRONG_STREAK] ?? 0),
            StudentStateSchema::KEY_HINTS_USED_COUNT => $attributes[StudentStateSchema::KEY_HINTS_USED_COUNT]
                ?? ($state->performance_metrics[StudentStateSchema::KEY_HINTS_USED_COUNT] ?? 0),
            StudentStateSchema::KEY_HINTS_AVAILABLE => $attributes[StudentStateSchema::KEY_HINTS_AVAILABLE]
                ?? ($state->performance_metrics[StudentStateSchema::KEY_HINTS_AVAILABLE]
                    ?? StudentStateSchema::DEFAULT_HINTS_AVAILABLE),
        ]);

        $state->learning_profile = array_merge($state->learning_profile ?? [], [
            StudentStateSchema::KEY_LEARNING_STYLE => $attributes[StudentStateSchema::KEY_LEARNING_STYLE]
                ?? ($state->learning_profile[StudentStateSchema::KEY_LEARNING_STYLE] ?? 'visual'),
        ]);

        $state->last_active_at = now();
        $state->save();
    }

    public function updateOrCreateProgress(array $conditions, array $values): void
    {
        $data = array_merge($conditions, $values);

        $this->saveProgress($data);
    }

    /** @param  array<int, int>  $questionIds
     * @return \Illuminate\Support\Collection<int, QuizAttempt> keyed by question_id
     */
    public function getLatestAttemptsForQuestions(string $userId, array $questionIds): \Illuminate\Support\Collection
    {
        if (empty($questionIds)) {
            return collect();
        }

        return QuizAttempt::where('user_id', '=', $userId)
            ->whereIn('question_id', $questionIds)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('question_id')
            ->map(fn ($attempts) => $attempts->first());
    }

    /** @return \Illuminate\Support\Collection<int, int> */
    public function getAnsweredQuestionIds(string $userId, string $materialId): \Illuminate\Support\Collection
    {
        return QuizAttempt::join('questions', 'quiz_attempts.question_id', '=', 'questions.id')
            ->where('quiz_attempts.user_id', $userId)
            ->where('questions.material_id', $materialId)
            ->where('quiz_attempts.is_correct', true)
            ->distinct()
            ->pluck('quiz_attempts.question_id');
    }

    public function resetProgress(string $userId, string $materialId): void
    {
        $questionIds = Question::where('material_id', '=', $materialId)->pluck('id');

        QuizAttempt::where('user_id', '=', $userId)
            ->whereIn('question_id', $questionIds)
            ->delete();
    }

    public function getStudentCountByMaterial(): Collection
    {
        return QuizAttempt::join('questions', 'quiz_attempts.question_id', '=', 'questions.id')
            ->select('questions.material_id')
            ->selectRaw('COUNT(DISTINCT quiz_attempts.user_id) as student_count')
            ->groupBy('questions.material_id')
            ->get()
            ->keyBy('material_id');
    }

    public function getLastAccessTime(?string $userId, string $materialId): ?string
    {
        if (is_null($userId)) {
            return null;
        }

        return QuizAttempt::join('questions', 'quiz_attempts.question_id', '=', 'questions.id')
            ->where('quiz_attempts.user_id', $userId)
            ->where('questions.material_id', $materialId)
            ->max('quiz_attempts.created_at');
    }

    public function getRecentSystemProgress(int $limit): Collection
    {
        return QuizAttempt::with(['user', 'question.material'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getMaterialPerformanceStats(): Collection
    {
        return QuizAttempt::join('questions', 'quiz_attempts.question_id', '=', 'questions.id')
            ->where('quiz_attempts.is_correct', true)
            ->select('questions.material_id', 'quiz_attempts.user_id', 'quiz_attempts.question_id')
            ->get();
    }

    public function getPopularMaterials(int $limit): Collection
    {
        return Material::withCount(['questions'])
            ->get()
            ->map(function ($material) {
                $correctAttempts = QuizAttempt::whereHas('question', fn ($q) => $q->where('material_id', $material->id))
                    ->where('is_correct', true)
                    ->distinct('user_id')
                    ->count('user_id');

                $totalAttempts = QuizAttempt::whereHas('question', fn ($q) => $q->where('material_id', $material->id))
                    ->distinct('id')
                    ->count('id');

                $completionRate = $totalAttempts > 0
                    ? round(($correctAttempts / $totalAttempts) * 100, 1)
                    : 0;

                return (object) [
                    'id'              => $material->id,
                    'title'           => $material->title,
                    'students_count'  => $correctAttempts,
                    'completion_rate' => $completionRate,
                ];
            })
            ->sortByDesc('students_count')
            ->take($limit)
            ->values();
    }

    public function getByUserAndMaterial(string $userId, string $materialId): Collection
    {
        return QuizAttempt::join('questions', 'quiz_attempts.question_id', '=', 'questions.id')
            ->where('quiz_attempts.user_id', $userId)
            ->where('questions.material_id', $materialId)
            ->orderBy('quiz_attempts.created_at', 'asc')
            ->select('quiz_attempts.*')
            ->get();
    }

    public function getWrongAnswers(string $userId, string $materialId): Collection
    {
        return QuizAttempt::join('questions', 'quiz_attempts.question_id', '=', 'questions.id')
            ->where('quiz_attempts.user_id', $userId)
            ->where('questions.material_id', $materialId)
            ->where('quiz_attempts.is_correct', false)
            ->select('quiz_attempts.*')
            ->get();
    }

    /**
     * Get consecutive failures for a question (for G22 - Persistent Fail).
     * Returns count of consecutive wrong attempts.
     */
    public function getConsecutiveFailures(?string $userId, string $questionId): int
    {
        if (is_null($userId)) {
            return 0;
        }

        $attempts = QuizAttempt::where('user_id', '=', $userId)
            ->where('question_id', '=', $questionId)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $consecutiveFails = 0;
        foreach ($attempts as $attempt) {
            if ($attempt->is_correct) {
                break;
            }
            $consecutiveFails++;
        }

        return $consecutiveFails;
    }

    /**
     * Get error type from latest attempt (for G09/G10).
     * Returns 'syntax', 'logic', or null.
     */
    public function getLatestErrorType(?string $userId, string $questionId): ?string
    {
        if (is_null($userId)) {
            return null;
        }

        $attempt = QuizAttempt::where('user_id', '=', $userId)
            ->where('question_id', '=', $questionId)
            ->where('is_correct', '=', false)
            ->latest()
            ->first();

        return $attempt?->error_type ?? 'logic';
    }

    public function getStudentState(?string $userId): ?StudentState
    {
        if (is_null($userId)) {
            return null;
        }

        return StudentState::where('user_id', '=', $userId)->first();
    }

    public function getOrCreateStudentState(?string $userId): StudentState
    {
        if (is_null($userId)) {
            return new StudentState();
        }

        return StudentState::firstOrCreate(['user_id' => $userId], [
            'gamification_data'   => [],
            'learning_profile'    => [],
            'performance_metrics' => [],
            'adaptive_state'      => [],
            'last_active_at'      => now(),
        ]);
    }

    /** @return array<string, mixed> */
    public function getUserMaterialProgressWithState(?string $userId, string $materialId): array
    {
        if (is_null($userId)) {
            return [
                'state'    => new StudentState(),
                'progress' => new Collection(),
            ];
        }

        $state    = $this->getOrCreateStudentState($userId);
        $progress = $this->getUserMaterialProgress($userId);

        return [
            'state'    => $state,
            'progress' => $progress,
        ];
    }
}
