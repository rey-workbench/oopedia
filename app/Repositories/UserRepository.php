<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\QuizAttempt;
use App\Models\StudentState;
use App\Models\User;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class UserRepository implements UserRepositoryInterface
{
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

        if (! $user) {
            return null;
        }

        $user->update($data);

        return $user;
    }

    public function delete(string $id): bool
    {
        $user = User::find($id);

        if (! $user) {
            return false;
        }

        return (bool) $user->delete();
    }

    public function getStudentsList(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = User::whereHas('role', function (Builder $q): void {
            $q->where('role_name', 'mahasiswa');
        });

        if ($search) {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', sprintf('%%%s%%', $search))
                    ->orWhere('email', 'like', sprintf('%%%s%%', $search));
            });
        }

        return $query->paginate($perPage, ['*'], 'page', null);
    }

    public function getStudentsWithRole(
        string $roleName,
        ?string $search = null,
        int $perPage = 10,
    ): LengthAwarePaginator {
        $query = User::whereHas('role', function (Builder $q) use ($roleName): void {
            $q->where('role_name', $roleName);
        });

        if ($search) {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', sprintf('%%%s%%', $search))
                    ->orWhere('email', 'like', sprintf('%%%s%%', $search));
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

    public function approveStudent(string $userId): void
    {
        $this->update($userId, ['is_approved' => true]);
    }

    public function getUsersByRoleAndApproval(
        string $roleName,
        bool $isApproved,
        ?string $search = null,
        ?int $perPage = 10,
        string $sortBy = 'created_at',
        string $sortOrder = 'desc',
    ): LengthAwarePaginator|Collection {
        $query = User::whereHas('role', function (Builder $q) use ($roleName): void {
            $q->where('role_name', $roleName);
        })
            ->where('is_approved', $isApproved)
            ->orderBy($sortBy, $sortOrder);

        if ($search) {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', sprintf('%%%s%%', $search))
                    ->orWhere('email', 'like', sprintf('%%%s%%', $search));
            });
        }

        if ($perPage === null) {
            return $query->get();
        }

        return $query->paginate($perPage, ['*'], 'page', null);
    }

    public function getActiveStudentsCount(int $days): int
    {
        return User::whereHas('role', function (Builder $q): void {
            $q->where('role_name', 'mahasiswa');
        })
            ->whereHas('quizAttempts', function ($query) use ($days): void {
                $query->where('created_at', '>=', now()->subDays($days));
            })
            ->count();
    }

    public function getStudentProgressOverview(int $limit): Collection
    {
        return User::whereHas('role', function (Builder $q): void {
            $q->where('role_name', 'mahasiswa');
        })
            ->withCount(['quizAttempts as completed_questions' => function ($query): void {
                $query->where('is_correct', true);
            }])
            ->with(['quizAttempts' => function ($query): void {
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
        return User::whereHas('role', function (Builder $q) use ($roleName): void {
            $q->where('role_name', $roleName);
        })
            ->count();
    }
}
