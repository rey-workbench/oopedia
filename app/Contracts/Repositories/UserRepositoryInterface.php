<?php

namespace App\Contracts\Repositories;

interface UserRepositoryInterface
{
    public function all();
    
    public function find($id);
    
    public function create(array $data);
    
    public function update($id, array $data);
    
    public function delete($id);
    
    public function paginate($perPage = 15);
    
    public function countAll();
    
    public function getStudentsList($search = null, $perPage = 10);
    
    public function getStudentsWithRole($roleId, $search = null, $perPage = 10);
    
    public function deleteStudentData($userId);
    
    public function findByEmail($email);
    
    public function getUnapprovedStudents();
    
    public function approveStudent($userId);
}
