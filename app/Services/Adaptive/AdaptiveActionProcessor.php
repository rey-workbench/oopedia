<?php

declare(strict_types=1);

namespace App\Services\Adaptive;

use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Contracts\Services\AdaptiveActionProcessorInterface;
use App\Enums\Adaptive\AdaptiveActionId;
use App\Models\Question;
use App\Models\StudentState;

final readonly class AdaptiveActionProcessor implements AdaptiveActionProcessorInterface
{
    private const array DIFFICULTY_ORDER = ['beginner', 'medium', 'hard', 'final'];

    public function __construct(
        private QuestionRepositoryInterface $questionRepository,
    ) {
    }

    public function process(StudentState $studentState, array $actions, string $materialId, bool $isCorrect): StudentState
    {
        $adaptiveState = $studentState->adaptive_state ?? [];

        // Reset transient flags before applying new actions
        $adaptiveState['show_guidance']       = false;
        $adaptiveState['needs_remedial']      = false;
        $adaptiveState['next_url']            = null;
        $adaptiveState['challenge_question']  = null;

        if ($isCorrect) {
            $adaptiveState['guidance_count'] = 0;
        }

        foreach ($actions as $action) {
            // Recommendation is now just an ID string or an object with an ID
            $actionIdString = is_array($action) ? $action['id'] : $action;

            $actionId = AdaptiveActionId::tryFrom($actionIdString);
            if (!$actionId) {
                continue;
            }

            match ($actionId) {
                AdaptiveActionId::REMEDIAL => $this->handleRemedial($studentState, $adaptiveState, $materialId),
                AdaptiveActionId::REMEDIAL_INTENSIVE => $this->handleRemedialIntensive($studentState, $adaptiveState, $materialId),
                AdaptiveActionId::REDUCE_DIFF => $this->handleReduceDifficulty($studentState, $adaptiveState, $materialId),
                AdaptiveActionId::INCREASE_DIFF => $this->handleIncreaseDifficulty($studentState, 1),
                AdaptiveActionId::REDUCE_HINT => $this->handleReduceHint($studentState, $adaptiveState),
                AdaptiveActionId::NEW_CHALLENGE => $this->handleNewChallenge($studentState, $adaptiveState, $materialId, $isCorrect),
                AdaptiveActionId::STREAK_BONUS => $this->handleStreakBonus($studentState, $adaptiveState, $isCorrect),
                AdaptiveActionId::CERTIFICATION => $this->handleCertification($studentState, $adaptiveState, $materialId),
                AdaptiveActionId::SHOW_GUIDANCE => $this->handleShowGuidance($studentState, $adaptiveState, $materialId),
                AdaptiveActionId::NOTIFY_TEACHER => $this->handleNotifyTeacher($adaptiveState),
                AdaptiveActionId::GIVE_HINT => $this->handleGiveHint($studentState),
                AdaptiveActionId::FEEDBACK => null,
            };
        }

        $studentState->adaptive_state = $adaptiveState;

        return $studentState;
    }

    private function handleRemedial(StudentState $studentState, array &$adaptiveState, string $materialId, ?string $customMessage = null): void
    {
        $adaptiveState['needs_remedial']       = true;
        $adaptiveState['remedial_material_id'] = $materialId;
        $studentState->target_difficulty       = 'beginner';
        $adaptiveState['next_url']             = route('mahasiswa.materials.show', ['material' => $materialId]);

        if ($customMessage) {
            $adaptiveState['remedial_message'] = $customMessage;
        }
    }

    private function handleRemedialIntensive(StudentState $studentState, array &$adaptiveState, string $materialId): void
    {
        $this->handleRemedial($studentState, $adaptiveState, $materialId);
        $adaptiveState['forced_easy_count'] = 5;
    }

    private function handleReduceDifficulty(StudentState $studentState, array &$adaptiveState, string $materialId): void
    {
        $currentDiff = $studentState->target_difficulty;

        if (!$currentDiff && isset($studentState->current_session['question_ids'])) {
            $questionIds = $studentState->current_session['question_ids'];
            $lastQuestionId = end($questionIds);
            if ($lastQuestionId) {
                $lastQuestion = Question::find($lastQuestionId);
                $currentDiff = $lastQuestion?->difficulty?->value;
            }
        }

        $currentDiff ??= 'beginner';

        $currentIndex = array_search($currentDiff, self::DIFFICULTY_ORDER, true);

        if ($currentIndex > 0) {
            // Cek apakah ada soal dengan tingkat kesulitan di bawahnya yang belum dijawab benar
            $lowerDifficulties = array_slice(self::DIFFICULTY_ORDER, 0, $currentIndex);

            $availableLowerQuestions = Question::where('material_id', $materialId)
                ->whereIn('difficulty', $lowerDifficulties)
                ->whereNotIn('id', function ($query) use ($studentState): void {
                    $query->select('question_id')
                          ->from('quiz_attempts')
                          ->where('user_id', $studentState->user_id)
                          ->where('is_correct', true);
                })->exists();

            if (!$availableLowerQuestions) {
                // Stok soal mudah sudah habis. Pemicu mentok, paksa Remedial (V01)
                $this->handleRemedial($studentState, $adaptiveState, $materialId);
                return;
            }

            $studentState->target_difficulty = self::DIFFICULTY_ORDER[$currentIndex - 1];
        } else {
            // Jika sudah di level Beginner dan disuruh turun, berarti mentok. Paksa Remedial.
            $this->handleRemedial($studentState, $adaptiveState, $materialId);
        }
    }

    private function handleIncreaseDifficulty(StudentState $studentState, int $steps): void
    {
        $currentDiff = $studentState->target_difficulty;

        if (!$currentDiff && isset($studentState->current_session['question_ids'])) {
            $questionIds = $studentState->current_session['question_ids'];
            $lastQuestionId = end($questionIds);
            if ($lastQuestionId) {
                $lastQuestion = Question::find($lastQuestionId);
                $currentDiff = $lastQuestion?->difficulty?->value;
            }
        }

        $currentDiff ??= 'beginner';

        $currentIndex = array_search($currentDiff, self::DIFFICULTY_ORDER, true);

        $newIndex = min(2, $currentIndex + $steps);
        $studentState->target_difficulty = self::DIFFICULTY_ORDER[$newIndex];
    }

    private function handleReduceHint(StudentState $studentState, array &$adaptiveState): void
    {
        $currentMax = $adaptiveState['max_hints_per_session'] ?? 3;
        $adaptiveState['max_hints_per_session'] = max(0, $currentMax - 1);
        $studentState->hints_available = min($studentState->hints_available, $adaptiveState['max_hints_per_session']);
        $adaptiveState['scaffold_mode'] = 'minimal';
    }

    private function handleNewChallenge(StudentState $studentState, array &$adaptiveState, string $materialId, bool $isCorrect): void
    {
        if ($isCorrect) {
            $studentState->xp += 100;
            $studentState->hints_available = ($studentState->hints_available ?? 0) + 1;
        }

        $question = $this->questionRepository->getRandomMultipleChoiceFromOtherMaterials($materialId);

        if ($question instanceof Question) {
            $adaptiveState['challenge_question'] = [
                'id' => $question->id,
                'content' => $question->question_text,
                'type' => $question->question_type->value,
                'options' => $question->answers->map(fn($a): array => [
                    'id' => $a->id,
                    'text' => $a->answer_text,
                    'is_correct' => $a->is_correct,
                ])->toArray(),
            ];
        }
    }

    private function handleStreakBonus(StudentState $studentState, array &$adaptiveState, bool $isCorrect): void
    {
        if (!$isCorrect) {
            return;
        }

        $studentState->xp += 50;
    }

    private function handleCertification(StudentState $studentState, array &$adaptiveState, string $materialId): void
    {
        $certs = $studentState->certifications ?? [];
        $certs[$materialId] = 'gold'; // Award gold certificate for completing the material
        $studentState->certifications = $certs;
        
        $adaptiveState['unlock_advanced'] = true;
        $this->handleNotifyTeacher($adaptiveState);
    }

    private function handleShowGuidance(StudentState $studentState, array &$adaptiveState, string $materialId): void
    {
        $count = ($adaptiveState['guidance_count'] ?? 0) + 1;
        $adaptiveState['guidance_count'] = $count;

        if ($count > 3) {
            $this->handleRemedial(
                $studentState,
                $adaptiveState,
                $materialId,
                'Kamu terlalu banyak menebak, baca materi dulu.'
            );
            $adaptiveState['guidance_count'] = 0; // Reset after forced remedial
            return;
        }

        $adaptiveState['show_guidance'] = true;
    }

    private function handleNotifyTeacher(array &$adaptiveState): void
    {
        $adaptiveState['notify_teacher'] = true;
        $adaptiveState['notify_teacher_type'] = 'general';
    }

    private function handleGiveHint(StudentState $studentState): void
    {
        $studentState->hints_available = ($studentState->hints_available ?? 0) + 1;
    }
}
