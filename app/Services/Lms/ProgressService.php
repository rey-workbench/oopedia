<?php

namespace App\Services\Lms;

use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Services\ProgressServiceInterface;

class ProgressService implements ProgressServiceInterface
{
    public function __construct(
        protected ProgressRepositoryInterface $progressRepo
    ) {}

    public function getAttemptCount($userId, $materialId, $questionId)
    {
        return $this->progressRepo->getAttemptCount($userId, $materialId, $questionId);
    }

    public function getAnsweredQuestionIds($userId, $materialId)
    {
        return $this->progressRepo->getAnsweredQuestionIds($userId, $materialId);
    }

    public function saveProgress(array $data)
    {
        return $this->progressRepo->saveProgress($data);
    }
}
