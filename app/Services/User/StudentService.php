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
use App\Http\Resources\MaterialResource;
use App\Http\Resources\UserResource;
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
        $lengthAwarePaginator = $this->userRepo->getStudentsList($search, $perPage);
        $allOrdered           = $this->materialRepo->getAllOrdered();
        $totalQuestions       = ProgressHelper::calculateTotalQuestions($allOrdered);

        return $lengthAwarePaginator->through(function ($student) use ($totalQuestions) {
            $progressStats  = $this->progressRepo->getUserProgressStats($student->id);
            $correctAnswers = $progressStats->sum('correct_answers');

            $student->overall_progress         = ProgressHelper::calculateProgressPercentage($correctAnswers, $totalQuestions);
            $student->total_answered_questions = $progressStats->sum('answered_questions');

            return new UserResource($student)->resolve();
        });
    }

    public function getStudentsList(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        return $this->userRepo->getStudentsList($search, $perPage);
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

    public function getStudentProgressDetail(string $userId): ?array
    {
        $user = $this->userRepo->find($userId);
        if (! $user instanceof User) {
            return null;
        }

        $allOrdered             = $this->materialRepo->getAllOrdered();
        $progressStats          = $this->progressRepo->getUserMaterialProgress($user->id);
        $materialsWithProgress  = collect();

        foreach ($allOrdered as $material) {
            $totalQuestions = $material->questions->count();

            $materialProgress   = $progressStats->firstWhere('material_id', $material->id);
            $correctAnswers     = $materialProgress ? $materialProgress->correct_answers : 0;
            $progressPercentage = ProgressHelper::calculateProgressPercentage($correctAnswers, $totalQuestions);
            $lastAccessed       = $this->progressRepo->getLastAccessTime($user->id, $material->id);

            $material->progress_percentage = $progressPercentage;
            $material->total_questions     = $totalQuestions;
            $material->completed_questions = $correctAnswers;
            $material->status              = $progressPercentage === 100 ? 'completed' : ($progressPercentage > 0 ? 'in_progress' : 'not_started');
            $material->last_accessed       = $lastAccessed ? Date::parse($lastAccessed)->toIso8601String() : null;

            $materialsWithProgress->push(new MaterialResource($material)->resolve());
        }

        $missingQuestionsByMaterial = [];

        foreach ($materialsWithProgress as $materialWithProgress) {
            $missingCount = max(0, $materialWithProgress['total_questions'] - $materialWithProgress['completed_questions']);

            if ($missingCount > 0) {
                $missingQuestionsByMaterial[] = [
                    'material_title' => $materialWithProgress['title'],
                    'missing_count'  => $missingCount,
                ];
            }
        }

        $recentActivities = $this->progressRepo->getRecentActivities($user->id, 10);

        $studentState   = StudentState::where('user_id', $user->id)->first();
        $certifications = $studentState ? ($studentState->certifications ?? []) : [];

        return [
            'student'                    => new UserResource($user)->resolve(),
            'materials'                  => $materialsWithProgress,
            'recent_activities'          => $recentActivities, // Might need RecentProgressResource
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
