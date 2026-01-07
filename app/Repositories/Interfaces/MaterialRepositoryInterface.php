<?php

namespace App\Repositories\Interfaces;

interface MaterialRepositoryInterface
{
    public function getAllWithQuestions();
    public function getAllWithQuestionsAndConfigs();
}
