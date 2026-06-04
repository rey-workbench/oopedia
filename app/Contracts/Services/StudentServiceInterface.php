<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;

interface StudentServiceInterface
{
    public function getStudentsList(?string $search = null, int $perPage = 10): LengthAwarePaginator;

    public function createStudent(array $data): User;

    public function updateStudent(string $studentId, array $data): User;

    public function deleteStudent(string $studentId): void;

    public function approveStudent(string $studentId): void;

    public function getStudentsWithProgress(?string $search = null, int $perPage = 10): LengthAwarePaginator;

    public function getStudentProgressDetail(string $userId): ?array;

    public function importStudentsFromFile(UploadedFile $uploadedFile): array;

    public function generateImportTemplate(): array;
}
