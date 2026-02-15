<?php

namespace App\Services\User;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminApproved;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Exception;

class UserService implements UserServiceInterface
{
    protected $userRepo;

    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function getUserById($id)
    {
        return $this->userRepo->find($id);
    }

    public function getAdmins($search = null, $perPage = 10)
    {
        // Role 2 is Admin
        return $this->userRepo->getStudentsWithRole(2, $search, $perPage);
    }

    public function createAdmin(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $data['role_id'] = 2; // Admin role
        $data['is_approved'] = true; // Admins created by superadmin are auto-approved
        
        return $this->userRepo->create($data);
    }

    public function updateAdmin($userId, array $data)
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        
        return $this->userRepo->update($userId, $data);
    }

    public function updateProfile($userId, array $data)
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $this->userRepo->update($userId, $data);
    }

    public function deleteAdmin($userId)
    {
        $user = $this->userRepo->find($userId);
        if (!$user) {
            throw new Exception('User not found');
        }

        // Don't allow deleting self or superadmin (role 1)
        if ($user->id === auth()->id() || $user->role_id === 1) {
            throw new Exception('Tidak dapat menghapus user ini');
        }
        
        return $this->userRepo->delete($user->id);
    }

    public function getPendingAdmins($perPage = null)
    {
        return $this->userRepo->getUsersByRoleAndApproval(2, false, null, $perPage);
    }

    public function getPendingAdminsCount()
    {
        return $this->userRepo->getUsersByRoleAndApproval(2, false, null, null)->count();
    }

    public function approveAdmin($userId)
    {
        $user = $this->userRepo->find($userId);
        if (!$user) {
            throw new Exception('User not found');
        }

        DB::beginTransaction();
        try {
            $this->userRepo->approveUser($user->id);            
            Mail::to($user->email)->send(new AdminApproved($user));

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function rejectAdmin($userId)
    {
        // Simply delete the user request
        return $this->userRepo->delete($userId);
    }

    public function registerUser(array $data)
    {
        // Hash password
        $data['password'] = Hash::make($data['password']);
        
        // Determine role_id based on email domain or provided role_id
        if (!isset($data['role_id'])) {
            $data['role_id'] = str_ends_with($data['email'], '@admin.oopedia.com') ? 2 : 3;
        }
        
        // Admin auto-approved, students need approval
        if (!isset($data['is_approved'])) {
            $data['is_approved'] = ($data['role_id'] === 2);
        }
        
        return $this->userRepo->create($data);
    }

    public function createStudent(array $data)
    {
        // Hash password
        $data['password'] = Hash::make($data['password']);
        $data['role_id'] = 3; // Student role
        $data['is_approved'] = true; // Admin-created students are auto-approved
        
        return $this->userRepo->create($data);
    }

    public function importAdminsFromFile($file)
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
                
                // Create the admin
                try {
                    $this->createAdmin([
                        'name' => $row[$nameIndex],
                        'email' => $row[$emailIndex],
                        'password' => $row[$passwordIndex], // Will be hashed in createAdmin
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
            'Content-Disposition' => 'attachment; filename="admin_template.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $callback = function() {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['name', 'email', 'password']);
            fputcsv($file, ['Nama Admin', 'admin@example.com', 'password123']);
            fclose($file);
        };
        
        return ['headers' => $headers, 'callback' => $callback];
    }
}
