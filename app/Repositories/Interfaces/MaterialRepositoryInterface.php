<?php

namespace App\Repositories\Interfaces;

interface MaterialRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllWithQuestions();
    public function getAllWithQuestionsAndConfigs();
    public function findBySlug($slug);
    public function getAllOrdered();
    public function findWithQuestionsShuffled($id);
}
