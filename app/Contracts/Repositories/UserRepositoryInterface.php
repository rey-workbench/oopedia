<?php

namespace App\Contracts\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function all(): Collection;

    public function find(string $id): ?User;

    public function create(array $data): User;

    public function update(string $id, array $data): ?User;

    public function delete(string $id): bool;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function countAll(): int;

    public function getStudentsList(?string $search = null, int $perPage = 10): LengthAwarePaginator;

    public function getStudentsWithRole(
        string $roleName,
        ?string $search = null,
        int $perPage = 10,
    ): LengthAwarePaginator;

    public function deleteStudentData(string $userId): void;

    public function findByEmail(string $email): ?User;

    public function getUnapprovedStudents(): Collection;

    public function approveStudent(string $userId): void;

    /**
     * Get users by role and approval status, paginated or as a full collection.
     * Passing null for $perPage returns a Collection of all matching users.
     *
     * @return LengthAwarePaginator|Collection<int, User>
     */
    public function getUsersByRoleAndApproval(
        string $roleName,
        bool $isApproved,
        ?string $search = null,
        ?int $perPage = 10,
    ): LengthAwarePaginator|Collection;

    public function countByRole(string $roleName): int;

    public function getActiveStudentsCount(int $days): int;

    public function getStudentProgressOverview(int $limit): Collection;
}
