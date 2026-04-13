<?php

namespace Tests\Feature\Unit\Services\Adaptive;

use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Enums\Lms\QuestionDifficulty;
use App\Models\StudentState;
use App\Rules\Adaptive\Constants\AdaptiveConstants;
use App\Services\Adaptive\FactGatheringService;
use Tests\TestCase;

class FactGatheringServiceTest extends TestCase
{
    public function test_it_adds_satisfactory_progress_fact_when_attempted_percentage_is_above_sixty_percent(): void
    {
        $progressRepository = $this->createMock(ProgressRepositoryInterface::class);
        $questionRepository = $this->createMock(QuestionRepositoryInterface::class);

        $progressRepository->method('getConsecutiveFailures')->willReturn(0);
        $progressRepository->method('getAttemptedQuestionIds')->willReturn(collect(['q1', 'q2', 'q3', 'q4', 'q5', 'q6', 'q7']));
        $questionRepository->method('countByMaterial')->willReturn(10);

        $service = new FactGatheringService($progressRepository, $questionRepository);
        $facts   = $service->gatherFacts(
            studentState: $this->makeStudentState('user-1'),
            isCorrect: true,
            usedHint: false,
            score: 95,
            timeSpent: 20,
            difficulty: QuestionDifficulty::MEDIUM,
            questionId: 'question-1',
            materialId: 'material-1',
            moduleId: 'module-1',
        );

        $this->assertContains(AdaptiveConstants::FACT_SATISFACTORY_PROGRESS, $facts);
    }

    public function test_it_does_not_add_satisfactory_progress_fact_when_attempted_percentage_is_not_above_sixty_percent(): void
    {
        $progressRepository = $this->createMock(ProgressRepositoryInterface::class);
        $questionRepository = $this->createMock(QuestionRepositoryInterface::class);

        $progressRepository->method('getConsecutiveFailures')->willReturn(0);
        $progressRepository->method('getAttemptedQuestionIds')->willReturn(collect(['q1', 'q2', 'q3', 'q4', 'q5', 'q6']));
        $questionRepository->method('countByMaterial')->willReturn(10);

        $service = new FactGatheringService($progressRepository, $questionRepository);
        $facts   = $service->gatherFacts(
            studentState: $this->makeStudentState('user-2'),
            isCorrect: true,
            usedHint: false,
            score: 95,
            timeSpent: 20,
            difficulty: QuestionDifficulty::MEDIUM,
            questionId: 'question-2',
            materialId: 'material-2',
            moduleId: 'module-2',
        );

        $this->assertNotContains(AdaptiveConstants::FACT_SATISFACTORY_PROGRESS, $facts);
    }

    private function makeStudentState(string $userId): StudentState
    {
        return new StudentState([
            'user_id'             => $userId,
            'gamification_data'   => [],
            'learning_profile'    => [],
            'performance_metrics' => [],
            'adaptive_state'      => [],
        ]);
    }
}
