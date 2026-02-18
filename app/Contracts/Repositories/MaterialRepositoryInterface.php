<?php

namespace App\Contracts\Repositories;

use App\Models\Material;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Contract for Material data access.
 */
interface MaterialRepositoryInterface
{
    /** @return Collection<int, Material> */
    public function all(): Collection;

    public function find(int $id): ?Material;

    public function create(array $data): Material;

    public function update(int $id, array $data): ?Material;

    public function delete(int $id): bool;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function countAll(): int;

    /** @return Collection<int, Material> */
    public function getAllWithQuestions(): Collection;

    /** @return Collection<int, Material> */
    public function getAllWithQuestionsAndConfigs(): Collection;

    /** @return Collection<int, Material> */
    public function getAllWithQuestionsAndActiveConfigs(): Collection;

    public function findBySlug(string $slug): ?Material;

    /** @return Collection<int, Material> */
    public function getAllOrdered(): Collection;

    public function findWithQuestionsShuffled(int $id): Material;

    public function findWithQuestionsAndAnswers(int $id): Material;

    /** @return Collection<int, Material> */
    public function getMaterialsForAdmin(?string $search = null, string $sort = 'created_at', string $direction = 'asc'): Collection;

    public function findWithRelations(int $id, array $relations = []): Material;
}
