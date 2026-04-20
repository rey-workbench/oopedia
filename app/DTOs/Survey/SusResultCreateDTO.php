<?php

declare(strict_types=1);

namespace App\DTOs\Survey;

use Illuminate\Http\Request;

final readonly class SusResultCreateDTO
{
    public function __construct(
        public string $user_id,
        public ?string $nim = null,
        public ?string $class = null,
        public int $q1 = 0,
        public int $q2 = 0,
        public int $q3 = 0,
        public int $q4 = 0,
        public int $q5 = 0,
        public int $q6 = 0,
        public int $q7 = 0,
        public int $q8 = 0,
        public int $q9 = 0,
        public int $q10 = 0,
        public ?string $comments = null,
        public ?string $suggestions = null,
    ) {}

    public static function fromRequest(Request $request, string $userId): self
    {
        return new self(
            user_id: $userId,
            nim: $request->input('nim'),
            class: $request->input('class'),
            q1: (int) $request->input('q1'),
            q2: (int) $request->input('q2'),
            q3: (int) $request->input('q3'),
            q4: (int) $request->input('q4'),
            q5: (int) $request->input('q5'),
            q6: (int) $request->input('q6'),
            q7: (int) $request->input('q7'),
            q8: (int) $request->input('q8'),
            q9: (int) $request->input('q9'),
            q10: (int) $request->input('q10'),
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
            'user_id'     => $this->user_id,
            'nim'         => $this->nim,
            'class'       => $this->class,
            'q1'          => $this->q1,
            'q2'          => $this->q2,
            'q3'          => $this->q3,
            'q4'          => $this->q4,
            'q5'          => $this->q5,
            'q6'          => $this->q6,
            'q7'          => $this->q7,
            'q8'          => $this->q8,
            'q9'          => $this->q9,
            'q10'         => $this->q10,
            'comments'    => $this->comments,
            'suggestions' => $this->suggestions,
        ];
    }
}
