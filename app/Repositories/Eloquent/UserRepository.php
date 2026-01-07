<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function getStudentsList($search = null, $perPage = 10)
    {
        $query = $this->model->where('role_id', 3);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        return $query->paginate($perPage);
    }

    public function getStudentsWithRole($roleId, $search = null, $perPage = 10)
    {
        $query = $this->model->where('role_id', $roleId);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        return $query->paginate($perPage);
    }

    public function deleteStudentWithRelations($userId)
    {
        DB::beginTransaction();
        
        try {
            // Delete related data using Eloquent relationships where possible
            if (Schema::hasTable('user_ranks')) {
                DB::table('user_ranks')->where('user_id', $userId)->delete();
            }
            
            if (Schema::hasTable('progress')) {
                DB::table('progress')->where('user_id', $userId)->delete();
            }
            
            if (Schema::hasTable('student_answers')) {
                DB::table('student_answers')->where('student_id', $userId)->delete();
            }
            
            if (Schema::hasTable('quiz_attempts')) {
                DB::table('quiz_attempts')->where('user_id', $userId)->delete();
            }
            
            // Delete the user
            $user = $this->find($userId);
            $user->delete();
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function createStudent(array $data)
    {
        $data['role_id'] = 3; // Student role
        $data['is_approved'] = true;
        
        return $this->create($data);
    }

    public function getUsersByRoleAndApproval($roleId, $isApproved, $search = null, $perPage = 10)
    {
        $query = $this->model->where('role_id', $roleId)
                             ->where('is_approved', $isApproved);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        return $query->paginate($perPage);
    }

    public function approveUser($userId)
    {
        return $this->update($userId, ['is_approved' => true]);
    }
    public function getActiveStudentsCount($days)
    {
        return DB::table('users')
            ->join('progress', 'users.id', '=', 'progress.user_id')
            ->where('users.role_id', 3) // Student
            ->where('progress.updated_at', '>=', now()->subDays($days))
            ->distinct('users.id')
            ->count('users.id');
    }

    public function getStudentProgressOverview($limit)
    {
        return $this->model->where('role_id', 3)
            ->withCount(['progress as completed_questions' => function($query) {
                $query->where('is_correct', true);
            }])
            ->with(['progress' => function($query) {
                $query->where('is_correct', true)
                      ->with('material:id,title');
            }])
            ->having('completed_questions', '>', 0)
            ->orderByDesc('completed_questions')
            ->limit($limit)
            ->get();
    }
}
