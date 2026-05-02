<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $id
 * @property string $name
 * @property int $total_correct_questions
 * @property int $weighted_score
 * @property int $rank
 * @property float $percentage
 * @property string $formatted_score
 * @property string $badge
 * @property string $badge_color
 * @property int $beginner_completed
 * @property int $medium_completed
 * @property int $hard_completed
 */
final class LeaderboardResource extends JsonResource
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
            'id'                      => $this->id,
            'name'                    => $this->name,
            'total_correct_questions' => $this->total_correct_questions,
            'weighted_score'          => $this->weighted_score,
            'rank'                    => $this->rank,
            'percentage'              => $this->percentage,
            'formatted_score'         => $this->formatted_score,
            'badge'                   => $this->badge,
            'badge_color'             => $this->badge_color,
            'stats'                   => [
                'beginner' => $this->beginner_completed,
                'medium'   => $this->medium_completed,
                'hard'     => $this->hard_completed,
            ],
        ];
    }
}
