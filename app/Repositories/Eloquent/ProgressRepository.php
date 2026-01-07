<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\ProgressRepositoryInterface;
use App\Models\Progress;
use Illuminate\Support\Facades\DB;

class ProgressRepository extends BaseRepository implements ProgressRepositoryInterface
{
    public function __construct(Progress $model)
    {
        parent::__construct($model);
    }

    public function getUserProgressStats($userId)
    {
        return $this->model
            ->select('material_id')
            ->selectRaw('COUNT(DISTINCT question_id) as answered_questions')
            ->selectRaw('SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_answers')
            ->where('user_id', $userId)
            ->groupBy('material_id')
            ->get();
    }

    public function getUserMaterialProgress($userId)
    {
        return $this->model
            ->select('material_id')
            ->selectRaw('COUNT(DISTINCT question_id) as total_answered')
            ->selectRaw('SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_answers')
            ->where('user_id', $userId)
            ->groupBy('material_id')
            ->get();
    }

    public function getRecentActivities($userId, $limit = 5)
    {
        return $this->model
            ->with(['material:id,title', 'question:id,difficulty'])
            ->where('user_id', $userId)
            ->where('is_correct', true)
            ->whereRaw('created_at = (
                SELECT MAX(p2.created_at)
                FROM progress p2
                WHERE p2.material_id = progress.material_id
                AND p2.user_id = progress.user_id
                AND p2.is_correct = true
            )')
            ->select('progress.*')
            ->selectRaw('(
                SELECT COUNT(DISTINCT p3.question_id) 
                FROM progress p3 
                WHERE p3.material_id = progress.material_id 
                AND p3.user_id = ?
                AND p3.is_correct = 1
            ) as total_correct', [$userId])
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get()
            ->map(function ($progress) {
                return (object)[
                    'material_title' => $progress->material->title,
                    'material_id' => $progress->material->id,
                    'difficulty' => $progress->question->difficulty,
                    'created_at' => $progress->created_at,
                    'is_correct' => $progress->is_correct,
                    'total_correct' => $progress->total_correct
                ];
            });
    }
    
    public function getDetailedUserProgress($userId)
    {
        return $this->model
            ->join('questions', 'progress.question_id', '=', 'questions.id')
            ->select('progress.material_id', 'questions.difficulty')
            ->selectRaw('COUNT(DISTINCT progress.question_id) as total_answered')
            ->selectRaw('SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_answers')
            ->where('progress.user_id', $userId)
            ->groupBy('progress.material_id', 'questions.difficulty')
            ->get();
    }

    public function getCorrectAnswersWithAttempts($roleId = 3)
    {
        return $this->model
            ->join('questions', 'progress.question_id', '=', 'questions.id')
            ->join('users', 'progress.user_id', '=', 'users.id')
            ->select(
                'progress.user_id',
                'progress.question_id',
                'questions.difficulty'
            )
            ->selectRaw('MIN(progress.attempt_number) as attempts_needed')
            ->where('progress.is_correct', 1)
            ->where('users.role_id', $roleId)
            ->groupBy('progress.user_id', 'progress.question_id', 'questions.difficulty')
            ->get();
    }

    public function getLeaderboardStats($roleId = 3)
    {
        return $this->model
            ->join('users', 'progress.user_id', '=', 'users.id')
            ->leftJoin('questions', 'progress.question_id', '=', 'questions.id')
            ->select(
                'users.id',
                'users.name',
                'users.email'
            )
            ->selectRaw('COUNT(DISTINCT CASE WHEN progress.is_correct = 1 THEN progress.question_id END) as total_correct_questions')
            ->selectRaw('COUNT(DISTINCT progress.question_id) as total_attempted')
            ->selectRaw('SUM(CASE WHEN progress.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers')
            ->selectRaw('MAX(progress.updated_at) as completion_date')
            ->selectRaw('COUNT(DISTINCT CASE WHEN progress.is_correct = 1 AND questions.difficulty = "beginner" THEN progress.question_id END) as beginner_completed')
            ->selectRaw('COUNT(DISTINCT CASE WHEN progress.is_correct = 1 AND questions.difficulty = "medium" THEN progress.question_id END) as medium_completed')
            ->selectRaw('COUNT(DISTINCT CASE WHEN progress.is_correct = 1 AND questions.difficulty = "hard" THEN progress.question_id END) as hard_completed')
            ->selectRaw('COUNT(progress.id) as total_attempts')
            ->where('users.role_id', $roleId)
            ->groupBy('users.id', 'users.name', 'users.email')
            ->get();
    }

    public function getAttemptCount($userId, $materialId, $questionId)
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('material_id', $materialId)
            ->where('question_id', $questionId)
            ->count();
    }

    public function saveProgress(array $data)
    {
        return $this->model->create($data);
    }

    public function updateOrCreateProgress(array $conditions, array $values)
    {
        return $this->model->updateOrCreate($conditions, $values);
    }
}
