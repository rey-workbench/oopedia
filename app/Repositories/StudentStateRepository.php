<?php

namespace App\Repositories;

use App\Contracts\Repositories\StudentStateRepositoryInterface;
use App\Models\StudentState;
use Illuminate\Database\Eloquent\Collection;

class StudentStateRepository implements StudentStateRepositoryInterface
{
    public function upsert(string $userId, string $materialId, array $attributes): StudentState
    {
        return StudentState::query()->updateOrCreate(
        ['user_id' => $userId],
            array_merge([
            'gamification_data' => [],
            'learning_profile' => [],
            'performance_metrics' => [],
            'adaptive_state' => [],
            'last_active_at' => now(),
        ], $attributes),
        );
    }

    public function getByUserAndMaterial(string $userId, string $materialId): ?StudentState
    {
        return StudentState::query()->where('user_id', '=', $userId)->first();
    }

    public function updateProgress(string $userId, string $materialId, array $progressData): void
    {
        $state = $this->getOrCreate($userId);

        $performanceMetrics = $state->performance_metrics ?? [];
        $performanceMetrics = array_merge($performanceMetrics, $progressData);

        $state->update([
            'performance_metrics' => $performanceMetrics,
            'last_active_at' => now(),
        ]);
    }

    /** @return Collection<string, StudentState> */
    public function getAll(string $userId): Collection
    {
        return StudentState::query()->where('user_id', '=', $userId)->get();
    }

    public function delete(string $userId, string $materialId): bool
    {
        return (bool)StudentState::query()->where('user_id', '=', $userId)->delete();
    }

    protected function getOrCreate(string $userId): StudentState
    {
        return StudentState::query()->firstOrCreate(
        ['user_id' => $userId],
        [
            'gamification_data' => [],
            'learning_profile' => [],
            'performance_metrics' => [],
            'adaptive_state' => [],
            'last_active_at' => now(),
        ],
        );
    }
}
