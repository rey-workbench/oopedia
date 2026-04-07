<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\QuizAttempt;
use App\Models\StudentState;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class UserRepository implements UserRepositoryInterface
{
    /** @return Collection<int, User> */
    public function all(): Collection
    {
        return User::all();
    }

    public function find(string $id): ?User
    {
        return User::find($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(string $id, array $data): ?User
    {
        $user = User::find($id);

        if ($user) {
            $user->update($data);

            return $user;
        }

        return null;
    }

    public function delete(string $id): bool
    {
        $user = User::find($id);

        if ($user) {
            return (bool) $user->delete();
        }

        return false;
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return User::paginate($perPage, ['*'], 'page', null);
    }

    public function countAll(): int
    {
        return User::count();
    }

    public function getStudentsList(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = User::whereHas('role', function ($q) {
            $q->where('role_name', 'mahasiswa');
        });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage, ['*'], 'page', null);
    }

    public function getStudentsWithRole(
        string $roleName,
        ?string $search = null,
        int $perPage = 10,
    ): LengthAwarePaginator {
        $query = User::whereHas('role', function ($q) use ($roleName) {
            $q->where('role_name', $roleName);
        });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage, ['*'], 'page', null);
    }

    public function deleteStudentData(string $userId): void
    {
        StudentState::where('user_id', $userId)->delete();
        QuizAttempt::where('user_id', $userId)->delete();
        User::find($userId)?->delete();
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /** @return Collection<int, User> */
    public function getUnapprovedStudents(): Collection
    {
        return User::whereHas('role', function ($q) {
            $q->where('role_name', 'mahasiswa');
        })
            ->where('is_approved', false)
            ->get();
    }

    public function approveStudent(string $userId): void
    {
        $this->update($userId, ['is_approved' => true]);
    }

    // ==================== ADDITIONAL METHODS ====================

    public function getUsersByRoleAndApproval(
        string $roleName,
        bool $isApproved,
        ?string $search = null,
        ?int $perPage = 10,
        string $sortBy = 'created_at',
        string $sortOrder = 'desc',
    ): LengthAwarePaginator|Collection {
        $query = User::whereHas('role', function ($q) use ($roleName) {
            $q->where('role_name', $roleName);
        })
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

        return $query->paginate($perPage, ['*'], 'page', null);
    }

    public function getActiveStudentsCount(int $days): int
    {
        return User::whereHas('role', function ($q) {
            $q->where('role_name', 'mahasiswa');
        })
            ->whereHas('quizAttempts', function ($query) use ($days) {
                $query->where('created_at', '>=', now()->subDays($days));
            })
            ->count();
    }

    public function getStudentProgressOverview(int $limit): Collection
    {
        return User::whereHas('role', function ($q) {
            $q->where('role_name', 'mahasiswa');
        })
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

    public function countByRole(string $roleName): int
    {
        return User::whereHas('role', function ($q) use ($roleName) {
            $q->where('role_name', $roleName);
        })
            ->count();
    }
}
