<?php

namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminApproved;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserService extends BaseService
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

    public function updateAdmin($user, array $data)
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        
        return $this->userRepo->update($user->id, $data);
    }

    public function updateProfile($user, array $data)
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $this->userRepo->update($user->id, $data);
    }

    public function deleteAdmin($user)
    {
        // Don't allow deleting self or superadmin (role 1)
        if ($user->id === auth()->id() || $user->role_id === 1) {
            throw new \Exception('Tidak dapat menghapus user ini');
        }
        
        return $this->userRepo->delete($user->id);
    }

    public function getPendingAdmins($perPage = null)
    {
        // Get users with role 2 (admin) who are NOT approved
        // Pass null for perPage to get all records if needed, but keeping perPage for flexibility
        // Default sorting is created_at desc
        return $this->userRepo->getUsersByRoleAndApproval(2, false, null, $perPage);
    }

    public function approveAdmin($user)
    {
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

    public function rejectAdmin($user)
    {
        // Simply delete the user request
        return $this->userRepo->delete($user->id);
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
