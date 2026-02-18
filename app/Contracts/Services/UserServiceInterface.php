<?php

namespace App\Contracts\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;

/**
 * Contract for user (admin) management service.
 */
interface UserServiceInterface
{
    /**
     * Get a user by their ID.
     */
    public function getUserById(int $id): ?User;

    /**
     * Get paginated list of admin users.
     */
    public function getAdmins(?string $search = null, int $perPage = 10): LengthAwarePaginator;

    /**
     * Create a new admin user.
     */
    public function createAdmin(array $data): User;

    /**
     * Update an existing admin user.
     */
    public function updateAdmin(int $userId, array $data): User;

    /**
     * Update a user's profile information.
     */
    public function updateProfile(int $userId, array $data): User;

    /**
     * Delete an admin user.
     */
    public function deleteAdmin(int $userId): void;

    /**
     * Get paginated list of pending admin approvals.
     */
    public function getPendingAdmins(?int $perPage = null): LengthAwarePaginator;

    /**
     * Get the count of pending admin approvals.
     */
    public function getPendingAdminsCount(): int;

    /**
     * Approve an admin user account.
     */
    public function approveAdmin(int $userId): void;

    /**
     * Reject and delete a pending admin account.
     */
    public function rejectAdmin(int $userId): void;

    /**
     * Register a new user.
     */
    public function registerUser(array $data): User;

    /**
     * Create a new student user.
     */
    public function createStudent(array $data): User;

    /**
     * Import admin users from an uploaded file.
     *
     * @return array<string, mixed>
     */
    public function importAdminsFromFile(UploadedFile $file): array;

    /**
     * Generate and return an import template file.
     */
    public function generateImportTemplate(): array;
}
