<?php

namespace App\DTOs\Analytics;

readonly class StudentProgressDTO
{
    public function __construct(
        public string $user_id,
        public string $material_id,
        public int $total_questions,
        public int $answered_questions,
        public int $correct_answers,
        public float $progress_percentage,
        public float $accuracy_percentage,
        public array $difficulty_breakdown,
    ) {
    }

    public function toArray(): array
    {
        return [
            'user_id'              => $this->user_id,
            'material_id'          => $this->material_id,
            'total_questions'      => $this->total_questions,
            'answered_questions'   => $this->answered_questions,
            'correct_answers'      => $this->correct_answers,
            'progress_percentage'  => $this->progress_percentage,
            'accuracy_percentage'  => $this->accuracy_percentage,
            'difficulty_breakdown' => $this->difficulty_breakdown,
        ];
    }
}
