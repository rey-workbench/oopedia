<?php

namespace App\DTOs\Question;

readonly class QuestionDTO
{
    public function __construct(
        public int $id,
        public int $material_id,
        public ?int $sub_material_id,
        public string $question_text,
        public string $question_type,
        public string $difficulty,
        public ?int $created_by,
        public \DateTimeInterface $created_at,
        public \DateTimeInterface $updated_at,
    ) {}

    public static function fromModel($question): self
    {
        return new self(
            id: $question->id,
            material_id: $question->material_id,
            sub_material_id: $question->sub_material_id,
            question_text: $question->question_text,
            question_type: $question->question_type,
            difficulty: $question->difficulty,
            created_by: $question->created_by,
            created_at: $question->created_at,
            updated_at: $question->updated_at,
        );
    }

    public function toArray(): array
    {
        return [
            'id'              => $this->id,
            'material_id'     => $this->material_id,
            'sub_material_id' => $this->sub_material_id,
            'question_text'   => $this->question_text,
            'question_type'   => $this->question_type,
            'difficulty'      => $this->difficulty,
            'created_by'      => $this->created_by,
            'created_at'      => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at'      => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
