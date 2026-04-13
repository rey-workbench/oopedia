<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\StudentStateRepositoryInterface;
use App\Models\StudentState;
use App\Schemas\StudentStateSchema;
use Illuminate\Database\Eloquent\Collection;

final class StudentStateRepository implements StudentStateRepositoryInterface
{
    public function getByUserAndMaterial(string $userId, string $materialId): ?StudentState
    {
        return StudentState::where('user_id', $userId)->first();
    }

    /** @return Collection<string, StudentState> */
    public function getAll(string $userId): Collection
    {
        return StudentState::where('user_id', $userId)->get();
    }

    public function delete(string $userId, string $materialId): bool
    {
        return (bool) StudentState::where('user_id', $userId)->delete();
    }

    protected function getOrCreate(string $userId): StudentState
    {
        return StudentState::firstOrCreate(
            ['user_id' => $userId],
            [
                'gamification_data'   => StudentStateSchema::getDefaultGamification(),
                'learning_profile'    => StudentStateSchema::getDefaultLearningProfile(),
                'performance_metrics' => StudentStateSchema::getDefaultPerformanceMetrics(),
                'adaptive_state'      => StudentStateSchema::getDefaultAdaptiveState(),
                'last_active_at'      => now(),
            ],
        );
    }
}
