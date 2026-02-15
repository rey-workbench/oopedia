<?php

namespace App\Repositories;

use App\Contracts\Repositories\StudentStateRepositoryInterface;
use App\Models\StudentState;

class StudentStateRepository implements StudentStateRepositoryInterface
{
    public function upsert($userId, $materialId, array $attributes)
    {
        // For StudentState, we don't have material_id - it's global per user
        // Adjusted to match actual table structure
        return StudentState::updateOrCreate(
            ['user_id' => $userId],
            array_merge([
                'gamification_data' => [],
                'learning_profile' => [],
                'performance_metrics' => [],
                'adaptive_state' => [],
                'last_active_at' => now(),
            ], $attributes)
        );
    }

    public function getByUserAndMaterial($userId, $materialId)
    {
        // Since StudentState doesn't have material_id, return user's global state
        return StudentState::where('user_id', $userId)->first();
    }

    public function updateProgress($userId, $materialId, array $progressData)
    {
        $state = $this->getOrCreate($userId);
        
        // Merge progress data into existing state
        $performanceMetrics = $state->performance_metrics ?? [];
        $performanceMetrics = array_merge($performanceMetrics, $progressData);
        
        $state->update([
            'performance_metrics' => $performanceMetrics,
            'last_active_at' => now(),
        ]);
        
        return $state;
    }

    public function getAll($userId)
    {
        return StudentState::where('user_id', $userId)->get();
    }

    public function delete($userId, $materialId)
    {
        return StudentState::where('user_id', $userId)->delete();
    }

    protected function getOrCreate($userId)
    {
        return StudentState::firstOrCreate(
            ['user_id' => $userId],
            [
                'gamification_data' => [],
                'learning_profile' => [],
                'performance_metrics' => [],
                'adaptive_state' => [],
                'last_active_at' => now(),
            ]
        );
    }
}
