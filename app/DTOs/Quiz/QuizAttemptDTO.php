<?php

namespace App\DTOs\Quiz;

readonly class QuizAttemptDTO
{
    public function __construct(
        public string $id,
        public string $user_id,
        public string $question_id,
        public ?string $answer_id,
        public bool $is_correct,
        public int $score,
        public int $attempt_number,
        public int $time_spent,
        public \DateTimeInterface $created_at,
        public \DateTimeInterface $updated_at,
    ) {}

    public static function fromModel($attempt): self
    {
        return new self(
            id: $attempt->id,
            user_id: $attempt->user_id,
            question_id: $attempt->question_id,
            answer_id: $attempt->answer_id,
            is_correct: (bool) $attempt->is_correct,
            score: $attempt->score,
            attempt_number: $attempt->attempt_number,
            time_spent: $attempt->time_spent,
            created_at: $attempt->created_at,
            updated_at: $attempt->updated_at,
        );
    }

    public function toArray(): array
    {
        return [
            'id'             => $this->id,
            'user_id'        => $this->user_id,
            'question_id'    => $this->question_id,
            'answer_id'      => $this->answer_id,
            'is_correct'     => $this->is_correct,
            'score'          => $this->score,
            'attempt_number' => $this->attempt_number,
            'time_spent'     => $this->time_spent,
            'created_at'     => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at'     => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
