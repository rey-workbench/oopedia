<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UserRepository implements UserRepositoryInterface
{
    public function all()
    {
        return User::all();
    }

    public function find($id)
    {
        return User::find($id);
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function update($id, array $data)
    {
        $user = User::find($id);
        if ($user) {
            $user->update($data);
            return $user;
        }
        return null;
    }

    public function delete($id)
    {
        $user = User::find($id);
        if ($user) {
            return $user->delete();
        }
        return false;
    }

    public function paginate($perPage = 15)
    {
        return User::paginate($perPage);
    }

    public function countAll()
    {
        return User::count('*');
    }

    public function getStudentsList($search = null, $perPage = 10)
    {
        $query = User::where('role_id', 3);
        
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
        $query = User::where('role_id', $roleId);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        return $query->paginate($perPage);
    }

    public function deleteStudentData($userId)
    {
        // Pure data deletion, no transaction (handled in service)
        // Delete related data
        if (Schema::hasTable('user_ranks')) {
            DB::table('user_ranks')->where('user_id', $userId)->delete();
        }
        
        if (Schema::hasTable('student_states')) {
            DB::table('student_states')->where('user_id', $userId)->delete();
        }
        
        if (Schema::hasTable('student_answers')) {
            DB::table('student_answers')->where('student_id', $userId)->delete();
        }
        
        if (Schema::hasTable('quiz_attempts')) {
            DB::table('quiz_attempts')->where('user_id', $userId)->delete();
        }
        
        // Delete the user
        $user = User::find($userId);
        if ($user) {
            return $user->delete();
        }
        
        return false;
    }

    public function deleteStudentWithRelations($userId)
    {
        // Deprecated: Use deleteStudentData() instead, transaction should be in service
        // Keeping for backward compatibility
        DB::beginTransaction();
        
        try {
            $result = $this->deleteStudentData($userId);
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function findByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function getUnapprovedStudents()
    {
        return User::where('role_id', 3)
            ->where('is_approved', false)
            ->get();
    }

    public function approveStudent($userId)
    {
        return $this->update($userId, ['is_approved' => true]);
    }

    public function createStudent(array $data)
    {
        $data['role_id'] = 3; // Student role
        $data['is_approved'] = true;
        
        return User::create($data);
    }

    public function getUsersByRoleAndApproval($roleId, $isApproved, $search = null, $perPage = 10, $sortBy = 'created_at', $sortOrder = 'desc')
    {
        $query = User::where('role_id', $roleId)
                     ->where('is_approved', $isApproved)
                     ->orderBy($sortBy, $sortOrder);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($perPage) {
            return $query->paginate($perPage);
        }
        
        return $query->get();
    }

    public function approveUser($userId)
    {
        return $this->update($userId, ['is_approved' => true]);
    }

    public function getActiveStudentsCount($days)
    {
        return DB::table('users')
            ->join('quiz_attempts', 'users.id', '=', 'quiz_attempts.user_id')
            ->where('users.role_id', 3) // Student
            ->where('quiz_attempts.created_at', '>=', now()->subDays($days))
            ->distinct('users.id')
            ->count('users.id');
    }

    public function getStudentProgressOverview($limit)
    {
        return User::where('role_id', 3)
            ->withCount(['quizAttempts as completed_questions' => function($query) {
                $query->where('is_correct', true);
            }])
            ->with(['quizAttempts' => function($query) {
                $query->where('is_correct', true)
                      ->with('question.material:id,title'); // Nested relation via question
            }])
            ->having('completed_questions', '>', 0)
            ->orderByDesc('completed_questions')
            ->limit($limit)
            ->get();
    }

    public function countByRole($roleId)
    {
        return User::where('role_id', $roleId)->count('*');
    }
}
