<?php

namespace App\Repositories;

use App\Contracts\Repositories\QuizAttemptRepositoryInterface;
use App\Models\QuizAttempt;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class QuizAttemptRepository implements QuizAttemptRepositoryInterface
{
    public function create(array $data): QuizAttempt
    {
        return DB::transaction(function () use ($data) {
            if (! isset($data['attempt_number'])) {
                $data['attempt_number'] = QuizAttempt::where('user_id', $data['user_id'])
                    ->where('question_id', $data['question_id'])
                    ->lockForUpdate()
                    ->count() + 1;
            }

            return QuizAttempt::create($data);
        });
    }

    public function find(int $id): ?QuizAttempt
    {
        return QuizAttempt::find($id);
    }

    /** @return Collection<int, QuizAttempt> */
    public function getByUser(int $userId): Collection
    {
        return QuizAttempt::where('user_id', $userId)
            ->with(['question', 'answer'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /** @return Collection<int, QuizAttempt> */
    public function getByUserAndQuestion(int $userId, int $questionId): Collection
    {
        return QuizAttempt::where('user_id', $userId)
            ->where('question_id', $questionId)
            ->orderBy('attempt_number', 'asc')
            ->get();
    }

    /** @return Collection<int, QuizAttempt> */
    public function getByMaterial(int $materialId): Collection
    {
        return QuizAttempt::join('questions', 'quiz_attempts.question_id', '=', 'questions.id')
            ->where('questions.material_id', $materialId)
            ->select('quiz_attempts.*')
            ->with(['question', 'user'])
            ->get();
    }

    public function getBestAttempt(int $userId, int $questionId): ?QuizAttempt
    {
        return QuizAttempt::where('user_id', $userId)
            ->where('question_id', $questionId)
            ->where('is_correct', true)
            ->orderBy('score', 'desc')
            ->orderBy('attempt_number', 'asc')
            ->first();
    }

    public function getLatestAttempt(int $userId, int $questionId): ?QuizAttempt
    {
        return QuizAttempt::where('user_id', $userId)
            ->where('question_id', $questionId)
            ->orderBy('attempt_number', 'desc')
            ->first();
    }

    public function countAttempts(int $userId, int $questionId): int
    {
        return QuizAttempt::where('user_id', $userId)
            ->where('question_id', $questionId)
            ->count();
    }

    /** @return Collection<int, QuizAttempt> */
    public function getCorrectAttempts(int $userId): Collection
    {
        return QuizAttempt::where('user_id', $userId)
            ->where('is_correct', true)
            ->with(['question.material'])
            ->get();
    }

    /** @return array<string, mixed> */
    public function getUserStats(int $userId): array
    {
        return (array) QuizAttempt::select(
            DB::raw('COUNT(DISTINCT question_id) as total_attempted'),
            DB::raw('SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as total_correct'),
            DB::raw('COUNT(*) as total_attempts'),
        )
            ->where('user_id', $userId)
            ->first();
    }
}
