<?php

namespace App\Repositories\Interfaces;

interface AnswerRepositoryInterface extends BaseRepositoryInterface
{
    public function getCorrectAnswer($questionId);
}
