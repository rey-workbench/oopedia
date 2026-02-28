<?php

namespace App\Contracts\Repositories;

use App\Models\Answer;
use Illuminate\Database\Eloquent\Collection;

/**
 * Contract for Answer data access.
 */
interface AnswerRepositoryInterface
{
    /** @return Collection<int, Answer> */
    public function all(): Collection;

    public function find(int $id): ?Answer;

    public function findOrFail(int $id): Answer;

    public function create(array $data): Answer;

    public function update(int $id, array $data): ?Answer;

    public function delete(int $id): bool;

    /** @return Collection<int, Answer> */
    public function getByQuestionId(int $questionId): Collection;

    /** @return Collection<int, Answer> */
    public function getCorrectAnswers(int $questionId): Collection;

    public function deleteByQuestionId(int $questionId): bool;
}
