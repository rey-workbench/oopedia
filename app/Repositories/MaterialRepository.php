<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Models\Material;
use Illuminate\Database\Eloquent\Collection;

final class MaterialRepository implements MaterialRepositoryInterface
{
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

        if (! $material) {
            return null;
        }

        $material->update($data);

        return $material;
    }

    public function delete(string $id): bool
    {
        $material = Material::find($id);

        if (! $material) {
            return false;
        }

        return (bool) $material->delete();
    }

    public function countAll(): int
    {
        return Material::count('*');
    }

    public function getAllWithQuestions(): Collection
    {
        return Material::with(['questions', 'media'])->get();
    }

    public function getAllWithQuestionsAndConfigs(): Collection
    {
        return Material::with(['questions', 'media'])->get();
    }

    public function getAllOrdered(): Collection
    {
        return Material::with(['questions', 'media', 'creator'])->oldest()
            ->get();
    }

    public function findWithQuestionsShuffled(string $id): Material
    {
        $material = Material::with(['questions.answers', 'creator', 'media'])
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

    public function getMaterialsForAdmin(
        ?string $search = null,
        string $sort = 'created_at',
        string $direction = 'asc',
    ): Collection {
        $query = Material::query();

        if ($search) {
            $query->where('title', 'like', sprintf('%%%s%%', $search));
        }

        $allowedSortFields = ['title', 'created_at'];

        if (in_array($sort, $allowedSortFields, true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->oldest();
        }

        return $query->with(['creator', 'media'])->get();

    }

    public function getMaterialsForListing(): Collection
    {
        return Material::with(['media', 'creator'])
            ->withCount('questions')->oldest()
            ->get();
    }
}
