<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\StudentStateRepositoryInterface;
use App\Models\StudentState;
use App\Rules\Adaptive\Constants\StudentStateSchema;

final class StudentStateRepository implements StudentStateRepositoryInterface
{
    public function findOrCreate(string $userId): StudentState
    {
        if ($userId === 'guest') {
            return new StudentState(array_merge(
                StudentStateSchema::defaults(),
                ['user_id' => 'guest'],
            ));
        }

        return StudentState::firstOrCreate(
            ['user_id' => $userId],
            array_merge(StudentStateSchema::defaults(), ['last_active_at' => null]),
        );
    }

    public function update(string $userId, array $data): StudentState
    {
        $state = $this->findOrCreate($userId);

        if ($userId !== 'guest') {
            $state->update($data);
        } else {
            $state->fill($data);
        }

        return $state;
    }
}
