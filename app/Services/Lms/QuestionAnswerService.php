<?php

namespace App\Services\Lms;

use App\Contracts\Repositories\AnswerRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Contracts\Services\GamificationServiceInterface;
use App\Contracts\Services\GuestProgressServiceInterface;
use App\Contracts\Services\QuestionAnswerServiceInterface;
use App\Models\Question;
use Illuminate\Support\Facades\Log;

class QuestionAnswerService implements QuestionAnswerServiceInterface
{
    public function __construct(
        protected QuestionRepositoryInterface $questionRepo,
        protected AnswerRepositoryInterface $answerRepo,
        protected ProgressRepositoryInterface $progressRepo,
        protected GamificationServiceInterface $gamificationService,
        protected GuestProgressServiceInterface $guestProgressService,
    ) {}

    /** @return array<string, mixed> */
    public function checkAnswer(array $data, string $userId, bool $isGuest): array
    {
        $question = $this->questionRepo->find($data['question_id']);

        $isCorrect         = $this->determineCorrectness($question, $data);
        $correctAnswers    = $question->answers()->where('is_correct', true)->get();
        $correctAnswerText = $correctAnswers->first()->answer_text ?? null;
        $explanation       = $correctAnswers->first()->explanation ?? null;

        $selectedAnswerText = $this->getSelectedAnswerText($question, $data, $correctAnswerText);

        if (! $this->hasValidAnswer($question, $data)) {
            return [
                'status'    => 'error',
                'message'   => 'Pilih salah satu jawaban.',
                'http_code' => 422,
            ];
        }

        $guestStateData = null;
        $score          = 0;
        $xpEarned       = 0;

        if ($isGuest) {
            [$score, $xpEarned, $guestStateData] = $this->processGuestAnswer($data, $question, $isCorrect);
        } else {
            $this->saveAuthenticatedProgress($data, $userId, $isCorrect, $question);
            $score = $isCorrect ? 100 : 0;
        }

        return [
            'status'             => $isCorrect ? 'success' : 'error',
            'message'            => $isCorrect ? 'Jawaban Benar!' : 'Jawaban Salah',
            'score'              => $score,
            'selectedAnswerText' => $selectedAnswerText,
            'correctAnswerText'  => $correctAnswerText,
            'explanation'        => $explanation,
            'hasNextQuestion'    => true,
            'nextUrl'            => route('mahasiswa.materials.questions.levels', [
                'material'   => $data['material_id'],
                'difficulty' => $data['difficulty'] ?? 'beginner',
            ]),
            'adaptiveResult' => [
                'facts'            => [],
                'triggered_rule'   => null,
                'global_xp_earned' => $xpEarned,
                'new_state'        => $guestStateData,
            ],
        ];
    }

    protected function getSelectedAnswerText(Question $question, array $data, ?string $default): string
    {
        return match ($question->question_type) {
            'fill_in_the_blank' => $data['fill_in_the_blank_answer'] ?? '',
            'drag_and_drop'     => 'Drag & Drop Answer',
            default             => $this->answerRepo->find($data['answer'])?->answer_text ?? $default ?? 'N/A',
        };
    }

    protected function hasValidAnswer(Question $question, array $data): bool
    {
        if ($question->question_type === 'fill_in_the_blank') {
            return true;
        }

        if ($question->question_type === 'drag_and_drop') {
            return true;
        }

        return isset($data['answer']);
    }

    /** @return array{0: int, 1: int, 2: array|null} */
    protected function processGuestAnswer(array $data, Question $question, bool $isCorrect): array
    {
        $difficulty    = $data['difficulty'] ?? 'beginner';
        $baseScore     = $isCorrect ? 80 : 0;
        $guestState    = $this->guestProgressService->getGamificationState();
        $currentXp     = $guestState['xp'];
        $currentStreak = $guestState['streak'];
        $xpEarned      = 0;

        if ($isCorrect) {
            $guestStateForCalc = ['global_xp' => $currentXp];
            $rewardData        = $this->gamificationService->calculateCorrectAnswerReward(
                $guestStateForCalc,
                false,
                $difficulty,
                (int) ($data['time_spent'] ?? 0),
            );
            $baseXp = $rewardData['global_xp_earned'];

            $currentStreak++;
            $streakBonus = $this->gamificationService->calculateStreakBonusXP($currentStreak);

            $xpEarned   = $baseXp + $streakBonus;
            $currentXp += $xpEarned;
        } else {
            $currentStreak = 0;
        }

        $this->guestProgressService->saveGamificationState($currentXp, $currentStreak);
        $this->guestProgressService->saveProgress($data, $isCorrect, $question->id);

        $guestStateData = [
            'gamification' => [
                'global_xp'      => $currentXp,
                'current_streak' => $currentStreak,
                'current_level'  => 'Tamu',
                'badges'         => [],
            ],
            'performance' => [
                'hints_available'          => 3,
                'total_questions_answered' => count($this->guestProgressService->getProgress()),
            ],
            'adaptive_state' => [
                'last_action' => 'NEXT_QUESTION',
                'message'     => $isCorrect ? 'Benar!' : 'Salah, tetap semangat!',
            ],
        ];

        return [$baseScore, $xpEarned, $guestStateData];
    }

