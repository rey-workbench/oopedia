<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;

interface UserServiceInterface
{
    public function getUserById(string $id): ?array;

    public function getAdmins(?string $search = null, int $perPage = 10): LengthAwarePaginator;

    public function createAdmin(array $data): User;

    public function updateAdmin(string $userId, array $data): User;

    public function updateProfile(string $userId, array $data): User;

    public function deleteAdmin(string $userId): void;

    public function getPendingAdmins(?int $perPage = null): LengthAwarePaginator;

    public function getPendingAdminsCount(): int;

    public function approveAdmin(string $userId): void;

    public function rejectAdmin(string $userId): void;

    public function registerUser(array $data): User;

    public function importAdminsFromFile(UploadedFile $uploadedFile): array;

    public function generateImportTemplate(): array;

    /**
     * @param array{google_id: string, name: string, email: string, avatar: ?string} $googleData
     */
    public function findOrCreateSocialUser(array $googleData): ?User;

    /**
     * @param array{google_id: string, name: string, email: string, avatar: ?string} $googleData
     */
    public function registerSocialUser(array $googleData, string $role): User;
}
