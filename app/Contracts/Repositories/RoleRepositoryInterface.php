<?php

namespace App\Contracts\Repositories;

interface RoleRepositoryInterface
{
    public function all();
    
    public function find($id);
    
    public function findByName($name);
    
    public function getUsersByRole($roleId);
}
