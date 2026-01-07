<?php

namespace App\Repositories\Interfaces;

interface QuestionBankRepositoryInterface extends BaseRepositoryInterface
{
    public function getWithCreator($search = null, $perPage = 10);
    public function getWithRelations($id, array $relations);
}
