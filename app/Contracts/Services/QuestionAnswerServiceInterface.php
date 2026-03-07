<?php

namespace App\Contracts\Services;

use App\Models\Question;

/**
 * Contract for checking and processing answers to quiz questions.
 */
interface QuestionAnswerServiceInterface
{
    /**
     * Check a single answer submission.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function checkAnswer(array $data, string $userId, bool $isGuest): array;

    /**
     * Check all answers for a completed quiz session.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function checkAllAnswers(array $data, string $userId): array;

    /**
     * Determine if a given answer for a question is correct.
     */
    public function determineCorrectness(Question $question, array $data): bool;
}
