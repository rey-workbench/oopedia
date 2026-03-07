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

    public function find(string $id): ?Answer;

    public function findOrFail(string $id): Answer;

    public function create(array $data): Answer;

    public function update(string $id, array $data): ?Answer;

    public function delete(string $id): bool;

    /** @return Collection<int, Answer> */
    public function getByQuestionId(string $questionId): Collection;

    /** @return Collection<int, Answer> */
    public function getCorrectAnswers(string $questionId): Collection;

    public function deleteByQuestionId(string $questionId): bool;
}
