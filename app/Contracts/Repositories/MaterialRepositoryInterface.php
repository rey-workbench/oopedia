<?php

namespace App\Contracts\Repositories;

use App\Models\Material;
use Illuminate\Database\Eloquent\Collection;

interface MaterialRepositoryInterface
{
    public function all(): Collection;

    public function find(string $id): ?Material;

    public function create(array $data): Material;

    public function update(string $id, array $data): ?Material;

    public function delete(string $id): bool;

    public function countAll(): int;

    public function getAllWithQuestions(): Collection;

    public function getAllWithQuestionsAndConfigs(): Collection;

    public function getAllOrdered(): Collection;

    public function findWithQuestionsShuffled(string $id): Material;

    public function findWithQuestionsAndAnswers(string $id): Material;

    public function getMaterialsForAdmin(
        ?string $search = null,
        string $sort = 'created_at',
        string $direction = 'asc',
    ): Collection;

    public function getMaterialsForListing(): Collection;
}
