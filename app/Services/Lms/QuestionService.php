<?php

declare(strict_types=1);

namespace App\Services\Lms;

use App\Contracts\Repositories\AnswerRepositoryInterface;
use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Contracts\Services\QuestionServiceInterface;
use App\DTOs\Question\QuestionCreateDTO;
use App\DTOs\Question\QuestionUpdateDTO;
use App\Enums\Lms\QuestionDifficulty;
use App\Exceptions\Domain\QuestionNotFoundException;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final readonly class QuestionService implements QuestionServiceInterface
{
    public function __construct(
        private QuestionRepositoryInterface $questionRepository,
        private AnswerRepositoryInterface $answerRepository,
    ) {}

    public function getFilteredQuestions(
        ?string $search = null,
        ?QuestionDifficulty $questionDifficulty = null,
        ?string $materialId = null,
    ): LengthAwarePaginator {
        $difficultyString = $questionDifficulty instanceof QuestionDifficulty ? $questionDifficulty->value : null;
        $questions        = $this->questionRepository->getFilteredQuestions($search, $difficultyString, $materialId);

        return $questions->through(fn ($question) => new QuestionResource($question)->resolve());
    }

    public function getQuestionById(string $id): ?array
    {
        $question = $this->questionRepository->find($id);

        return $question instanceof Question ? new QuestionResource($question)->resolve() : null;
    }

    public function getQuestionWithAnswers(string $id): ?array
    {
        $question = $this->questionRepository->findWithAnswers($id);

        return $question ? new QuestionResource($question)->resolve() : null;
    }

    public function createQuestion(QuestionCreateDTO $questionCreateDTO): Question
    {
        return DB::transaction(function () use ($questionCreateDTO): Question {
            $question = $this->questionRepository->create([
                'question_text' => $questionCreateDTO->question_text,
                'question_type' => $questionCreateDTO->question_type,
                'difficulty'    => $questionCreateDTO->difficulty,
                'material_id'   => $questionCreateDTO->material_id,
                'created_by'    => $questionCreateDTO->created_by,
            ]);

            $this->createAnswers($question->id, $questionCreateDTO->answers);

            return $question;
        });
    }

    public function updateQuestion(string $questionId, QuestionUpdateDTO $questionUpdateDTO): Question
    {
        $question = $this->questionRepository->find($questionId);
        if (! $question instanceof Question) {
            throw new QuestionNotFoundException($questionId);
        }

        return DB::transaction(function () use ($question, $questionUpdateDTO) {
            $question->update([
                'question_text' => $questionUpdateDTO->question_text,
                'question_type' => $questionUpdateDTO->question_type,
                'difficulty'    => $questionUpdateDTO->difficulty,
                'material_id'   => $questionUpdateDTO->material_id,
            ]);

            $this->answerRepository->deleteByQuestionId($question->id);
            $this->createAnswers($question->id, $questionUpdateDTO->answers);

            return $question->fresh(['answers']);
        });
    }

    public function deleteQuestion(string $questionId): void
    {
        $question = $this->questionRepository->find($questionId);
        if ($question instanceof Question) {
            DB::transaction(function () use ($question): void {
                $this->answerRepository->deleteByQuestionId($question->id);
                $this->questionRepository->delete($question->id);
            });
        }
    }

    private function createAnswers(string $questionId, array $answersData): void
    {
        foreach ($answersData as $answerData) {
            $this->answerRepository->create([
                'question_id'    => $questionId,
                'answer_text'    => $answerData['answer_text']        ?? null,
                'is_correct'     => $answerData['is_correct']         ?? 0,
                'explanation'    => $answerData['explanation']        ?? null,
                'drag_source'    => $answerData['drag_source']        ?? null,
                'drag_target'    => $answerData['drag_target']        ?? null,
                'blank_position' => $answerData['blank_position']     ?? null,
            ]);
        }
    }
}
