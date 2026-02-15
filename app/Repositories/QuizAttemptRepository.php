<?php

namespace App\Repositories;

use App\Contracts\Repositories\QuizAttemptRepositoryInterface;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\DB;

class QuizAttemptRepository implements QuizAttemptRepositoryInterface
{
    public function create(array $data)
    {
        // Calculate attempt number if not provided
        if (!isset($data['attempt_number'])) {
            $data['attempt_number'] = $this->countAttempts($data['user_id'], $data['question_id']) + 1;
        }

        return QuizAttempt::create($data);
    }

    public function find($id)
    {
        return QuizAttempt::find($id);
    }

    public function getByUser($userId)
    {
        return QuizAttempt::where('user_id', $userId)
            ->with(['question', 'answer'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getByUserAndQuestion($userId, $questionId)
    {
        return QuizAttempt::where('user_id', $userId)
            ->where('question_id', $questionId)
            ->orderBy('attempt_number', 'asc')
            ->get();
    }

    public function getByMaterial($materialId)
    {
        return QuizAttempt::join('questions', 'quiz_attempts.question_id', '=', 'questions.id')
            ->where('questions.material_id', $materialId)
            ->select('quiz_attempts.*')
            ->with(['question', 'user'])
            ->get();
    }

    public function getBestAttempt($userId, $questionId)
    {
        return QuizAttempt::where('user_id', $userId)
            ->where('question_id', $questionId)
            ->where('is_correct', true)
            ->orderBy('score', 'desc')
            ->orderBy('attempt_number', 'asc')
            ->first();
    }

    public function getLatestAttempt($userId, $questionId)
    {
        return QuizAttempt::where('user_id', $userId)
            ->where('question_id', $questionId)
            ->orderBy('attempt_number', 'desc')
            ->first();
    }

    public function countAttempts($userId, $questionId)
    {
        return QuizAttempt::where('user_id', $userId)
            ->where('question_id', $questionId)
            ->count();
    }

    public function getCorrectAttempts($userId)
    {
        return QuizAttempt::where('user_id', $userId)
            ->where('is_correct', true)
            ->with(['question.material'])
            ->get();
    }

    public function getUserStats($userId)
    {
        return QuizAttempt::select(
            DB::raw('COUNT(DISTINCT question_id) as total_attempted'),
            DB::raw('SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as total_correct'),
            DB::raw('COUNT(*) as total_attempts')
        )
        ->where('user_id', $userId)
        ->first();
    }
}
