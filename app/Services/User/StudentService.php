<?php

namespace App\Services\User;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Services\StudentServiceInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Exception;

class StudentService implements StudentServiceInterface
{
    protected $userRepo;
    protected $materialRepo;
    protected $progressRepo;

    public function __construct(
        UserRepositoryInterface $userRepo,
        MaterialRepositoryInterface $materialRepo,
        ProgressRepositoryInterface $progressRepo
    ) {
        $this->userRepo = $userRepo;
        $this->materialRepo = $materialRepo;
        $this->progressRepo = $progressRepo;
    }

    public function getStudentsWithProgress($search = null, $perPage = 10)
    {
        $students = $this->userRepo->getStudentsList($search, $perPage);
        
        // Get all materials 
        $materials = $this->materialRepo->getAllOrdered();
        
        // Calculate total questions
        $totalQuestions = $materials->sum(fn($m) => $m->questions->count());
        
        // Add progress data to each student
        foreach ($students as $student) {
            $progressStats = $this->progressRepo->getUserProgressStats($student->id);
            $correctAnswers = $progressStats->sum('correct_answers');
            
            $student->overall_progress = $totalQuestions > 0 
                ? min(100, round(($correctAnswers / $totalQuestions) * 100))
                : 0;
            
            $student->total_answered_questions = $progressStats->sum('answered_questions');
        }
        
        return $students;
    }

    public function getStudentsList($search = null, $perPage = 10)
    {
        return $this->userRepo->getStudentsList($search, $perPage);
    }

    public function getStudentById($id)
    {
        return $this->userRepo->find($id);
    }

    public function createStudent(array $data)
    {
        // Hash password
        $data['password'] = Hash::make($data['password']);
        $data['role_id'] = 3; // Student role
        $data['is_approved'] = true; // Admin-created students are auto-approved
        
        return $this->userRepo->create($data);
    }

    public function updateStudent($studentId, array $data)
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        
        return $this->userRepo->update($studentId, $data);
    }

    public function deleteStudent($studentId)
    {
        $student = $this->userRepo->find($studentId);
        if (!$student) {
            throw new Exception('Student not found');
        }

        DB::beginTransaction();
        try {
            $this->userRepo->deleteStudentData($studentId);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getPendingStudents($perPage = null)
    {
        return $this->userRepo->getUsersByRoleAndApproval(3, false, null, $perPage);
    }

    public function approveStudent($studentId)
    {
        return $this->userRepo->approveStudent($studentId);
    }

    public function rejectStudent($studentId)
    {
        return $this->userRepo->delete($studentId);
    }

    public function getStudentProgressDetail($student)
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
            $correctAnswers = $materialProgress ? $materialProgress->correct_answers : 0;
            
            // Calculate progress percentage
            $progressPercentage = $totalQuestions > 0 
                ? min(100, round(($correctAnswers / $totalQuestions) * 100))
                : 0;
            
            // Get last access time
            $lastAccessed = $this->progressRepo->getLastAccessTime($student->id, $material->id);
            
            $materialsWithProgress->push((object)[
                'id' => $material->id,
                'title' => $material->title,
                'total_questions' => $totalQuestions,
                'answered_questions' => $correctAnswers,
                'progress' => $progressPercentage,
                'last_accessed' => $lastAccessed ? \Carbon\Carbon::parse($lastAccessed) : null
            ]);
        }
        
        $missingQuestionsByMaterial = [];
        
        foreach ($materialsWithProgress as $item) {
            $missingCount = max(0, $item->total_questions - $item->answered_questions);
            
            if ($missingCount > 0) {
                $missingQuestionsByMaterial[] = [
                    'material_title' => $item->title,
                    'missing_count' => $missingCount
                ];
            }
        }
        
        // Get recent activities
        $recentActivities = $this->progressRepo->getRecentActivities($student->id, 10);
        
        return [
            'materials' => $materialsWithProgress,
            'recent_activities' => $recentActivities,
            'missingQuestionsByMaterial' => $missingQuestionsByMaterial
        ];
    }

    public function importStudentsFromFile($file)
    {
        $path = $file->getRealPath();
        $successCount = 0;
        $errorRows = [];
        
        if (($handle = fopen($path, 'r')) !== false) {
            // Read header
            $header = fgetcsv($handle, 1000, ',');
            
            // Validate required columns
            $requiredColumns = ['name', 'email', 'password'];
            $missingColumns = array_diff($requiredColumns, $header);
            
            if (!empty($missingColumns)) {
                throw new \Exception('File tidak memiliki kolom yang diperlukan: ' . implode(', ', $missingColumns));
            }
            
            // Map column indexes
            $nameIndex = array_search('name', $header);
            $emailIndex = array_search('email', $header);
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
                    'name' => $row[$nameIndex] ?? '',
                    'email' => $row[$emailIndex] ?? '',
                    'password' => $row[$passwordIndex] ?? '',
                ];
                
                $validator = Validator::make($rowData, [
                    'name' => 'required|string|max:255',
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
                        'row' => $rowNumber,
                        'errors' => $validator->errors()->all(),
                    ];
                    continue;
                }
                
                // Create the student
                try {
                    $this->userRepo->createStudent([
                        'name' => $row[$nameIndex],
                        'email' => $row[$emailIndex],
                        'password' => Hash::make($row[$passwordIndex]),
                    ]);
                    
                    $successCount++;
                } catch (\Exception $e) {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'errors' => [$e->getMessage()],
                    ];
                }
            }
            fclose($handle);
        }
        
        return [
            'success_count' => $successCount,
            'error_rows' => $errorRows
        ];
    }

    public function generateImportTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="mahasiswa_template.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $callback = function() {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['name', 'email', 'password']);
            fputcsv($file, ['Nama Mahasiswa', 'mahasiswa@example.com', 'password123']);
            fclose($file);
        };
        
        return ['headers' => $headers, 'callback' => $callback];
    }
}
