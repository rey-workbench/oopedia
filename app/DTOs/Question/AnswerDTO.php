<?php

namespace App\DTOs\Question;

readonly class AnswerDTO
{
    public function __construct(
        public string $id,
        public string $question_id,
        public string $answer_text,
        public bool $is_correct,
        public \DateTimeInterface $created_at,
        public \DateTimeInterface $updated_at,
    ) {}

    public static function fromModel($answer): self
    {
        return new self(
            id: $answer->id,
            question_id: $answer->question_id,
            answer_text: $answer->answer_text,
            is_correct: (bool) $answer->is_correct,
            created_at: $answer->created_at,
            updated_at: $answer->updated_at,
        );
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'question_id' => $this->question_id,
            'answer_text' => $this->answer_text,
            'is_correct'  => $this->is_correct,
            'created_at'  => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at'  => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
