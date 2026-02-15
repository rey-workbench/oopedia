<?php

namespace App\Services\Lms\Question;

use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Contracts\Repositories\AnswerRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Services\QuestionAnswerServiceInterface;
use Illuminate\Support\Facades\Log;

class QuestionAnswerService implements QuestionAnswerServiceInterface
{
    public function __construct(
        protected QuestionRepositoryInterface $questionRepo,
        protected AnswerRepositoryInterface $answerRepo,
        protected ProgressRepositoryInterface $progressRepo
    ) {}

    public function checkAnswer($data, $userId, $isGuest)
    {
        $question = $this->questionRepo->find($data['question_id']);
        $isCorrect = false;
        $correctAnswerText = null;
        $selectedAnswerText = null;
        $explanation = null;

        // Check answer based on question type
        $isCorrect = $this->determineCorrectness($question, $data);
        $correctAnswers = $question->answers()->where('is_correct', true)->get();

        if ($question->question_type === 'fill_in_the_blank') {
            $selectedAnswerText = $data['fill_in_the_blank_answer'] ?? '';
            $correctAnswerText = $correctAnswers->first()->answer_text ?? null;
        } else {
            // Multiple choice / Radio button
            if (!isset($data['answer']) && $question->question_type !== 'drag_and_drop') {
                return [
                    'status' => 'error',
                    'message' => 'Pilih salah satu jawaban.',
                    'http_code' => 422
                ];
            }

            if ($question->question_type === 'drag_and_drop') {
                $selectedAnswerText = 'Drag & Drop Answer'; // Placeholder
                $correctAnswerText = 'Correct Arrangement';
            } else {
                $selectedAnswer = $this->answerRepo->find($data['answer']);
                $selectedAnswerText = $selectedAnswer->answer_text ?? 'N/A';
                $correctAnswerText = $correctAnswers->first()->answer_text ?? null;
            }

            $explanation = $correctAnswers->first()->explanation ?? null;
        }

        // Save progress
        if ($isGuest) {
            $this->saveGuestProgress($data, $isCorrect, $question->id);
        } else {
            $this->saveAuthenticatedProgress($data, $userId, $isCorrect, $question);
        }

        // Build response
        if ($isCorrect && $isGuest) {
            return [
                'status' => 'success',
                'message' => 'Jawaban Benar!',
                'selectedAnswerText' => $selectedAnswerText ?? null,
                'correctAnswerText' => $correctAnswerText ?? null,
                'explanation' => $explanation ?? null,
                'redirect_url' => route('mahasiswa.materials.questions.levels', [
                    'material' => $data['material_id'],
                    'difficulty' => $data['difficulty'] ?? null
                ])
            ];
        } else {
            return [
                'status' => $isCorrect ? 'success' : 'error',
                'message' => $isCorrect ? 'Jawaban Benar!' : 'Jawaban Salah',
                'selectedAnswerText' => $selectedAnswerText,
                'correctAnswerText' => $correctAnswerText,
                'explanation' => $explanation,
                'hasNextQuestion' => true,
                'nextUrl' => null
            ];
        }
    }

    protected function saveAuthenticatedProgress($data, $userId, $isCorrect, $question)
    {
        $attemptsCount = $this->progressRepo->getAttemptCount(
            $userId,
            $data['material_id'],
            $question->id
        );

        $attemptNumber = $attemptsCount > 0 ? $attemptsCount + 1 : 1;

        $this->progressRepo->saveProgress([
            'user_id' => $userId,
            'material_id' => $data['material_id'], 
            'question_id' => $question->id,
            'answer_id' => $data['answer'] ?? null,
            'is_correct' => $isCorrect,
            'is_answered' => true,
            'attempt_number' => $attemptNumber
        ]);

        Log::info("User {$userId} answered question {$question->id}, attempt {$attemptNumber}, " .
            "difficulty: {$question->difficulty}, result: " . ($isCorrect ? 'CORRECT' : 'INCORRECT'));
    }

