<?php

declare(strict_types=1);

namespace Tests\Feature\Unit\Services\Lms;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Enums\Lms\QuestionDifficulty;
use App\Enums\Lms\QuestionType;
use App\Models\Material;
use App\Models\Question;
use App\Services\Lms\QuestionListingService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Tests\TestCase;

final class QuestionListingServiceTest extends TestCase
{
    public function test_it_falls_back_to_remaining_questions_when_target_difficulty_hides_all_unanswered_items(): void
    {
        $material     = new Material;
        $material->id = 'material-1';

        $questions = new EloquentCollection([
            $this->makeQuestion('q1', QuestionDifficulty::HARD),
            $this->makeQuestion('q2', QuestionDifficulty::HARD),
            $this->makeQuestion('q3', QuestionDifficulty::HARD),
            $this->makeQuestion('q4', QuestionDifficulty::HARD),
            $this->makeQuestion('q5', QuestionDifficulty::HARD),
            $this->makeQuestion('q6', QuestionDifficulty::MEDIUM),
            $this->makeQuestion('q7', QuestionDifficulty::MEDIUM),
        ]);

        $answeredQuestionIds = collect(['q1', 'q2', 'q3', 'q4', 'q5']);

        $materialRepo = $this->createMock(MaterialRepositoryInterface::class);

        $progressRepo = $this->createMock(ProgressRepositoryInterface::class);
        $progressRepo->expects($this->once())
            ->method('getAnsweredQuestionIds')
            ->with('user-1', 'material-1')
            ->willReturn($answeredQuestionIds);
        $progressRepo->expects($this->once())
            ->method('getAttemptedQuestionIds')
            ->with('user-1', 'material-1')
            ->willReturn($answeredQuestionIds);

        $questionRepo = $this->createMock(QuestionRepositoryInterface::class);
        $questionRepo->expects($this->once())
            ->method('getByMaterialAndDifficulty')
            ->with('material-1', 'all', null)
            ->willReturn($questions);
        $questionRepo->expects($this->once())
            ->method('countByMaterial')
            ->with('material-1')
            ->willReturn(7);

        $service = new QuestionListingService($materialRepo, $progressRepo, $questionRepo);

        $result = $service->getQuizData(
            material: $material,
            difficulty: null,
            userId: 'user-1',
            isGuest: false,
            guestProgress: [],
            subMaterialId: null,
            targetDifficulty: QuestionDifficulty::HARD,
        );

        $this->assertNotNull($result['currentQuestion']);
        $this->assertContains($result['currentQuestion']->id, ['q6', 'q7']);
        $this->assertSame(QuestionDifficulty::MEDIUM, $result['currentQuestion']->difficulty);

        $this->assertSame(7, $result['totalQuestions']);
        $this->assertSame(5, $result['answeredCount']);
        $this->assertSame(6, $result['currentQuestionNumber']);
    }

    private function makeQuestion(string $id, QuestionDifficulty $difficulty): Question
    {
        $question                = new Question;
        $question->id            = $id;
        $question->material_id   = 'material-1';
        $question->difficulty    = $difficulty;
        $question->question_type = QuestionType::RADIO_BUTTON;
        $question->setRelation('answers', new EloquentCollection);

        return $question;
    }
}
