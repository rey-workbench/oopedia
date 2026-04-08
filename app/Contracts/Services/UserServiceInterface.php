<?php

namespace App\Contracts\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;

interface UserServiceInterface
{
    public function getUserById(string $id): ?User;

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

    public function importAdminsFromFile(UploadedFile $file): array;

    public function generateImportTemplate(): array;
}
