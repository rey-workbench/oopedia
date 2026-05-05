<?php

declare(strict_types=1);

namespace App\DTOs\Survey;

use App\Enums\Lms\AssessmentType;
use Illuminate\Http\Request;

final readonly class SusResultCreateDTO
{
    public function __construct(
        public string $user_id,
        public AssessmentType $assessment_type = AssessmentType::PRE_TEST,
        public array $answers = [],
        public ?string $comments = null,
        public ?string $suggestions = null,
    ) {}

    public static function fromRequest(Request $request, string $userId): self
    {
        return new self(
            user_id: $userId,
            assessment_type: AssessmentType::from($request->input('assessment_type', 'pre')),
            answers: $request->input('answers', []),
            comments: $request->input('comments'),
            suggestions: $request->input('suggestions'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'user_id'         => $this->user_id,
            'assessment_type' => $this->assessment_type,
            'answers'         => $this->answers,
            'comments'        => $this->comments,
            'suggestions'     => $this->suggestions,
        ];
    }
}
