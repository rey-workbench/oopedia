<?php

namespace App\Contracts\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Contract for User data access.
 */
interface UserRepositoryInterface
{
    /** @return Collection<int, User> */
    public function all(): Collection;

    public function find(int $id): ?User;

    public function create(array $data): User;

    public function update(int $id, array $data): ?User;

    public function delete(int $id): bool;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function countAll(): int;

    public function getStudentsList(?string $search = null, int $perPage = 10): LengthAwarePaginator;

    public function getStudentsWithRole(int $roleId, ?string $search = null, int $perPage = 10): LengthAwarePaginator;

    public function deleteStudentData(int $userId): void;

    public function findByEmail(string $email): ?User;

    /** @return Collection<int, User> */
    public function getUnapprovedStudents(): Collection;

    public function approveStudent(int $userId): void;

    /**
     * Get users by role and approval status, paginated or as a full collection.
     * Passing null for $perPage returns a Collection of all matching users.
     *
     * @return LengthAwarePaginator|Collection<int, User>
     */
    public function getUsersByRoleAndApproval(
        int $roleId,
        bool $isApproved,
        ?string $search = null,
        ?int $perPage = 10,
    ): LengthAwarePaginator|Collection;

    public function countByRole(int $roleId): int;

    public function getActiveStudentsCount(int $days): int;

    /** @return Collection<int, User> */
    public function getStudentProgressOverview(int $limit): Collection;
}
