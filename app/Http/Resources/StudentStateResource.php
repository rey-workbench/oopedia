<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\StudentState;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin StudentState
 */
final class StudentStateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[\Override]
    public function toArray(Request $request): array
    {
        return [
            'accuracy'            => round((float) ($this->accuracy ?? 0), 2),
            'xp'                  => $this->xp,
            'streak'              => $this->streak,
            'level'               => $this->level  ?? 'Pemula',
            'badges'              => $this->badges ?? [],
            'hints_available'     => $this->hints_available,
            'target_difficulty'   => $this->target_difficulty,
            'adaptive_state'      => $this->adaptive_state      ?? [],
            'performance_metrics' => $this->performance_metrics ?? [],
            'last_active_at'      => $this->updated_at?->toIso8601String(),
        ];
    }
}
