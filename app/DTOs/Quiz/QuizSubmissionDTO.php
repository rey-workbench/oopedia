<?php

declare(strict_types=1);

namespace App\DTOs\Quiz;

/**
 * Data Transfer Object for a student's answer submission.
 */
final readonly class QuizSubmissionDTO
{
    public function __construct(
        public string $userId,
        public string $materialId,
        public string $questionId,
        public ?string $answer = null,
        public ?string $fillInTheBlankAnswer = null,
        public mixed $dragAndDropAnswers = null,
        public bool $usedHint = false,
        public int $timeSpent = 0,
    ) {}

    public static function fromRequest(string $userId, string $materialId, string $questionId, array $data): self
    {
        return new self(
            userId: $userId ?: 'guest',
            materialId: $materialId,
            questionId: $questionId,
            answer: $data['answer']                                 ?? null,
            fillInTheBlankAnswer: $data['fill_in_the_blank_answer'] ?? null,
            dragAndDropAnswers: $data['drag_and_drop_answers']      ?? null,
            usedHint: (bool) ($data['used_hint'] ?? false),
            timeSpent: (int) ($data['time_spent'] ?? 0),
        );
    }

    public function toArray(): array
    {
        return [
            'answer'                   => $this->answer,
            'fill_in_the_blank_answer' => $this->fillInTheBlankAnswer,
            'drag_and_drop_answers'    => $this->dragAndDropAnswers,
            'used_hint'                => $this->usedHint,
            'time_spent'               => $this->timeSpent,
        ];
    }
}
