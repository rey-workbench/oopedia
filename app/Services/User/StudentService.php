<?php

namespace App\Services\User;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\StudentServiceInterface;
use App\Exceptions\Domain\UserNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StudentService implements StudentServiceInterface
{
    public function __construct(
        protected UserRepositoryInterface $userRepo,
        protected MaterialRepositoryInterface $materialRepo,
        protected ProgressRepositoryInterface $progressRepo,
    ) {}

    public function getStudentsWithProgress(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        $students = $this->userRepo->getStudentsList($search, $perPage);

        // Get all materials
        $materials = $this->materialRepo->getAllOrdered();

        // Calculate total questions
        $totalQuestions = $materials->sum(fn ($m) => $m->questions->count());

        // Add progress data to each student
        foreach ($students as $student) {
            $progressStats  = $this->progressRepo->getUserProgressStats($student->id);
            $correctAnswers = $progressStats->sum('correct_answers');

            $student->overall_progress = $totalQuestions > 0
                ? min(100, round(($correctAnswers / $totalQuestions) * 100))
                : 0;

            $student->total_answered_questions = $progressStats->sum('answered_questions');
        }

        return $students;
    }

    public function getStudentsList(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        return $this->userRepo->getStudentsList($search, $perPage);
    }

    public function getStudentById(int $id): ?\App\Models\User
    {
        return $this->userRepo->find($id);
    }

    public function createStudent(array $data): \App\Models\User
    {
        // Hash password
        $data['password']    = Hash::make($data['password']);
        $data['role_id']     = 3; // Student role
        $data['is_approved'] = true; // Admin-created students are auto-approved

        return $this->userRepo->create($data);
    }

    public function updateStudent(int $studentId, array $data): \App\Models\User
    {
        if (isset($data['password']) && ! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $this->userRepo->update($studentId, $data);
    }

    public function deleteStudent(int $studentId): void
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
        return $this->userRepo->getUsersByRoleAndApproval(3, false, null, $perPage ?? 10);
    }

    public function approveStudent(int $studentId): void
    {
        $this->userRepo->approveStudent($studentId);
    }

    public function rejectStudent(int $studentId): void
    {
        $this->userRepo->delete($studentId);
    }

    /** @return array<string, mixed> */
    public function getStudentProgressDetail(\App\Models\User $student): array
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
            $progressPercentage = $totalQuestions > 0
                ? min(100, round(($correctAnswers / $totalQuestions) * 100))
                : 0;

            // Get last access time
            $lastAccessed = $this->progressRepo->getLastAccessTime($student->id, $material->id);

            $materialsWithProgress->push((object) [
                'id'                 => $material->id,
                'title'              => $material->title,
                'total_questions'    => $totalQuestions,
                'answered_questions' => $correctAnswers,
                'progress'           => $progressPercentage,
                'last_accessed'      => $lastAccessed ? \Carbon\Carbon::parse($lastAccessed) : null,
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

        return [
            'materials'                  => $materialsWithProgress,
            'recent_activities'          => $recentActivities,
            'missingQuestionsByMaterial' => $missingQuestionsByMaterial,
        ];
    }

    /** @return array<string, mixed> */
    public function importStudentsFromFile(UploadedFile $file): array
    {
        $path         = $file->getRealPath();
        $successCount = 0;
        $errorRows    = [];

        if (($handle = fopen($path, 'r')) !== false) {
            // Read header
            $header = fgetcsv($handle, 1000, ',');

            // Validate required columns
            $requiredColumns = ['name', 'email', 'password'];
            $missingColumns  = array_diff($requiredColumns, $header);

            if (! empty($missingColumns)) {
                throw new \Exception('File tidak memiliki kolom yang diperlukan: ' . implode(', ', $missingColumns));
            }

            // Map column indexes
            $nameIndex     = array_search('name', $header);
            $emailIndex    = array_search('email', $header);
            $passwordIndex = array_search('password', $header);

            // Process each row
            $rowNumber = 1;

            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $rowNumber++;

                // Skip empty rows
                if (empty($row[$nameIndex]) && empty($row[$emailIndex])) {
                    continue;
                }

                // Validate row data
                $rowData = [
                    'name'     => $row[$nameIndex]     ?? '',
                    'email'    => $row[$emailIndex]    ?? '',
                    'password' => $row[$passwordIndex] ?? '',
                ];

                $validator = Validator::make($rowData, [
                    'name'  => 'required|string|max:255',
                    'email' => [
                        'required',
                        'string',
                        'email',
                        'max:255',
                        Rule::unique('users'),
                    ],
                    'password' => 'required|string|min:8',
                ]);

                if ($validator->fails()) {
                    $errorRows[] = [
                        'row'    => $rowNumber,
                        'errors' => $validator->errors()->all(),
                    ];
                    continue;
                }

                // Create the student
                try {
                    $this->userRepo->create([
                        'name'        => $row[$nameIndex],
                        'email'       => $row[$emailIndex],
                        'password'    => Hash::make($row[$passwordIndex]),
                        'role_id'     => 3,
                        'is_approved' => true,
                    ]);

                    $successCount++;
                } catch (\Exception $e) {
                    $errorRows[] = [
                        'row'    => $rowNumber,
                        'errors' => [$e->getMessage()],
                    ];
                }
            }
            fclose($handle);
        }

        return [
            'success_count' => $successCount,
            'error_rows'    => $errorRows,
        ];
    }

    /** @return array<string, mixed> */
    public function generateImportTemplate(): array
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="mahasiswa_template.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['name', 'email', 'password']);
            fputcsv($file, ['Nama Mahasiswa', 'mahasiswa@example.com', 'password123']);
            fclose($file);
        };

        return ['headers' => $headers, 'callback' => $callback];
    }
}
