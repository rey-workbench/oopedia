<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\StudentServiceInterface;
use App\Enums\User\RoleName;
use App\Exceptions\Domain\UserNotFoundException;
use App\Helpers\ProgressHelper;
use App\Models\Role;
use App\Models\StudentState;
use App\Models\User;
use App\Services\User\Concerns\ImportsCsvUsers;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final readonly class StudentService implements StudentServiceInterface
{
    use ImportsCsvUsers;

    public function __construct(
        public UserRepositoryInterface $userRepo,
        public MaterialRepositoryInterface $materialRepo,
        public ProgressRepositoryInterface $progressRepo,
    ) {}

    public function getStudentsWithProgress(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        $lengthAwarePaginator       = $this->userRepo->getStudentsList($search, $perPage);
        $allOrdered                 = $this->materialRepo->getAllOrdered();
        $totalQuestions             = ProgressHelper::calculateTotalQuestions($allOrdered);

        foreach ($lengthAwarePaginator as $student) {
            $progressStats  = $this->progressRepo->getUserProgressStats($student->id);
            $correctAnswers = $progressStats->sum('correct_answers');

            $student->overall_progress = ProgressHelper::calculateProgressPercentage($correctAnswers, $totalQuestions);

            $student->total_answered_questions = $progressStats->sum('answered_questions');
        }

        return $lengthAwarePaginator;
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
        $data['password']    = Hash::make($data['password']);
        $data['role_id']     = Role::where('role_name', RoleName::MAHASISWA)->value('id');
        $data['is_approved'] = true;

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

        if (! $student instanceof User) {
            throw new UserNotFoundException($studentId);
        }

        DB::transaction(function () use ($studentId): void {
            $this->userRepo->deleteStudentData($studentId);
        });
    }

    public function approveStudent(string $studentId): void
    {
        $this->userRepo->approveStudent($studentId);
    }

    public function getStudentProgressDetail(User $user): array
    {
        $allOrdered             = $this->materialRepo->getAllOrdered();
        $progressStats          = $this->progressRepo->getUserMaterialProgress($user->id);
        $materialsWithProgress  = collect();

        foreach ($allOrdered as $material) {
            $totalQuestions = $material->questions->count();

            $materialProgress   = $progressStats->firstWhere('material_id', $material->id);
            $correctAnswers     = $materialProgress ? $materialProgress->correct_answers : 0;
            $progressPercentage = ProgressHelper::calculateProgressPercentage($correctAnswers, $totalQuestions);
            $lastAccessed       = $this->progressRepo->getLastAccessTime($user->id, $material->id);

            $materialsWithProgress->push((object) [
                'id'                 => $material->id,
                'title'              => $material->title,
                'total_questions'    => $totalQuestions,
                'answered_questions' => $correctAnswers,
                'progress'           => $progressPercentage,
                'last_accessed'      => $lastAccessed ? Date::parse($lastAccessed) : null,
            ]);
        }

        $missingQuestionsByMaterial = [];

        foreach ($materialsWithProgress as $materialWithProgress) {
            $missingCount = max(0, $materialWithProgress->total_questions - $materialWithProgress->answered_questions);

            if ($missingCount > 0) {
                $missingQuestionsByMaterial[] = [
                    'material_title' => $materialWithProgress->title,
                    'missing_count'  => $missingCount,
                ];
            }
        }

        $recentActivities = $this->progressRepo->getRecentActivities($user->id, 10);

        $studentState   = StudentState::where('user_id', $user->id)->first();
        $certifications = $studentState ? ($studentState->certifications ?? []) : [];

        return [
            'materials'                  => $materialsWithProgress,
            'recent_activities'          => $recentActivities,
            'missingQuestionsByMaterial' => $missingQuestionsByMaterial,
            'certifications'             => $certifications,
        ];
    }

    public function importStudentsFromFile(UploadedFile $uploadedFile): array
    {
        return $this->importUsersFromCsv($uploadedFile, fn (array $rowData): User => $this->createStudent($rowData));
    }

    public function generateImportTemplate(): array
    {
        return $this->generateCsvTemplate(
            'mahasiswa_template.csv',
            ['Nama Mahasiswa', 'mahasiswa@example.com', 'password123'],
        );
    }
}
