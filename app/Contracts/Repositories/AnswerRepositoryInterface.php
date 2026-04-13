<?php

namespace App\Contracts\Repositories;

use App\Models\Answer;
use Illuminate\Database\Eloquent\Collection;

interface AnswerRepositoryInterface
{
    public function all(): Collection;

    public function find(string $id): ?Answer;

    public function findOrFail(string $id): Answer;

    public function create(array $data): Answer;

    public function update(string $id, array $data): ?Answer;

    public function delete(string $id): bool;

    public function getCorrectAnswers(string $questionId): Collection;

    public function deleteByQuestionId(string $questionId): bool;
}
