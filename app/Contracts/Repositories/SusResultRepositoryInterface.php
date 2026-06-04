<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Models\SusAnswer;
use App\Models\SusQuestion;
use App\Models\SusResult;
use Illuminate\Database\Eloquent\Collection;

interface SusResultRepositoryInterface
{
    /**
     * @return Collection<int, SusResult>
     */
    public function getAllWithUser(?string $assessmentType = null): Collection;

    public function findWithRelations(string $id): SusResult;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): SusResult;

    /** @return Collection<int, SusQuestion> */
    public function getOrderedQuestions(): Collection;

    public function createAnswer(array $data): SusAnswer;

    public function getAverageValueForQuestion(string $questionId, array $resultIds): ?float;
}
