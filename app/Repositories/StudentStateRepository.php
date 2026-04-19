<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\StudentStateRepositoryInterface;
use App\Models\StudentState;
use App\Schemas\StudentStateSchema;

final class StudentStateRepository implements StudentStateRepositoryInterface
{
    public function findOrCreate(string $userId): StudentState
    {
        if ($userId === 'guest') {
            return new StudentState([
                'user_id'             => 'guest',
                'gamification_data'   => StudentStateSchema::getDefaultGamification(),
                'learning_profile'    => StudentStateSchema::getDefaultLearningProfile(),
                'performance_metrics' => StudentStateSchema::getDefaultPerformanceMetrics(),
                'adaptive_state'      => StudentStateSchema::getDefaultAdaptiveState(),
            ]);
        }

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

    public function update(string $userId, array $data): StudentState
    {
        $state = $this->findOrCreate($userId);

        if ($userId !== 'guest') {
            $state->update($data);
        } else {
            // For guests, we just update the in-memory instance
            $state->fill($data);
        }

        return $state;
    }
}
