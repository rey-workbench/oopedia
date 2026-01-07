<?php

namespace App\Repositories\Interfaces;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function getStudentsList($search = null, $perPage = 10);
    public function deleteStudentWithRelations($userId);
    public function createStudent(array $data);
    public function getStudentsWithRole($roleId, $search = null, $perPage = 10);
    public function getUsersByRoleAndApproval($roleId, $isApproved, $search = null, $perPage = 10, $sortBy = 'created_at', $sortOrder = 'desc');
    public function approveUser($userId);
    public function getActiveStudentsCount($days);
    public function getStudentProgressOverview($limit);
}
