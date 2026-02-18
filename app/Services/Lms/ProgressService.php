<?php

namespace App\Services\Lms;

use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Services\ProgressServiceInterface;
use App\Models\QuizAttempt;
use Illuminate\Support\Collection as SupportCollection;

class ProgressService implements ProgressServiceInterface
{
    public function __construct(
        protected ProgressRepositoryInterface $progressRepo,
    ) {}

    public function getAttemptCount(int|string $userId, int $materialId, int $questionId): int
    {
        return $this->progressRepo->getAttemptCount($userId, $materialId, $questionId);
    }

    /** @return SupportCollection<int, int> */
    public function getAnsweredQuestionIds(int|string $userId, int $materialId): SupportCollection
    {
        return $this->progressRepo->getAnsweredQuestionIds($userId, $materialId);
    }

    public function saveProgress(array $data): QuizAttempt
    {
        return $this->progressRepo->saveProgress($data);
    }
}
