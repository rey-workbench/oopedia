<?php

declare(strict_types=1);

namespace App\DTOs\Question;

use Illuminate\Http\Request;

final readonly class QuestionCreateDTO
{
    public function __construct(
        public string $material_id,
        public string $question_text,
        public string $question_type,
        public string $difficulty,
        public string $created_by,
        public array $answers,
    ) {}

    public static function fromRequest(Request $request, string $userId): self
    {
        return new self(
            material_id: $request->input('material_id'),
            question_text: $request->input('question_text'),
            question_type: $request->input('question_type'),
            difficulty: $request->input('difficulty'),
            created_by: $userId,
            answers: self::processAnswers($request),
        );
    }

    private static function processAnswers(Request $request): array
    {
        $answers      = $request->input('answers', []);
        $questionType = $request->input('question_type');

        if (
            in_array($questionType, ['radio_button', 'fill_in_the_blank'])
            && $request->has('correct_answer')
        ) {
            $correctIndex     = $request->input('correct_answer');
            $processedAnswers = [];

            foreach ($answers as $index => $answer) {
                $processedAnswers[] = [
                    'answer_text' => $answer['answer_text'] ?? $answer,
                    'is_correct'  => ($index == $correctIndex) ? 1 : 0,
                ];
            }

            return $processedAnswers;
        }

        return $answers;
    }

    public function toArray(): array
    {
        return [
            'material_id'     => $this->material_id,
            'question_text'   => $this->question_text,
            'question_type'   => $this->question_type,
            'difficulty'      => $this->difficulty,
            'created_by'      => $this->created_by,
            'answers'         => $this->answers,
        ];
    }
}
