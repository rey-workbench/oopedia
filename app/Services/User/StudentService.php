<?php

namespace App\Services\User;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\StudentServiceInterface;
use App\Exceptions\Domain\UserNotFoundException;
use App\Helpers\ProgressHelper;
use App\Models\Role;
use App\Models\StudentState;
use App\Models\User;
use App\Services\User\Concerns\ImportsCsvUsers;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentService implements StudentServiceInterface
{
    use ImportsCsvUsers;

    public function __construct(
        protected UserRepositoryInterface $userRepo,
        protected MaterialRepositoryInterface $materialRepo,
        protected ProgressRepositoryInterface $progressRepo,
    ) {
    }

    public function getStudentsWithProgress(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        $students = $this->userRepo->getStudentsList($search, $perPage);

        // Get all materials
        $materials = $this->materialRepo->getAllOrdered();

        // Calculate total questions
        $totalQuestions = ProgressHelper::calculateTotalQuestions($materials);

        // Add progress data to each student
        foreach ($students as $student) {
            $progressStats  = $this->progressRepo->getUserProgressStats($student->id);
            $correctAnswers = $progressStats->sum('correct_answers');

            $student->overall_progress = ProgressHelper::calculateProgressPercentage($correctAnswers, $totalQuestions);

            $student->total_answered_questions = $progressStats->sum('answered_questions');
        }

        return $students;
    }

    public function getStudentsList(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        return $this->userRepo->getStudentsList($search, $perPage);
    }

    public function getStudentById(string $id): ?User
    {
        return $this->userRepo->find($id);
    }

    public function createStudent(array $data): User
    {
        // Hash password
        $data['password']    = Hash::make($data['password']);
        $data['role_id']     = Role::where('role_name', 'mahasiswa')->value('id'); // Student role
        $data['is_approved'] = true; // Admin-created students are auto-approved

        return $this->userRepo->create($data);
    }

    public function updateStudent(string $studentId, array $data): User
    {
        if (isset($data['password']) && ! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $this->userRepo->update($studentId, $data);
    }

    public function deleteStudent(string $studentId): void
    {
        $student = $this->userRepo->find($studentId);

        if (! $student) {
            throw new UserNotFoundException($studentId);
        }

        DB::transaction(function () use ($studentId) {
            $this->userRepo->deleteStudentData($studentId);
        });
    }

    public function getPendingStudents(?int $perPage = null): LengthAwarePaginator
    {
        return $this->userRepo->getUsersByRoleAndApproval('mahasiswa', false, null, $perPage ?? 10);
    }

    public function approveStudent(string $studentId): void
    {
        $this->userRepo->approveStudent($studentId);
    }

    public function rejectStudent(string $studentId): void
    {
        $this->userRepo->delete($studentId);
    }

    /** @return array<string, mixed> */
    public function getStudentProgressDetail(User $student): array
    {
        // Get all materials
        $materials = $this->materialRepo->getAllOrdered();

        // Get progress data for this student
        $progressStats = $this->progressRepo->getUserMaterialProgress($student->id);

        // Build materials with progress
        $materialsWithProgress = collect();

        foreach ($materials as $material) {
            $totalQuestions = $material->questions->count();

            // Get correct answers for this material
            $materialProgress = $progressStats->firstWhere('material_id', $material->id);
            $correctAnswers   = $materialProgress ? $materialProgress->correct_answers : 0;

            // Calculate progress percentage
            $progressPercentage = ProgressHelper::calculateProgressPercentage($correctAnswers, $totalQuestions);

            // Get last access time
            $lastAccessed = $this->progressRepo->getLastAccessTime($student->id, $material->id);

            $materialsWithProgress->push((object) [
                'id'                 => $material->id,
                'title'              => $material->title,
                'total_questions'    => $totalQuestions,
                'answered_questions' => $correctAnswers,
                'progress'           => $progressPercentage,
                'last_accessed'      => $lastAccessed ? Carbon::parse($lastAccessed) : null,
            ]);
        }

        $missingQuestionsByMaterial = [];

        foreach ($materialsWithProgress as $item) {
            $missingCount = max(0, $item->total_questions - $item->answered_questions);

            if ($missingCount > 0) {
                $missingQuestionsByMaterial[] = [
                    'material_title' => $item->title,
                    'missing_count'  => $missingCount,
                ];
            }
        }

        // Get recent activities
        $recentActivities = $this->progressRepo->getRecentActivities($student->id, 10);

        $studentState   = StudentState::find($student->id);
        $certifications = $studentState ? ($studentState->learning_profile['certifications'] ?? []) : [];

        return [
            'materials'                  => $materialsWithProgress,
            'recent_activities'          => $recentActivities,
            'missingQuestionsByMaterial' => $missingQuestionsByMaterial,
            'certifications'             => $certifications,
        ];
    }

    /** @return array<string, mixed> */
    public function importStudentsFromFile(UploadedFile $file): array
    {
        return $this->importUsersFromCsv($file, fn (array $rowData) => $this->createStudent($rowData));
    }

    /** @return array<string, mixed> */
    public function generateImportTemplate(): array
    {
        return $this->generateCsvTemplate(
            'mahasiswa_template.csv',
            ['Nama Mahasiswa', 'mahasiswa@example.com', 'password123'],
        );
    }
}
