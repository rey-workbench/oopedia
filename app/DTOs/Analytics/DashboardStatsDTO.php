<?php

namespace App\DTOs\Analytics;

readonly class DashboardStatsDTO
{
    public function __construct(
        public int $total_materials,
        public int $total_questions,
        public int $total_students,
        public int $answered_questions,
        public int $correct_answers,
        public float $accuracy_percentage,
        public array $recent_activities,
    ) {}

    public function toArray(): array
    {
        return [
            'total_materials' => $this->total_materials,
            'total_questions' => $this->total_questions,
            'total_students' => $this->total_students,
            'answered_questions' => $this->answered_questions,
            'correct_answers' => $this->correct_answers,
            'accuracy_percentage' => $this->accuracy_percentage,
            'recent_activities' => $this->recent_activities,
        ];
    }
}
