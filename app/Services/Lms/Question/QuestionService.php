<?php

namespace App\Services\Lms\Question;

use App\Contracts\Repositories\AnswerRepositoryInterface;
use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Contracts\Services\QuestionServiceInterface;
use App\Exceptions\Domain\QuestionNotFoundException;
use App\Models\Question;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuestionService implements QuestionServiceInterface
{
    public function __construct(
        protected QuestionRepositoryInterface $questionRepo,
        protected AnswerRepositoryInterface $answerRepo,
    ) {}

    public function getFilteredQuestions(
        ?string $search = null,
        ?string $difficulty = null,
        ?int $materialId = null,
    ): LengthAwarePaginator {
        $questions = $this->questionRepo->getFilteredQuestions($search, $difficulty, $materialId);

        return $questions->through(function ($question) {
            $question->formatted_type = match ($question->question_type) {
                'fill_in_the_blank' => 'Fill in the Blank',
                'radio_button'      => 'Radio Button',
                'drag_and_drop'     => 'Drag and Drop',
                default             => $question->question_type,
            };

            return $question;
        });
    }

    public function getAvailableQuestionsForBank(
        int $materialId,
        array $excludeIds,
        ?string $search = null,
        ?string $difficulty = null,
    ): LengthAwarePaginator {
        return $this->questionRepo->getQuestionsForBank($materialId, $excludeIds, $search, $difficulty);
    }

    public function getQuestionById(int $id): ?Question
    {
        return $this->questionRepo->find($id);
    }

    public function getQuestionWithAnswers(int $id): ?Question
    {
        return $this->questionRepo->findWithAnswers($id);
    }

    public function existsByMaterialAndDifficulty(int $materialId, string $difficulty): bool
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

    public function updateQuestion(int $questionId, array $data): Question
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

    public function deleteQuestion(int $questionId): void
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

    protected function createAnswers(int $questionId, array $answersData): void
    {
        foreach ($answersData as $answer) {
            $this->answerRepo->create([
                'question_id'    => $questionId,
                'answer_text'    => $answer['answer_text'],
                'is_correct'     => $answer['is_correct']     ?? 0,
                'explanation'    => $answer['explanation']    ?? null,
                'drag_source'    => $answer['drag_source']    ?? null,
                'drag_target'    => $answer['drag_target']    ?? null,
                'blank_position' => $answer['blank_position'] ?? null,
            ]);
        }
    }
}
