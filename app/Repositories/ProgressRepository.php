<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Models\Material;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Models\StudentState;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProgressRepository implements ProgressRepositoryInterface
{
    /** @return array<string, mixed> */
    public function getUserProgressStats(int|string|null $userId): \Illuminate\Database\Eloquent\Collection
    {
        if (is_null($userId)) {
            return new Collection();
        }

        // Join with Questions to get material_id
        return QuizAttempt::join('questions', 'quiz_attempts.question_id', '=', 'questions.id')
            ->select('questions.material_id')
            ->selectRaw('COUNT(DISTINCT quiz_attempts.question_id) as answered_questions')
            ->selectRaw('SUM(CASE WHEN quiz_attempts.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers')
            ->where('quiz_attempts.user_id', $userId)
            ->groupBy('questions.material_id')
            ->get();
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, mixed> */
    public function getUserMaterialProgress(int|string|null $userId): \Illuminate\Database\Eloquent\Collection
    {
        if (is_null($userId)) {
            return new Collection();
        }

        return QuizAttempt::join('questions', 'quiz_attempts.question_id', '=', 'questions.id')
            ->select('questions.material_id')
            ->selectRaw('COUNT(DISTINCT quiz_attempts.question_id) as total_answered')
            ->selectRaw('SUM(CASE WHEN quiz_attempts.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers')
            ->where('quiz_attempts.user_id', $userId)
            ->groupBy('questions.material_id')
            ->get();
    }

    /** @return \Illuminate\Support\Collection<int, mixed> */
    public function getRecentActivities(int|string|null $userId, int $limit = 5): \Illuminate\Support\Collection
    {
        if (is_null($userId)) {
            return collect();
        }

        // Get latest correct attempts
        return QuizAttempt::with(['question.material'])
            ->where('user_id', $userId)
            ->where('is_correct', true)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get()
            ->map(function ($attempt) use ($userId) {
                $materialId = $attempt->question->material_id ?? 0;
                $difficulty = $attempt->question->difficulty;

                // Count how many hard questions completed in this material BEFORE this attempt
                $previousHardCount = 0;
                if ($difficulty === 'hard') {
                    $previousHardCount = QuizAttempt::join('questions', 'quiz_attempts.question_id', '=', 'questions.id')
                        ->where('quiz_attempts.user_id', $userId)
                        ->where('questions.material_id', $materialId)
                        ->where('questions.difficulty', 'hard')
                        ->where('quiz_attempts.is_correct', true)
                        ->where('quiz_attempts.created_at', '<', $attempt->created_at)
                        ->distinct('quiz_attempts.question_id')
                        ->count();
                }

                return (object) [
                    'material_title' => $attempt->question->material->title ?? 'Unknown',
                    'material_id' => $materialId,
                    'difficulty' => $attempt->question->difficulty,
                    'created_at' => $attempt->created_at,
                    'is_correct' => $attempt->is_correct,
                    'previous_hard_count' => $previousHardCount,
                    // Total correct count calculation might be expensive per row, simplified here
                    'total_correct' => $this->getMaterialCorrectCount($attempt->user_id, $materialId),
                ];
            });
    }

    protected function getMaterialCorrectCount(int $userId, int $materialId): int
    {
        return QuizAttempt::join('questions', 'quiz_attempts.question_id', '=', 'questions.id')
            ->where('quiz_attempts.user_id', $userId)
            ->where('questions.material_id', $materialId)
            ->where('quiz_attempts.is_correct', true)
            ->distinct('quiz_attempts.question_id')
            ->count();
    }

    /** @return array<string, mixed> */
    public function getDetailedUserProgress(int|string|null $userId): array
    {
        if (is_null($userId)) {
            return [];
        }

        return QuizAttempt::join('questions', 'quiz_attempts.question_id', '=', 'questions.id')
            ->select('questions.material_id', 'questions.difficulty')
            ->selectRaw('COUNT(DISTINCT quiz_attempts.question_id) as total_answered')
            ->selectRaw('SUM(CASE WHEN quiz_attempts.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers')
            ->where('quiz_attempts.user_id', $userId)
            ->groupBy('questions.material_id', 'questions.difficulty')
            ->get();
    }

    /** @return Collection<int, mixed> */
    public function getCorrectAnswersWithAttempts(int $roleId = 3): Collection
    {
        return QuizAttempt::join('questions', 'quiz_attempts.question_id', '=', 'questions.id')
            ->join('users', 'quiz_attempts.user_id', '=', 'users.id')
            ->select(
                'quiz_attempts.user_id',
                'quiz_attempts.question_id',
                'questions.difficulty',
            )
            ->selectRaw('MIN(quiz_attempts.attempt_number) as attempts_needed')
            ->where('quiz_attempts.is_correct', 1)
            ->where('users.role_id', $roleId)
            ->groupBy('quiz_attempts.user_id', 'quiz_attempts.question_id', 'questions.difficulty')
            ->get();
    }

    /** @return array<int, mixed> */
    /** @return \Illuminate\Database\Eloquent\Collection<int, mixed> */
    public function getLeaderboardStats(int $roleId = 3): \Illuminate\Database\Eloquent\Collection
    {
        return QuizAttempt::join('users', 'quiz_attempts.user_id', '=', 'users.id')
            ->leftJoin('questions', 'quiz_attempts.question_id', '=', 'questions.id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
            )
            ->selectRaw('COUNT(DISTINCT CASE WHEN quiz_attempts.is_correct = 1 THEN quiz_attempts.question_id END) as total_correct_questions')
            ->selectRaw('COUNT(DISTINCT quiz_attempts.question_id) as total_attempted')
            ->selectRaw('SUM(CASE WHEN quiz_attempts.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers')
            ->selectRaw('MAX(quiz_attempts.updated_at) as completion_date')
            ->selectRaw('COUNT(DISTINCT CASE WHEN quiz_attempts.is_correct = 1 AND questions.difficulty = "beginner" THEN quiz_attempts.question_id END) as beginner_completed')
            ->selectRaw('COUNT(DISTINCT CASE WHEN quiz_attempts.is_correct = 1 AND questions.difficulty = "medium" THEN quiz_attempts.question_id END) as medium_completed')
            ->selectRaw('COUNT(DISTINCT CASE WHEN quiz_attempts.is_correct = 1 AND questions.difficulty = "hard" THEN quiz_attempts.question_id END) as hard_completed')
            ->selectRaw('COUNT(quiz_attempts.id) as total_attempts')
            ->where('users.role_id', $roleId)
            ->groupBy('users.id', 'users.name', 'users.email')
            ->get();
    }

    public function getAttemptCount(int|string $userId, int $materialId, int $questionId): int
    {
        // Attempts specific to a question
        return QuizAttempt::where('user_id', $userId)
            ->where('question_id', $questionId)
            ->count();
    }

    public function saveProgress(array $data): QuizAttempt
    {
        // 1. Create QuizAttempt
        // Calculate attempt number if not provided
        if (! isset($data['attempt_number'])) {
            $data['attempt_number'] = QuizAttempt::where('user_id', $data['user_id'])
                ->where('question_id', $data['question_id'])
                ->count() + 1;
        }

        $attempt = QuizAttempt::create([
            'user_id' => $data['user_id'],
            'question_id' => $data['question_id'],
            'answer_id' => $data['answer_id'] ?? null,
            'user_response' => $data['user_response'] ?? null,
            'is_correct' => $data['is_correct'] ?? false,
            'score' => $data['score'] ?? ($data['is_correct'] ? 100 : 0),
            'attempt_number' => $data['attempt_number'],
            'time_spent' => 0, // Can be updated later
        ]);

        // 2. Update StudentState if attributes provided
        if (isset($data['attributes']) && is_array($data['attributes'])) {
            $this->updateStudentState($data['user_id'], $data['attributes']);
        }

        return $attempt;
    }

    public function updateStudentState(int|string|null $userId, array $attributes): void
    {
        if (is_null($userId)) {
            return;
        }

        $state = StudentState::firstOrNew(['user_id' => $userId]);

        // 1. Update Gamification Data
        $gamification = $state->gamification_data ?? [];
        $gamification['global_xp'] = $attributes['global_xp'] ?? ($gamification['global_xp'] ?? 0);
        $gamification['current_level'] = $attributes['current_level'] ?? ($gamification['current_level'] ?? 'Pemula');
        $gamification['current_streak'] = $attributes['current_streak'] ?? ($gamification['current_streak'] ?? 0);
        $gamification['max_streak'] = $attributes['max_streak'] ?? ($gamification['max_streak'] ?? 0);
        $state->gamification_data = $gamification;

        // 2. Update Performance Metrics
        $metrics = $state->performance_metrics ?? [];
        $metrics['total_questions_answered'] = $attributes['total_questions_answered'] ?? ($metrics['total_questions_answered'] ?? 0);
        $metrics['correct_count'] = $attributes['correct_count'] ?? ($metrics['correct_count'] ?? 0);
        $metrics['wrong_count'] = $attributes['wrong_count'] ?? ($metrics['wrong_count'] ?? 0);
        $metrics['wrong_streak'] = $attributes['wrong_streak'] ?? ($metrics['wrong_streak'] ?? 0);
        $metrics['hints_used_count'] = $attributes['hints_used_count'] ?? ($metrics['hints_used_count'] ?? 0);
        $metrics['hints_available'] = $attributes['hints_available'] ?? ($metrics['hints_available'] ?? 3);
        $state->performance_metrics = $metrics;

        // 3. Update Learning Profile
        $profile = $state->learning_profile ?? [];
        $profile['learning_style'] = $attributes['learning_style'] ?? ($profile['learning_style'] ?? 'visual');
        $state->learning_profile = $profile;

        $state->last_active_at = now();
        $state->save();
    }

    public function updateOrCreateProgress(array $conditions, array $values): void
    {
        // Adapter for old code: simple create a new attempt
        // We assume strictly that this is a new interaction.
        // Merge conditions (user_id, question_id) with values
        $data = array_merge($conditions, $values);

        $this->saveProgress($data);
    }

    /** @return \Illuminate\Support\Collection<int, int> */
    public function getAnsweredQuestionIds(int|string $userId, int $materialId): \Illuminate\Support\Collection
    {
        return QuizAttempt::join('questions', 'quiz_attempts.question_id', '=', 'questions.id')
            ->where('quiz_attempts.user_id', $userId)
            ->where('questions.material_id', $materialId)
            ->where('quiz_attempts.is_correct', true)
            ->distinct()
            ->pluck('quiz_attempts.question_id');
    }

    public function resetProgress(int|string $userId, int $materialId): void
    {
        $questionIds = Question::where('material_id', $materialId)->pluck('id');

        QuizAttempt::where('user_id', $userId)
            ->whereIn('question_id', $questionIds)
            ->delete();
    }

    public function getStudentCountByMaterial(): \Illuminate\Database\Eloquent\Collection
    {
        return QuizAttempt::join('questions', 'quiz_attempts.question_id', '=', 'questions.id')
            ->select('questions.material_id')
            ->selectRaw('COUNT(DISTINCT quiz_attempts.user_id) as student_count')
            ->groupBy('questions.material_id')
            ->get()
            ->keyBy('material_id');
    }

    public function getLastAccessTime(int|string|null $userId, int $materialId): ?string
    {
        if (is_null($userId)) {
            return null;
        }

        // Join not strictly needed if we just want max created_at for user?
        // But need to filter by material
        return QuizAttempt::join('questions', 'quiz_attempts.question_id', '=', 'questions.id')
            ->where('quiz_attempts.user_id', $userId)
            ->where('questions.material_id', $materialId)
            ->max('quiz_attempts.created_at'); // attempts use created_at roughly as access time
    }

    public function getRecentSystemProgress(int $limit): \Illuminate\Database\Eloquent\Collection
    {
        return QuizAttempt::with(['user', 'question.material'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getMaterialPerformanceStats(): \Illuminate\Database\Eloquent\Collection
    {
        return QuizAttempt::join('questions', 'quiz_attempts.question_id', '=', 'questions.id')
            ->where('quiz_attempts.is_correct', true)
            ->select('questions.material_id', 'quiz_attempts.user_id', 'quiz_attempts.question_id')
            ->get();
    }

    public function getPopularMaterials(int $limit): \Illuminate\Database\Eloquent\Collection
    {
        return Material::query()
            ->leftJoin('questions', 'materials.id', '=', 'questions.material_id')
            ->leftJoin('quiz_attempts', function ($join) {
                $join->on('questions.id', '=', 'quiz_attempts.question_id')
                    ->where('quiz_attempts.is_correct', '=', true);
            })
            ->select(
                'materials.id',
                'materials.title',
                DB::raw('COUNT(DISTINCT quiz_attempts.user_id) as students_count'),
                DB::raw('ROUND(
                    (COUNT(DISTINCT CASE WHEN quiz_attempts.is_correct = 1 THEN quiz_attempts.id ELSE NULL END) * 100.0) /
                    NULLIF(COUNT(DISTINCT quiz_attempts.id), 0),
                    1
                ) as completion_rate'),
            )
            ->groupBy('materials.id', 'materials.title')
            ->orderByDesc('students_count')
            ->limit($limit)
            ->get();
    }

    // ==================== PERSONALIZATION QUERIES ====================

    public function getFirstProgress($userId, $materialId)
    {
        return QuizAttempt::join('questions', 'quiz_attempts.question_id', '=', 'questions.id')
            ->where('quiz_attempts.user_id', $userId)
            ->where('questions.material_id', $materialId)
            ->oldest('quiz_attempts.created_at')
            ->select('quiz_attempts.*')
            ->first();
    }

    public function getLatestProgress($userId)
    {
        return StudentState::where('user_id', $userId)->first();
    }

    public function getByUserAndMaterial(int|string $userId, int $materialId): \Illuminate\Database\Eloquent\Collection
    {
        return QuizAttempt::join('questions', 'quiz_attempts.question_id', '=', 'questions.id')
            ->where('quiz_attempts.user_id', $userId)
            ->where('questions.material_id', $materialId)
            ->orderBy('quiz_attempts.created_at', 'asc')
            ->select('quiz_attempts.*')
            ->get();
    }

    public function getWrongAnswers(int|string $userId, int $materialId): \Illuminate\Database\Eloquent\Collection
    {
        return QuizAttempt::join('questions', 'quiz_attempts.question_id', '=', 'questions.id')
            ->where('quiz_attempts.user_id', $userId)
            ->where('questions.material_id', $materialId)
            ->where('quiz_attempts.is_correct', false)
            // Get latest attempt per question? Or all wrong attempts?
            // "WrongAnswers" implies questions user is currently stuck on.
            // If they answered correctly later, it shouldn't be here.
            // Complex logic. For now, return all wrong attempts.
            ->select('quiz_attempts.*')
            ->get();
    }

    // ==================== ADAPTIVE FACT GATHERING ====================

    /**
     * Get consecutive failures for a question (for G22 - Persistent Fail).
     * Returns count of consecutive wrong attempts.
     */
    public function getConsecutiveFailures(int|string|null $userId, int $questionId): int
    {
        if (is_null($userId)) {
            return 0;
        }

        $attempts = QuizAttempt::where('user_id', $userId)
            ->where('question_id', $questionId)
            ->orderBy('created_at', 'desc')
            ->take(10) // Check last 10 attempts
            ->get();

        $consecutiveFails = 0;
        foreach ($attempts as $attempt) {
            if ($attempt->is_correct) {
                break; // Stop counting if we hit a correct answer
            }
            $consecutiveFails++;
        }

        return $consecutiveFails;
    }

    /**
     * Get error type from latest attempt (for G09/G10).
     * Returns 'syntax', 'logic', or null.
     */
    public function getLatestErrorType(int|string|null $userId, int $questionId): ?string
    {
        if (is_null($userId)) {
            return null;
        }

        $attempt = QuizAttempt::where('user_id', $userId)
            ->where('question_id', $questionId)
            ->where('is_correct', false)
            ->latest()
            ->first();

        return $attempt?->error_type ?? 'logic'; // Default to logic error
    }

    public function getStudentState(int|string|null $userId): ?StudentState
    {
        if (is_null($userId)) {
            return null;
        }

        return StudentState::where('user_id', $userId)->first();
    }

    public function getOrCreateStudentState(int|string|null $userId): StudentState
    {
        if (is_null($userId)) {
            return new StudentState();
        }

        // StudentState is global per user
        return StudentState::firstOrCreate(['user_id' => $userId], [
            'gamification_data' => [],
            'learning_profile' => [],
            'performance_metrics' => [],
            'adaptive_state' => [],
            'last_active_at' => now(),
        ]);
    }

    /** @return array<string, mixed> */
    public function getUserMaterialProgressWithState(int|string|null $userId, int $materialId): array
    {
        if (is_null($userId)) {
            return [
                'state' => new StudentState(),
                'progress' => new Collection(),
            ];
        }

        $state = $this->getOrCreateStudentState($userId);
        $progress = $this->getUserMaterialProgress($userId);

        return [
            'state' => $state,
            'progress' => $progress,
        ];
    }
}
