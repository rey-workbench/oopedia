<?php

namespace App\Repositories;

use App\Contracts\Repositories\StudentStateRepositoryInterface;
use App\Models\StudentState;
use Illuminate\Database\Eloquent\Collection;

class StudentStateRepository implements StudentStateRepositoryInterface
{
    public function upsert(int $userId, int $materialId, array $attributes): StudentState
    {
        return StudentState::updateOrCreate(
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

    public function getByUserAndMaterial(int $userId, int $materialId): ?StudentState
    {
        return StudentState::where('user_id', $userId)->first();
    }

    public function updateProgress(int $userId, int $materialId, array $progressData): void
    {
        $state = $this->getOrCreate($userId);

        $performanceMetrics = $state->performance_metrics ?? [];
        $performanceMetrics = array_merge($performanceMetrics, $progressData);

        $state->update([
            'performance_metrics' => $performanceMetrics,
            'last_active_at' => now(),
        ]);
    }

    /** @return Collection<int, StudentState> */
    public function getAll(int $userId): Collection
    {
        return StudentState::where('user_id', $userId)->get();
    }

    public function delete(int $userId, int $materialId): bool
    {
        return (bool) StudentState::where('user_id', $userId)->delete();
    }

    protected function getOrCreate(int $userId): StudentState
    {
        return StudentState::firstOrCreate(
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