    protected function saveAuthenticatedProgress(array $data, string $userId, bool $isCorrect, Question $question): void
    {
        $attemptsCount = $this->progressRepo->getAttemptCount(
            $userId,
            $data['material_id'],
            $question->id,
        );

        $attemptNumber = $attemptsCount > 0 ? $attemptsCount + 1 : 1;

        // Handle answer_id vs user_response based on question type
        $answerId     = null;
        $userResponse = null;

        if ($question->question_type === 'multiple_choice' || $question->question_type === 'radio_button') {
            $answerId = $data['answer'] ?? null;
        } elseif ($question->question_type === 'fill_in_the_blank') {
            $userResponse = $data['fill_in_the_blank_answer'] ?? null;
        } elseif ($question->question_type === 'drag_and_drop') {
            $userResponse = $data['drag_and_drop_answers'] ?? null;
        }

        $this->progressRepo->saveProgress([
            'user_id'        => $userId,
            'material_id'    => $data['material_id'],
            'question_id'    => $question->id,
            'answer_id'      => $answerId,
            'user_response'  => $userResponse,
            'is_correct'     => $isCorrect,
            'is_answered'    => true,
            'attempt_number' => $attemptNumber,
        ]);

        Log::info("User {$userId} answered question {$question->id}, attempt {$attemptNumber}, " .
            "difficulty: {$question->difficulty}, result: " . ($isCorrect ? 'CORRECT' : 'INCORRECT'));
    }

    /** @return array<string, mixed> */
    public function checkAllAnswers(array $data, string $userId): array
    {
        $materialId = $data['material_id'];
        $answers    = $data['answers'];

        $totalQuestions = count($answers);
        $correctAnswers = 0;
        $results        = [];

        foreach ($answers as $questionId => $answerId) {
            $question       = $this->questionRepo->findWithAnswers($questionId);
            $selectedAnswer = $question->answers->find($answerId);

            $isCorrect     = $selectedAnswer && $selectedAnswer->is_correct;
            $correctAnswer = $question->answers->where('is_correct', true)->first();

            $attemptNumber = $this->progressRepo->getAttemptCount($userId, $materialId, $questionId) + 1;

            $this->progressRepo->updateOrCreateProgress(
                [
                    'user_id'     => $userId,
                    'material_id' => $materialId,
                    'question_id' => $questionId,
                ],
                [
                    'is_correct'     => $isCorrect,
                    'is_answered'    => true,
                    'attempt_number' => $attemptNumber,
                ],
            );

            if ($isCorrect) {
                $correctAnswers++;
            }

            $results[$questionId] = [
                'is_correct'           => $isCorrect,
                'question_text'        => $question->question_text,
                'selected_answer'      => $selectedAnswer ? $selectedAnswer->answer_text : null,
                'correct_answer'       => $isCorrect ? null : ($correctAnswer->answer_text ?? null),
                'explanation'          => $correctAnswer->explanation ?? null,
                'selected_explanation' => $selectedAnswer ? $selectedAnswer->explanation : null,
            ];
        }

        $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;

        return [
            'status'         => 'success',
            'message'        => "Anda menjawab benar $correctAnswers dari $totalQuestions soal (Skor: $score%)",
            'score'          => $score,
            'results'        => $results,
            'correctAnswers' => $correctAnswers,
            'totalQuestions' => $totalQuestions,
            'nextUrl'        => route('mahasiswa.dashboard'),
        ];
    }

    /**
     * Determine if the provided answer data is correct for the given question.
     */
    public function determineCorrectness(Question $question, array $data): bool
    {
        if ($question->question_type === 'multiple_choice' || $question->question_type === 'radio_button') {
            if (! isset($data['answer'])) {
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
