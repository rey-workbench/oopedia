<?php

declare(strict_types=1);

namespace App\DTOs\Question;

use Illuminate\Http\Request;

final readonly class QuestionUpdateDTO
{
    public function __construct(
        public ?string $material_id = null,
        public ?string $question_text = null,
        public ?string $question_type = null,
        public ?string $difficulty = null,
        public ?array $answers = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            material_id: $request->input('material_id'),
            question_text: $request->input('question_text'),
            question_type: $request->input('question_type'),
            difficulty: $request->input('difficulty'),
            answers: $request->input('answers'),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'material_id'     => $this->material_id,
            'question_text'   => $this->question_text,
            'question_type'   => $this->question_type,
            'difficulty'      => $this->difficulty,
            'answers'         => $this->answers,
        ], fn ($value) => $value !== null);
    }
}
