<?php

namespace App\Repositories\Interfaces;

interface UeqSurveyRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllWithUser($class = null);
    public function getDistinctClasses();
    public function findByUserId($userId);
}
