<?php

namespace App\Services\Lms;

use App\Contracts\Repositories\AnswerRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Contracts\Services\QuestionAnswerServiceInterface;
use App\Contracts\Services\GamificationServiceInterface;
use App\Contracts\Services\GuestProgressServiceInterface;
use App\Models\Question;
use Illuminate\Support\Facades\Log;

class QuestionAnswerService implements QuestionAnswerServiceInterface
{
    public function __construct(protected
        QuestionRepositoryInterface $questionRepo, protected
        AnswerRepositoryInterface $answerRepo, protected
        ProgressRepositoryInterface $progressRepo, protected
        GamificationServiceInterface $gamificationService, protected
        GuestProgressServiceInterface $guestProgressService,
        )
    {
    }

    /** @return array<string, mixed> */
    public function checkAnswer(array $data, int|string $userId, bool $isGuest): array
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
        }
        else {
            // Multiple choice / Radio button
            if (!isset($data['answer']) && $question->question_type !== 'drag_and_drop') {
                return [
                    'status' => 'error',
                    'message' => 'Pilih salah satu jawaban.',
                    'http_code' => 422,
                ];
            }

            if ($question->question_type === 'drag_and_drop') {
                $selectedAnswerText = 'Drag & Drop Answer'; // Placeholder
                $correctAnswerText = 'Correct Arrangement';
            }
            else {
                $selectedAnswer = $this->answerRepo->find($data['answer']);
                $selectedAnswerText = $selectedAnswer->answer_text ?? 'N/A';
                $correctAnswerText = $correctAnswers->first()->answer_text ?? null;
            }

            $explanation = $correctAnswers->first()->explanation ?? null;
        }

        // Gamification & Progress
        $guestStateData = null;
        $score = 0;
        $xpEarned = 0;

        if ($isGuest) {
            // Calculate Score
            $difficulty = $data['difficulty'] ?? 'beginner';
            $baseScore = 80; // Standard base
            $score = $isCorrect ? $baseScore : 0;

            // Guest Gamification (Session-based)
            $gamificationState = $this->guestProgressService->getGamificationState();
            $currentXp = $gamificationState['xp'];
            $currentStreak = $gamificationState['streak'];

            if ($isCorrect) {
                // Delegate XP calculation to QuizRewardService (single owner)
                $guestState = ['global_xp' => $currentXp];
                $rewardData = $this->gamificationService->calculateCorrectAnswerReward(
                    $guestState,
                    false, // guests cannot use hints in this flow
                    $difficulty,
                    (int)($data['time_spent'] ?? 0),
                );
                $baseXp = $rewardData['global_xp_earned'];

                // Delegate streak bonus to StreakService (single owner)
                $currentStreak++;
                $streakBonus = $this->gamificationService->calculateStreakBonusXP($currentStreak);

                $xpEarned = $baseXp + $streakBonus;
                $currentXp += $xpEarned;
            }
            else {
                $currentStreak = 0;
            }

            // Save to Session
            $this->guestProgressService->saveGamificationState($currentXp, $currentStreak);

            // Save Progress
            $this->guestProgressService->saveProgress($data, $isCorrect, $question->id);

            // Construct State for Frontend
            $guestStateData = [
                'gamification' => [
                    'global_xp' => $currentXp,
                    'current_streak' => $currentStreak,
                    'current_level' => 'Tamu',
                    'badges' => [],
                ],
                'performance' => [
                    'hints_available' => 3, // Static for guests
                    'total_questions_answered' => count($this->guestProgressService->getProgress()),
                ],
                // Mock adaptive state to prevent frontend errors
                'adaptive_state' => [
                    'last_action' => 'NEXT_QUESTION',
                    'message' => $isCorrect ? 'Benar!' : 'Salah, tetap semangat!',
                ],
            ];
        }
        else {
            $this->saveAuthenticatedProgress($data, $userId, $isCorrect, $question);
            // Score for auth users is calculated in controller/Service usually,
            // but if this path is taken, default strictly.
            $score = $isCorrect ? 100 : 0;
        }

        // Unified Response Structure
        return [
            'status' => $isCorrect ? 'success' : 'error',
            'message' => $isCorrect ? 'Jawaban Benar!' : 'Jawaban Salah',
            'score' => $score,
            'selectedAnswerText' => $selectedAnswerText,
            'correctAnswerText' => $correctAnswerText,
            'explanation' => $explanation,
            'hasNextQuestion' => true,
            'nextUrl' => route('mahasiswa.materials.questions.levels', [
                'material' => $data['material_id'],
                'difficulty' => $data['difficulty'] ?? 'beginner', // Default to prevent null
            ]),
            'adaptiveResult' => [
                'facts' => [],
                'triggered_rule' => null,
                'global_xp_earned' => $xpEarned,
                'new_state' => $guestStateData,
            ],
        ];
    }

    protected function saveAuthenticatedProgress(array $data, int|string $userId, bool $isCorrect, Question $question): void
    {
        $attemptsCount = $this->progressRepo->getAttemptCount(
            $userId,
            $data['material_id'],
            $question->id,
        );

        $attemptNumber = $attemptsCount > 0 ? $attemptsCount + 1 : 1;

        // Handle answer_id vs user_response based on question type
        $answerId = null;
        $userResponse = null;

        if ($question->question_type === 'multiple_choice' || $question->question_type === 'radio_button') {
            $answerId = $data['answer'] ?? null;
        }
        elseif ($question->question_type === 'fill_in_the_blank') {
            $userResponse = $data['fill_in_the_blank_answer'] ?? null;
        }
        elseif ($question->question_type === 'drag_and_drop') {
            $userResponse = $data['drag_and_drop_answers'] ?? null;
        }

        $this->progressRepo->saveProgress([
            'user_id' => $userId,
            'material_id' => $data['material_id'],
            'question_id' => $question->id,
            'answer_id' => $answerId,
            'user_response' => $userResponse,
            'is_correct' => $isCorrect,
            'is_answered' => true,
            'attempt_number' => $attemptNumber,
        ]);

        Log::info("User {$userId} answered question {$question->id}, attempt {$attemptNumber}, " .
            "difficulty: {$question->difficulty}, result: " . ($isCorrect ? 'CORRECT' : 'INCORRECT'));
    }


    /** @return array<string, mixed> */
    public function checkAllAnswers(array $data, int|string $userId): array
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
                'question_id' => $questionId,
            ],
            [
                'is_correct' => $isCorrect,
                'is_answered' => true,
                'attempt_number' => $attemptNumber,
            ],
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
                'selected_explanation' => $selectedAnswer ? $selectedAnswer->explanation : null,
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
            'nextUrl' => route('mahasiswa.dashboard'),
        ];
    }

    /**
     * Determine if the provided answer data is correct for the given question.
     */
    public function determineCorrectness(Question $question, array $data): bool
    {
        if ($question->question_type === 'multiple_choice' || $question->question_type === 'radio_button') {
            if (!isset($data['answer'])) {
                return false;
            }

            $selectedAnswer = $question->answers()
                ->where('id', $data['answer'])
                ->first();

            return $selectedAnswer && $selectedAnswer->is_correct;
        }

        if ($question->question_type === 'fill_in_the_blank') {
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

        if ($question->question_type === 'drag_and_drop') {
            $userAnswersStr = $data['drag_and_drop_answers'] ?? '[]';
            // It might come as a JSON string or already an array depending on how it's passed
            $userAnswers = is_array($userAnswersStr) ? $userAnswersStr : json_decode($userAnswersStr, true);

            if (empty($userAnswers)) {
                return false;
            }

            $correctAnswers = $question->answers()->whereNotNull('drag_target')->get();
            if ($correctAnswers->isEmpty()) {
                return false;
            }

            foreach ($correctAnswers as $correctAns) {
                $targetPos = $correctAns->drag_target;
                // Check if user placed this answer text at the target position
                $userValue = $userAnswers[$targetPos] ?? null;

                // Compare values (trimming whitespace)
                if (trim($userValue ?? '') !== trim($correctAns->answer_text)) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }
}
