<?php

namespace App\Services\Lms;

use App\Contracts\Repositories\AnswerRepositoryInterface;
use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Contracts\Services\QuestionServiceInterface;
use App\Exceptions\Domain\QuestionNotFoundException;
use App\Models\Question;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class QuestionService implements QuestionServiceInterface
{
    public function __construct(
        public readonly QuestionRepositoryInterface $questionRepo,
        public readonly AnswerRepositoryInterface $answerRepo,
    ) {
    }

    public function getFilteredQuestions(
        ?string $search = null,
        ?string $difficulty = null,
        ?string $materialId = null,
    ): LengthAwarePaginator {
        $questions = $this->questionRepo->getFilteredQuestions($search, $difficulty, $materialId);

        return $questions->through(function ($question) {
            $question->formatted_type = match ($question->question_type) {
                Question::QUESTION_TYPE_FILL_IN_THE_BLANK => 'Fill in the Blank',
                Question::QUESTION_TYPE_RADIO_BUTTON      => 'Radio Button',
                Question::QUESTION_TYPE_DRAG_AND_DROP     => 'Drag and Drop',
                default                                   => $question->question_type,
            };

            return $question;
        });
    }

    public function getAvailableQuestionsForBank(
        string $materialId,
        array $excludeIds,
        ?string $search = null,
        ?string $difficulty = null,
    ): LengthAwarePaginator {
        return $this->questionRepo->getQuestionsForBank($materialId, $excludeIds, $search, $difficulty);
    }

    public function getQuestionById(string $id): ?Question
    {
        return $this->questionRepo->find($id);
    }

    public function getQuestionWithAnswers(string $id): ?Question
    {
        return $this->questionRepo->findWithAnswers($id);
    }

    public function existsByMaterialAndDifficulty(string $materialId, string $difficulty): bool
    {
        return $this->questionRepo->existsByMaterialAndDifficulty($materialId, $difficulty);
    }

    public function createQuestion(array $data): Question
    {
        return DB::transaction(function () use ($data) {
            $question = $this->questionRepo->create([
                'question_text'   => $data['question_text'],
                'question_type'   => $data['question_type'],
                'difficulty'      => $data['difficulty'],
                'material_id'     => $data['material_id'],
                'sub_material_id' => $data['sub_material_id'] ?? null,
                'created_by'      => Auth::id(),
            ]);

            $this->createAnswers($question->id, $data['answers']);

            return $question;
        });
    }

    public function updateQuestion(string $questionId, array $data): Question
    {
        $question = $this->questionRepo->find($questionId);

        if (! $question) {
            throw new QuestionNotFoundException($questionId);
        }

        return DB::transaction(function () use ($question, $data) {
            $this->questionRepo->update($question->id, [
                'question_text'   => $data['question_text'],
                'question_type'   => $data['question_type'],
                'difficulty'      => $data['difficulty'],
                'material_id'     => $data['material_id'],
                'sub_material_id' => $data['sub_material_id'] ?? null,
                'updated_by'      => Auth::id(),
            ]);

            $this->answerRepo->deleteByQuestionId($question->id);
            $this->createAnswers($question->id, $data['answers']);

            return $question->fresh();
        });
    }

    public function deleteQuestion(string $questionId): void
    {
        $question = $this->questionRepo->find($questionId);

        if (! $question) {
            throw new QuestionNotFoundException($questionId);
        }

        DB::transaction(function () use ($question) {
            $this->answerRepo->deleteByQuestionId($question->id);
            $this->questionRepo->delete($question->id);
        });
    }

    protected function createAnswers(string $questionId, array $answersData): void
    {
        foreach ($answersData as $answer) {
            $this->answerRepo->create([
                'question_id'    => $questionId,
                'answer_text'    => $answer['answer_text']        ?? null,
                'is_correct'     => $answer['is_correct']         ?? 0,
                'explanation'    => $answer['explanation']        ?? null,
                'drag_source'    => $answer['drag_source']        ?? null,
                'drag_target'    => $answer['drag_target']        ?? null,
                'blank_position' => $answer['blank_position']     ?? null,
            ]);
        }
    }
}
