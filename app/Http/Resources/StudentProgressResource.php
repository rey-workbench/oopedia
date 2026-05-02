<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
final class StudentProgressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[\Override]
    public function toArray(Request $request): array
    {
        $uniqueCorrectQuestions = $this->quizAttempts
            ->where('is_correct', true)
            ->pluck('question_id')
            ->unique()
            ->count();

        $accuracy = $this->quizAttempts->count() > 0
            ? round(($this->quizAttempts->where('is_correct', true)->count() / $this->quizAttempts->count()) * 100, 1)
            : 0;

        $totalConfiguredQuestions = $this->additional['totalConfiguredQuestions'] ?? 100;

        return [
            'id'                  => $this->id,
            'user'                => [
                'id'   => $this->id,
                'name' => $this->name,
            ],
            'accuracy'            => $accuracy,
            'correct_count'       => $uniqueCorrectQuestions,
            'completed_materials' => $totalConfiguredQuestions > 0 ? round(($uniqueCorrectQuestions / $totalConfiguredQuestions) * 100) : 0,
            'average_score'       => round($this->quizAttempts->avg('score') ?? 0, 1),
        ];
    }
}
