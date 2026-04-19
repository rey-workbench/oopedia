<?php

declare(strict_types=1);

namespace App\Services\Lms;

use App\Contracts\Services\QuestionAnswerServiceInterface;
use App\Enums\Lms\QuestionType;
use App\Models\Question;

final class QuestionAnswerService implements QuestionAnswerServiceInterface
{
    public function determineCorrectness(Question $question, array $data): bool
    {
        if ($question->question_type !== QuestionType::RADIO_BUTTON) {
            if ($question->question_type !== QuestionType::FILL_IN_THE_BLANK) {
                if ($question->question_type !== QuestionType::DRAG_AND_DROP) {
                    return false;
                }

                $userAnswersStr = $data['drag_and_drop_answers'] ?? '[]';
                $userAnswers    = is_array($userAnswersStr) ? $userAnswersStr : json_decode($userAnswersStr, true);

                if (empty($userAnswers)) {
                    return false;
                }

                $correctAnswers = $question->answers()->whereNotNull('drag_target')->get();
                if ($correctAnswers->isEmpty()) {
                    return false;
                }

                foreach ($correctAnswers as $correctAns) {
                    $targetPos = $correctAns->drag_target;
                    $userValue = $userAnswers[$targetPos] ?? null;

                    if (trim($userValue ?? '') !== trim($correctAns->answer_text)) {
                        return false;
                    }
                }

                return true;
            }

            $answer = trim(strtolower($data['fill_in_the_blank_answer'] ?? ''));
            if (empty($answer)) {
                return false;
            }

            return $question->answers()
                ->where('is_correct', true)
                ->get()
                ->contains(function ($ans) use ($answer) {
                    return trim(strtolower($ans->answer_text)) === $answer;
                });
        }

        if (! isset($data['answer'])) {
            return false;
        }

        $selectedAnswer = $question->answers()
            ->where('id', $data['answer'])
            ->first();

        return $selectedAnswer && $selectedAnswer->is_correct;
    }
}
