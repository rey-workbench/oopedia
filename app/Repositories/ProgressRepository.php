<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Enums\Lms\QuestionDifficulty;
use App\Models\Material;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Models\StudentState;
use App\Schemas\StudentStateSchema;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class ProgressRepository implements ProgressRepositoryInterface
{
    public function getUserProgressStats(?string $userId): Collection
    {
        if (is_null($userId) || $userId === 'guest') {
            return collect();
        }

        return QuizAttempt::where('user_id', $userId)
            ->with('question')
            ->get()
            ->groupBy(fn ($attempt) => $attempt->question?->material_id)
            ->map(function ($attempts, $materialId) {
                $answeredQuestions = $attempts->unique('question_id')->count();
                $correctAnswers    = $attempts->where('is_correct', true)->unique('question_id')->count();

                return (object) [
                    'material_id'        => $materialId,
                    'answered_questions' => $answeredQuestions,
                    'correct_answers'    => $correctAnswers,
                ];
            })
            ->filter()
            ->values()
            ->pipe(fn ($c) => collect($c));
    }

    public function getUserMaterialProgress(?string $userId): Collection
    {
        if (is_null($userId) || $userId === 'guest') {
            return collect();
        }

        return QuizAttempt::where('user_id', $userId)
            ->with('question')
            ->get()
            ->groupBy(fn ($attempt) => $attempt->question?->material_id)
            ->map(function ($attempts, $materialId) {
                $totalAnswered  = $attempts->unique('question_id')->count();
                $correctAnswers = $attempts->where('is_correct', true)->count();

                return (object) [
                    'material_id'     => $materialId,
                    'total_answered'  => $totalAnswered,
                    'correct_answers' => $correctAnswers,
                ];
            })
            ->filter()
            ->values()
            ->pipe(fn ($c) => collect($c));
    }

    public function getRecentActivities(?string $userId, int $limit = 5): Collection
    {
        if (is_null($userId) || $userId === 'guest') {
            return collect();
        }

        $attempts = QuizAttempt::with(['question.material'])
            ->where('user_id', $userId)
            ->where('is_correct', true)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();

        $correctCountsByMaterial = QuizAttempt::where('user_id', $userId)
            ->where('is_correct', true)
            ->with('question')
            ->get()
            ->groupBy(fn ($attempt) => $attempt->question?->material_id)
            ->map(fn ($group) => $group->unique('question_id')->count());

        return $attempts->map(function ($attempt) use ($correctCountsByMaterial) {
            $materialId        = $attempt->question->material_id ?? 0;
            $difficulty        = $attempt->question?->difficulty instanceof QuestionDifficulty ? $attempt->question->difficulty->value : $attempt->question?->difficulty;
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
                'material_title'      => $attempt->question->material->title ?? 'Unknown',
                'material_id'         => $materialId,
                'difficulty'          => $difficulty,
                'created_at'          => $attempt->created_at,
                'is_correct'          => $attempt->is_correct,
                'previous_hard_count' => $previousHardCount,
                'total_correct'       => $correctCountsByMaterial->get($materialId, 0),
            ];
        })->pipe(fn ($c) => collect($c));
    }

    /** @return Collection<int, mixed> */
    public function getDetailedUserProgress(?string $userId): Collection
    {
        if (is_null($userId) || $userId === 'guest') {
            return collect();
        }

        return QuizAttempt::where('user_id', $userId)
            ->with('question')
            ->get()
            ->groupBy(function ($attempt) {
                $question   = $attempt->question;
                $materialId = $question?->material_id ?? 'unknown';
                $difficulty = $question?->difficulty;
                $diffKey    = $difficulty instanceof QuestionDifficulty ? $difficulty->value : ($difficulty ?? 'unknown');

                return "{$materialId}-{$diffKey}";
            })
            ->map(function ($attempts, $key) {
                $parts                     = explode('-', (string) $key, 2);
                $materialId                = $parts[0] ?? 'unknown';
                $difficulty                = $parts[1] ?? 'unknown';
                $totalAnswered             = $attempts->unique('question_id')->count();
                $correctAnswers            = $attempts->where('is_correct', true)->unique('question_id')->count();

                return (object) [
                    'material_id'     => $materialId,
                    'difficulty'      => $difficulty,
                    'total_answered'  => $totalAnswered,
                    'correct_answers' => $correctAnswers,
                ];
            })
            ->filter()
            ->values()
            ->pipe(fn ($c) => collect($c));
    }

    public function getCorrectAnswersWithAttempts(string $roleName = 'mahasiswa'): Collection
    {
        return QuizAttempt::where('is_correct', 1)
            ->whereHas('user.role', fn ($q) => $q->where('role_name', $roleName))
            ->with(['question', 'user'])
            ->get()
            ->groupBy(fn ($attempt) => "{$attempt->user_id}-{$attempt->question_id}")
            ->map(function ($attempts, $key) {
                [$userId, $questionId] = explode('-', $key, 2);
                $firstAttempt          = $attempts->sortBy('attempt_number')->first();

                return (object) [
                    'user_id'         => $userId,
                    'question_id'     => $questionId,
                    'difficulty'      => $firstAttempt->question?->difficulty,
                    'attempts_needed' => $firstAttempt->attempt_number,
                ];
            })
            ->values()
            ->pipe(fn ($c) => collect($c));
    }

    public function getLeaderboardStats(string $roleName = 'mahasiswa'): Collection
    {
        return QuizAttempt::whereHas('user.role', fn ($q) => $q->where('role_name', $roleName))
            ->with(['user', 'question'])
            ->get()
            ->groupBy('user_id')
            ->map(function ($attempts, $userId) {
                $user                  = $attempts->first()->user;
                $totalCorrectQuestions = $attempts->where('is_correct', true)->unique('question_id')->count();
                $totalAttempted        = $attempts->unique('question_id')->count();
                $correctAnswers        = $attempts->where('is_correct', true)->count();
                $totalAttempts         = $attempts->count();
                $completionDate        = $attempts->max('updated_at');

                $beginnerCompleted = $attempts->where('is_correct', true)
                    ->filter(fn ($a) => ($a->question?->difficulty instanceof QuestionDifficulty ? $a->question->difficulty->value : $a->question?->difficulty) === 'beginner')
                    ->unique('question_id')
                    ->count();
                $mediumCompleted = $attempts->where('is_correct', true)
                    ->filter(fn ($a) => ($a->question?->difficulty instanceof QuestionDifficulty ? $a->question->difficulty->value : $a->question?->difficulty) === 'medium')
                    ->unique('question_id')
                    ->count();
                $hardCompleted = $attempts->where('is_correct', true)
                    ->filter(fn ($a) => ($a->question?->difficulty instanceof QuestionDifficulty ? $a->question->difficulty->value : $a->question?->difficulty) === 'hard')
                    ->unique('question_id')
                    ->count();

                return (object) [
                    'id'                      => $userId,
                    'name'                    => $user?->name,
                    'email'                   => $user?->email,
                    'total_correct_questions' => $totalCorrectQuestions,
                    'total_attempted'         => $totalAttempted,
                    'correct_answers'         => $correctAnswers,
                    'completion_date'         => $completionDate,
                    'beginner_completed'      => $beginnerCompleted,
                    'medium_completed'        => $mediumCompleted,
                    'hard_completed'          => $hardCompleted,
                    'total_attempts'          => $totalAttempts,
                ];
            })
            ->values()
            ->pipe(fn ($c) => collect($c));
    }

    public function getAttemptCount(string $userId, string $materialId, string $questionId): int
    {
        if ($userId === 'guest') {
            return 0;
        }

        return QuizAttempt::where('user_id', $userId)
            ->where('question_id', $questionId)
            ->count();
    }

    public function saveProgress(array $data): QuizAttempt
    {
        if ($data['user_id'] === 'guest') {
            return new QuizAttempt($data);
        }

        if (! isset($data['attempt_number'])) {
            $data['attempt_number'] = DB::transaction(function () use ($data) {
                return QuizAttempt::where('user_id', $data['user_id'])
                    ->where('question_id', $data['question_id'])
                    ->lockForUpdate()
                    ->count() + 1;
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
        if (is_null($userId) || $userId === 'guest') {
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

    public function getLatestAttemptsForQuestions(string $userId, array $questionIds): Collection
    {
        if (empty($questionIds)) {
            return collect();
        }

        return QuizAttempt::where('user_id', $userId)
            ->whereIn('question_id', $questionIds)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('question_id')
            ->map(fn ($attempts) => $attempts->first())
            ->pipe(fn ($c) => collect($c));
    }

    public function getAnsweredQuestionIds(string $userId, string $materialId): Collection
    {
        if ($userId === 'guest') {
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
        if ($userId === 'guest') {
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

        QuizAttempt::where('user_id', $userId)
            ->whereIn('question_id', $questionIds)
            ->delete();
    }

    public function getStudentCountByMaterial(): Collection
    {
        return QuizAttempt::where('is_correct', true)
            ->with('question')
            ->get()
            ->groupBy(fn ($attempt) => $attempt->question?->material_id)
            ->map(function ($attempts, $materialId) {
                return (object) [
                    'material_id'   => $materialId,
                    'student_count' => $attempts->unique('user_id')->count(),
                ];
            })
            ->filter()
            ->values()
            ->pipe(fn ($c) => collect($c));
    }

    public function getLastAccessTime(?string $userId, string $materialId): ?string
    {
        if (is_null($userId) || $userId === 'guest') {
            return null;
        }

        return QuizAttempt::where('user_id', $userId)
            ->whereRelation('question', 'material_id', $materialId)
            ->max('created_at');
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
        return QuizAttempt::where('is_correct', true)
            ->with(['question', 'user'])
            ->get()
            ->map(fn ($attempt) => (object) [
                'material_id' => $attempt->question?->material_id,
                'user_id'     => $attempt->user_id,
                'question_id' => $attempt->question_id,
            ])->pipe(fn ($c) => collect($c));
    }

    public function getPopularMaterials(int $limit): Collection
    {
        return Material::withCount(['questions'])
            ->get()
            ->map(function ($material) {
                $correctAttempts = QuizAttempt::whereRelation('question', 'material_id', $material->id)
                    ->where('is_correct', true)
                    ->distinct('user_id')
                    ->count('user_id');

                $totalAttempts = QuizAttempt::whereRelation('question', 'material_id', $material->id)
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
            ->values()
            ->pipe(fn ($c) => collect($c));
    }

    public function getByUserAndMaterial(string $userId, string $materialId): Collection
    {
        if ($userId === 'guest') {
            return collect();
        }

        return QuizAttempt::where('user_id', $userId)
            ->whereRelation('question', 'material_id', $materialId)
            ->with(['question', 'answer'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->pipe(fn ($c) => collect($c));
    }

    public function getWrongAnswers(string $userId, string $materialId): Collection
    {
        if ($userId === 'guest') {
            return collect();
        }

        return QuizAttempt::where('user_id', $userId)
            ->whereRelation('question', 'material_id', $materialId)
            ->where('is_correct', false)
            ->with(['question', 'answer'])
            ->get()
            ->pipe(fn ($c) => collect($c));
    }

    public function getConsecutiveFailures(?string $userId, string $questionId): int
    {
        if (is_null($userId)) {
            return 0;
        }

        $attempts = QuizAttempt::where('user_id', $userId)
            ->where('question_id', $questionId)
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

    public function getLatestErrorType(?string $userId, string $questionId): ?string
    {
        if (is_null($userId)) {
            return null;
        }

        $attempt = QuizAttempt::where('user_id', $userId)
            ->where('question_id', $questionId)
            ->where('is_correct', false)
            ->latest()
            ->first();

        return $attempt?->error_type ?? 'logic';
    }

    public function getStudentState(?string $userId): ?StudentState
    {
        if (is_null($userId) || $userId === 'guest') {
            return null;
        }

        return StudentState::where('user_id', $userId)->first();
    }

    public function getOrCreateStudentState(?string $userId): StudentState
    {
        if (is_null($userId) || $userId === 'guest') {
            return new StudentState([
                'user_id'             => 'guest',
                'gamification_data'   => StudentStateSchema::getDefaultGamification(),
                'learning_profile'    => StudentStateSchema::getDefaultLearningProfile(),
                'performance_metrics' => StudentStateSchema::getDefaultPerformanceMetrics(),
                'adaptive_state'      => StudentStateSchema::getDefaultAdaptiveState(),
            ]);
        }

        return StudentState::firstOrCreate(['user_id' => $userId], [
            'gamification_data'   => StudentStateSchema::getDefaultGamification(),
            'learning_profile'    => StudentStateSchema::getDefaultLearningProfile(),
            'performance_metrics' => StudentStateSchema::getDefaultPerformanceMetrics(),
            'adaptive_state'      => StudentStateSchema::getDefaultAdaptiveState(),
            'last_active_at'      => now(),
        ]);
    }

    public function getUserMaterialProgressWithState(?string $userId, string $materialId): array
    {
        if (is_null($userId) || $userId === 'guest') {
            return [
                'state'    => $this->getOrCreateStudentState('guest'),
                'progress' => new Collection,
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
