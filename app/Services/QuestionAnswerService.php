<?php

namespace App\Services;

use App\Repositories\QuestionRepository;
use App\Repositories\AnswerRepository;
use App\Repositories\ProgressRepository;
use Illuminate\Support\Facades\Log;

class QuestionAnswerService
{
    protected $questionRepo;
    protected $answerRepo;
    protected $progressRepo;

    public function __construct(
        QuestionRepository $questionRepo,
        AnswerRepository $answerRepo,
        ProgressRepository $progressRepo
    ) {
        $this->questionRepo = $questionRepo;
        $this->answerRepo = $answerRepo;
        $this->progressRepo = $progressRepo;
    }

    public function checkAnswer($data, $userId, $isGuest)
    {
        $question = $this->questionRepo->find($data['question_id']);
        $isCorrect = false;
        $correctAnswerText = null;
        $selectedAnswerText = null;
        $explanation = null;

        // Check answer based on question type
        if ($question->question_type === 'fill_in_the_blank') {
            $userAnswer = trim(strtolower($data['fill_in_the_blank_answer']));
            $correctAnswer = trim(strtolower($question->correct_answer));
            $isCorrect = $userAnswer === $correctAnswer;
            $selectedAnswerText = $data['fill_in_the_blank_answer'];
            $correctAnswerText = $question->correct_answer;
        } elseif ($question->question_type === 'true_false') {
            $userAnswer = $data['answer'] === 'true';
            $isCorrect = $userAnswer === $question->is_true;
            $selectedAnswerText = $userAnswer ? 'Benar' : 'Salah';
            $correctAnswerText = $question->is_true ? 'Benar' : 'Salah';
        } else {
            // Multiple choice
            if (!isset($data['answer'])) {
                return [
                    'status' => 'error',
                    'message' => 'Pilih salah satu jawaban.',
                    'http_code' => 422
                ];
            }

            $selectedAnswer = $this->answerRepo->find($data['answer']);
            $isCorrect = $selectedAnswer->is_correct;
            $selectedAnswerText = $selectedAnswer->answer_text;

            if (!$isCorrect) {
                $correctAnswer = $this->answerRepo->getCorrectAnswer($question->id);
                $correctAnswerText = $correctAnswer->answer_text ?? null;
            }

            $explanation = $selectedAnswer->explanation;
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
            'material_id' => $data['material_id'], // Repos uses question_id to link to material via Question model, but we pass it anyway
            'question_id' => $question->id,
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
                    'material_id' => $materialId, // Handled safely by Repo
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
}
