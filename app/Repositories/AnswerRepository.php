<?php

namespace App\Repositories;

use App\Contracts\Repositories\AnswerRepositoryInterface;
use App\Models\Answer;

class AnswerRepository implements AnswerRepositoryInterface
{
    /**
     * Get all answers
     */
    public function all()
    {
        return Answer::all();
    }

    /**
     * Find answer by ID
     */
    public function find($id)
    {
        return Answer::find($id);
    }

    /**
     * Find answer by ID or fail
     */
    public function findOrFail($id)
    {
        return Answer::findOrFail($id);
    }

    /**
     * Create new answer
     */
    public function create(array $data)
    {
        return Answer::create($data);
    }

    /**
     * Update answer
     */
    public function update($id, array $data)
    {
        $answer = $this->findOrFail($id);
        $answer->update($data);
        return $answer;
    }

    /**
     * Delete answer
     */
    public function delete($id)
    {
        $answer = $this->findOrFail($id);
        return $answer->delete();
    }

    /**
     * Get all answers for a question
     */
    public function getByQuestionId($questionId)
    {
        return Answer::where('question_id', $questionId)->get();
    }

    /**
     * Get correct answers for a question (multiple correct answers possible)
     */
    public function getCorrectAnswers($questionId)
    {
        return Answer::where('question_id', $questionId)
            ->where('is_correct', true)
            ->get();
    }

    /**
     * Delete all answers for a question
     */
    public function deleteByQuestionId($questionId)
    {
        return Answer::where('question_id', $questionId)->delete();
    }
}
