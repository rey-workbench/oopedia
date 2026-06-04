<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\DTOs\Quiz\QuizSubmissionDTO;
use App\Models\Question;

interface QuizSubmissionServiceInterface
{
    public function determineCorrectness(Question $question, array $data): bool;

    public function handleSubmission(QuizSubmissionDTO $quizSubmissionDTO): array;
}
