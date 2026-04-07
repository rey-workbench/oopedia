<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Models\Material;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class MaterialRepository implements MaterialRepositoryInterface
{
    /** @return Collection<int, Material> */
    public function all(): Collection
    {
        return Material::all();
    }

    public function find(string $id): ?Material
    {
        return Material::find($id);
    }

    public function create(array $data): Material
    {
        return Material::create($data);
    }

    public function update(string $id, array $data): ?Material
    {
        $material = Material::find($id);

        if ($material) {
            $material->update($data);

            return $material;
        }

        return null;
    }

    public function delete(string $id): bool
    {
        $material = Material::find($id);

        if ($material) {
            return (bool) $material->delete();
        }

        return false;
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Material::paginate($perPage, ['*'], 'page', null);
    }

    public function countAll(): int
    {
        return Material::count('*');
    }

    /** @return Collection<int, Material> */
    public function getAllWithQuestions(): Collection
    {
        return Material::with(['questions'])->get();
    }

    /** @return Collection<int, Material> */
    public function getAllWithQuestionsAndConfigs(): Collection
    {
        return Material::with(['questions'])->get();
    }

    /** @return Collection<int, Material> */
    public function getAllWithQuestionsAndActiveConfigs(): Collection
    {
        return Material::with(['questions'])->get();
    }

    public function findBySlug(string $slug): ?Material
    {
        $title = str_replace('-', ' ', $slug);

        return Material::where('title', '=', $title)->firstOrFail();
    }

    /** @return Collection<int, Material> */
    public function getAllOrdered(): Collection
    {
        return Material::with(['questions', 'media', 'creator'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function findWithQuestionsShuffled(string $id): Material
    {
        $material = Material::with(['questions.answers', 'subMaterials.questions', 'creator', 'media'])
            ->findOrFail($id);

        foreach ($material->questions as $question) {
            if ($question->question_type !== 'fill_in_the_blank') {
                $question->setRelation('answers', $question->answers->shuffle());
            }
        }

        return $material;
    }

    public function findWithQuestionsAndAnswers(string $id): Material
    {
        return Material::with(['questions.answers'])->findOrFail($id);
    }

    /** @return Collection<int, Material> */
    public function getMaterialsForAdmin(
        ?string $search = null,
        string $sort = 'created_at',
        string $direction = 'asc',
    ): Collection {
        $query = Material::newQuery();

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        $allowedSortFields = ['title', 'created_at'];

        if (in_array($sort, $allowedSortFields)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('created_at', 'asc');
        }

        return $query->with(['creator', 'subMaterials', 'media'])->get();
    }

    public function findWithRelations(string $id, array $relations = []): Material
    {
        $query = Material::newQuery();

        if (! empty($relations)) {
            $query->with($relations);
        }

        return $query->findOrFail($id);
    }

    public function getMaterialsForListing(): Collection
    {
        return Material::with(['media', 'creator'])
            ->withCount('questions')
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
