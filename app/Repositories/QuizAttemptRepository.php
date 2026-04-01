<?php

namespace App\Repositories;

use App\Contracts\Repositories\QuizAttemptRepositoryInterface;
use App\Models\QuizAttempt;
use Illuminate\Database\Eloquent\Collection;

class QuizAttemptRepository implements QuizAttemptRepositoryInterface
{
    public function create(array $data): QuizAttempt
    {
        return DB::transaction(function () use ($data) {
            if (! isset($data['attempt_number'])) {
                $data['attempt_number'] = QuizAttempt::where('user_id', '=', $data['user_id'])
                    ->where('question_id', '=', $data['question_id'])
                    ->lockForUpdate()
                    ->count('*') + 1;
            }

            return QuizAttempt::create($data);
        });
    }

    public function find(string $id): ?QuizAttempt
    {
        return QuizAttempt::find($id, ['*']);
    }

    /** @return Collection<int, QuizAttempt> */
    public function getByUser(string $userId): Collection
    {
        return QuizAttempt::where('user_id', '=', $userId)
            ->with(['question', 'answer'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /** @return Collection<int, QuizAttempt> */
    public function getByUserAndQuestion(string $userId, string $questionId): Collection
    {
        return QuizAttempt::where('user_id', '=', $userId)
            ->where('question_id', '=', $questionId)
            ->orderBy('attempt_number', 'asc')
            ->get();
    }

    /** @return Collection<int, QuizAttempt> */
    public function getByMaterial(string $materialId): Collection
    {
        return QuizAttempt::join('questions', 'quiz_attempts.question_id', '=', 'questions.id')
            ->where('questions.material_id', $materialId)
            ->select('quiz_attempts.*')
            ->with(['question', 'user'])
            ->get();
    }

    public function getBestAttempt(string $userId, string $questionId): ?QuizAttempt
    {
        return QuizAttempt::where('user_id', '=', $userId)
            ->where('question_id', '=', $questionId)
            ->where('is_correct', '=', true)
            ->orderBy('score', 'desc')
            ->orderBy('attempt_number', 'asc')
            ->first();
    }

    public function getLatestAttempt(string $userId, string $questionId): ?QuizAttempt
    {
        return QuizAttempt::where('user_id', '=', $userId)
            ->where('question_id', '=', $questionId)
            ->orderBy('attempt_number', 'desc')
            ->first();
    }

    public function countAttempts(string $userId, string $questionId): int
    {
        return QuizAttempt::where('user_id', '=', $userId)
            ->where('question_id', '=', $questionId)
            ->count('*');
    }

    /** @return Collection<int, QuizAttempt> */
    public function getCorrectAttempts(string $userId): Collection
    {
        return QuizAttempt::where('user_id', '=', $userId)
            ->where('is_correct', '=', true)
            ->with(['question.material'])
            ->get();
    }

    /** @return array<string, mixed> */
    public function getUserStats(string $userId): array
    {
        $totalAttempted = QuizAttempt::where('user_id', $userId)
            ->distinct('question_id')
            ->count('question_id');

        $totalCorrect = QuizAttempt::where('user_id', $userId)
            ->where('is_correct', true)
            ->count('id');

        $totalAttempts = QuizAttempt::where('user_id', $userId)
            ->count('id');

        return [
            'total_attempted' => $totalAttempted,
            'total_correct'   => $totalCorrect,
            'total_attempts'  => $totalAttempts,
        ];
    }
}
