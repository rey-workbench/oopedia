<?php

namespace App\Contracts\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;

/**
 * Contract for student user management service.
 */
interface StudentServiceInterface
{
    /**
     * Get paginated list of students.
     */
    public function getStudentsList(?string $search = null, int $perPage = 10): LengthAwarePaginator;

    /**
     * Get a student by their ID.
     */
    public function getStudentById(string $id): ?User;

    /**
     * Create a new student user.
     */
    public function createStudent(array $data): User;

    /**
     * Update an existing student.
     */
    public function updateStudent(string $studentId, array $data): User;

    /**
     * Delete a student user.
     */
    public function deleteStudent(string $studentId): void;

    /**
     * Get paginated list of pending student approvals.
     */
    public function getPendingStudents(?int $perPage = null): LengthAwarePaginator;

    /**
     * Approve a student account.
     */
    public function approveStudent(string $studentId): void;

    /**
     * Reject and delete a pending student account.
     */
    public function rejectStudent(string $studentId): void;

    /**
     * Get paginated list of students with their progress data.
     */
    public function getStudentsWithProgress(?string $search = null, int $perPage = 10): LengthAwarePaginator;

    /**
     * Get detailed progress information for a student.
     *
     * @return array<string, mixed>
     */
    public function getStudentProgressDetail(User $student): array;

    /**
     * Import students from an uploaded file.
     *
     * @return array<string, mixed>
     */
    public function importStudentsFromFile(UploadedFile $file): array;

    /**
     * Generate a template for importing students.
     *
     * @return array<string, mixed>
     */
    public function generateImportTemplate(): array;
}
