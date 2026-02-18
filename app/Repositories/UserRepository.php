<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UserRepository implements UserRepositoryInterface
{
    /** @return Collection<int, User> */
    public function all(): Collection
    {
        return User::all();
    }

    public function find(int $id): ?User
    {
        return User::find($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(int $id, array $data): ?User
    {
        $user = User::find($id);

        if ($user) {
            $user->update($data);

            return $user;
        }

        return null;
    }

    public function delete(int $id): bool
    {
        $user = User::find($id);

        if ($user) {
            return (bool) $user->delete();
        }

        return false;
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return User::paginate($perPage);
    }

    public function countAll(): int
    {
        return User::count('*');
    }

    public function getStudentsList(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = User::where('role_id', 3);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    public function getStudentsWithRole(int $roleId, ?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = User::where('role_id', $roleId);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    public function deleteStudentData(int $userId): void
    {
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

        $user = User::find($userId);
        $user?->delete();
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /** @return Collection<int, User> */
    public function getUnapprovedStudents(): Collection
    {
        return User::where('role_id', 3)
            ->where('is_approved', false)
            ->get();
    }

    public function approveStudent(int $userId): void
    {
        $this->update($userId, ['is_approved' => true]);
    }

    // ==================== ADDITIONAL METHODS ====================

    public function getUsersByRoleAndApproval(
        int $roleId,
        bool $isApproved,
        ?string $search = null,
        ?int $perPage = 10,
        string $sortBy = 'created_at',
        string $sortOrder = 'desc',
    ): LengthAwarePaginator|Collection {
        $query = User::where('role_id', $roleId)
            ->where('is_approved', $isApproved)
            ->orderBy($sortBy, $sortOrder);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($perPage === null) {
            return $query->get();
        }

        return $query->paginate($perPage);
    }

    public function getActiveStudentsCount(int $days): int
    {
        return DB::table('users')
            ->join('quiz_attempts', 'users.id', '=', 'quiz_attempts.user_id')
            ->where('users.role_id', 3)
            ->where('quiz_attempts.created_at', '>=', now()->subDays($days))
            ->distinct('users.id')
            ->count('users.id');
    }

    public function getStudentProgressOverview(int $limit): Collection
    {
        return User::where('role_id', 3)
            ->withCount(['quizAttempts as completed_questions' => function ($query) {
                $query->where('is_correct', true);
            }])
            ->with(['quizAttempts' => function ($query) {
                $query->where('is_correct', true)
                    ->with('question.material:id,title');
            }])
            ->having('completed_questions', '>', 0)
            ->orderByDesc('completed_questions')
            ->limit($limit)
            ->get();
    }

    public function countByRole(int $roleId): int
    {
        return User::where('role_id', $roleId)->count('*');
    }
}