    protected function saveGuestProgress($data, $isCorrect, $questionId)
    {
        $sessionKey = 'guest_progress';
        $guestProgress = session($sessionKey, []);

        $progressKey = $data['material_id'] . '_' . $questionId;
        $guestProgress[$progressKey] = [
            'is_correct' => $isCorrect,
            'attempt_number' => isset($guestProgress[$progressKey])
                ? $guestProgress[$progressKey]['attempt_number'] + 1
                : 1
        ];

        session([$sessionKey => $guestProgress]);

        if ($isCorrect) {
            if (!session()->has('guest_progress')) {
                session(['guest_progress' => []]);
            }

            if (!session()->has('guest_progress.' . $data['material_id'])) {
                session(['guest_progress.' . $data['material_id'] => []]);
            }

            $currentProgress = session('guest_progress.' . $data['material_id'], []);
            $currentProgress[$questionId] = [
                'is_correct' => true,
                'answered_at' => now()->toDateTimeString()
            ];

            session(['guest_progress.' . $data['material_id'] => $currentProgress]);
        }
    }

    public function checkAllAnswers($data, $userId)
    {
        $materialId = $data['material_id'];
        $answers = $data['answers'];

        $totalQuestions = count($answers);
        $correctAnswers = 0;
        $results = [];

        foreach ($answers as $questionId => $answerId) {
            $question = $this->questionRepo->findWithAnswers($questionId);
            $selectedAnswer = $question->answers->find($answerId);

            $isCorrect = $selectedAnswer && $selectedAnswer->is_correct;
            $correctAnswer = $question->answers->where('is_correct', true)->first();

            $attemptNumber = $this->progressRepo->getAttemptCount($userId, $materialId, $questionId) + 1;

            $this->progressRepo->updateOrCreateProgress(
                [
                    'user_id' => $userId,
                    'material_id' => $materialId, 
                    'question_id' => $questionId
                ],
                [
                    'is_correct' => $isCorrect,
                    'is_answered' => true,
                    'attempt_number' => $attemptNumber
                ]
            );

            if ($isCorrect) {
                $correctAnswers++;
            }

            $results[$questionId] = [
                'is_correct' => $isCorrect,
                'question_text' => $question->question_text,
                'selected_answer' => $selectedAnswer ? $selectedAnswer->answer_text : null,
                'correct_answer' => $isCorrect ? null : ($correctAnswer->answer_text ?? null),
                'explanation' => $correctAnswer->explanation ?? null,
                'selected_explanation' => $selectedAnswer ? $selectedAnswer->explanation : null
            ];
        }

        $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;

        return [
            'status' => 'success',
            'message' => "Anda menjawab benar $correctAnswers dari $totalQuestions soal (Skor: $score%)",
            'score' => $score,
            'results' => $results,
            'correctAnswers' => $correctAnswers,
            'totalQuestions' => $totalQuestions,
            'nextUrl' => route('mahasiswa.dashboard')
        ];
    }

    /**
     * Determine if the provided answer data is correct for the given question.
     */
    public function determineCorrectness($question, array $data): bool
    {
        if ($question->question_type === 'multiple_choice' || $question->question_type === 'radio_button') {
            if (!isset($data['answer'])) return false;
            
            $selectedAnswer = $question->answers()
                ->where('id', $data['answer'])
                ->first();
                
            return $selectedAnswer && $selectedAnswer->is_correct;
        } 
        
        if ($question->question_type === 'fill_in_the_blank') {
            $answer = trim(strtolower($data['fill_in_the_blank_answer'] ?? ''));
            if (empty($answer)) return false;

            return $question->answers()
                ->where('is_correct', true)
                ->get()
                ->contains(function ($ans) use ($answer) {
                    return trim(strtolower($ans->answer_text)) === $answer;
                });
        } 
        
        if ($question->question_type === 'drag_and_drop') {
            // Placeholder for drag and drop logic
            return false;
        }

        return false;
    }
}
