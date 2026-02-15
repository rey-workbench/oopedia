<?php

namespace App\Services\Lms\Question;

use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Contracts\Repositories\AnswerRepositoryInterface;
use App\Contracts\Services\QuestionServiceInterface;
use App\Models\Question;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class QuestionService implements QuestionServiceInterface
{
    protected $questionRepo;
    protected $answerRepo;

    public function __construct(
        QuestionRepositoryInterface $questionRepo,
        AnswerRepositoryInterface $answerRepo
    )
    {
        $this->questionRepo = $questionRepo;
        $this->answerRepo = $answerRepo;
    }

    public function getFilteredQuestions($search = null, $difficulty = null, $materialId = null)
    {
        $questions = $this->questionRepo->getFilteredQuestions($search, $difficulty, $materialId);

        $questions->getCollection()->transform(function ($question) {
            $question->formatted_type = match($question->question_type) {
                'fill_in_the_blank' => 'Fill in the Blank',
                'radio_button' => 'Radio Button',
                'drag_and_drop' => 'Drag and Drop',
                default => $question->question_type
            };
            return $question;
        });

        return $questions;
    }

    public function getAvailableQuestionsForBank($materialId, array $excludeIds, $search = null, $difficulty = null)
    {
        return $this->questionRepo->getQuestionsForBank($materialId, $excludeIds, $search, $difficulty);
    }

    public function getQuestionById($id)
    {
        return $this->questionRepo->find($id);
    }

    public function getQuestionWithAnswers($id)
    {
        return $this->questionRepo->findWithAnswers($id);
    }

    public function existsByMaterialAndDifficulty($materialId, $difficulty)
    {
        return $this->questionRepo->existsByMaterialAndDifficulty($materialId, $difficulty);
    }

    public function createQuestion(array $data)
    {
        return DB::transaction(function () use ($data) {
            $question = $this->questionRepo->create([
                'question_text' => $data['question_text'],
                'question_type' => $data['question_type'],
                'difficulty' => $data['difficulty'],
                'material_id' => $data['material_id'],
                'sub_material_id' => $data['sub_material_id'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $this->createAnswers($question->id, $data['answers']);

            return $question;
        });
    }

    public function updateQuestion($questionId, array $data)
    {
        $question = $this->questionRepo->find($questionId);
        if (!$question) {
            throw new Exception("Question not found");
        }

        return DB::transaction(function () use ($question, $data) {
            $this->questionRepo->update($question->id, [
                'question_text' => $data['question_text'],
                'question_type' => $data['question_type'],
                'difficulty' => $data['difficulty'],
                'material_id' => $data['material_id'],
                'sub_material_id' => $data['sub_material_id'] ?? null,
            ]);

            // Delete existing answers via repository
            $this->answerRepo->deleteByQuestionId($question->id);

            // Create new answers
            $this->createAnswers($question->id, $data['answers']);

            return $question->fresh();
        });
    }

    public function deleteQuestion($questionId)
    {
        $question = $this->questionRepo->find($questionId);
        if (!$question) {
            throw new Exception("Question not found");
        }

        return DB::transaction(function () use ($question) {
            $this->answerRepo->deleteByQuestionId($question->id);
            $this->questionRepo->delete($question->id);
            return true;
        });
    }

    protected function createAnswers($questionId, array $answersData)
    {
        foreach ($answersData as $answer) {
            $this->answerRepo->create([
                'question_id' => $questionId,
                'answer_text' => $answer['answer_text'],
                'is_correct' => $answer['is_correct'] ?? 0,
                'explanation' => $answer['explanation'] ?? null,
                'drag_source' => $answer['drag_source'] ?? null,
                'drag_target' => $answer['drag_target'] ?? null,
                'blank_position' => $answer['blank_position'] ?? null
            ]);
        }
    }
}
