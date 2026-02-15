<?php

namespace App\Contracts\Services;

interface StudentServiceInterface
{
    public function getStudentsList($search = null, $perPage = 10);
    
    public function getStudentById($id);
    
    public function createStudent(array $data);
    
    public function updateStudent($studentId, array $data);
    
    public function deleteStudent($studentId);
    
    public function getPendingStudents($perPage = null);
    
    public function approveStudent($studentId);
    
    public function rejectStudent($studentId);
    
    public function importStudentsFromFile($file);
}
