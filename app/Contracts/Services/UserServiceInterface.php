<?php

namespace App\Contracts\Services;

interface UserServiceInterface
{
    public function getUserById($id);
    
    public function getAdmins($search = null, $perPage = 10);
    
    public function createAdmin(array $data);
    
    public function updateAdmin($userId, array $data);
    
    public function updateProfile($userId, array $data);
    
    public function deleteAdmin($userId);
    
    public function getPendingAdmins($perPage = null);
    
    public function getPendingAdminsCount();
    
    public function approveAdmin($userId);
    
    public function rejectAdmin($userId);
    
    public function registerUser(array $data);
    
    public function createStudent(array $data);
    
    public function importAdminsFromFile($file);
    
    public function generateImportTemplate();
}
