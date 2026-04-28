<?php

namespace App\Contracts\Repositories;

use App\Models\Answer;
use Illuminate\Database\Eloquent\Collection;

interface AnswerRepositoryInterface
{
    public function all(): Collection;

    public function create(array $data): Answer;

    public function deleteByQuestionId(string $questionId): bool;
}
