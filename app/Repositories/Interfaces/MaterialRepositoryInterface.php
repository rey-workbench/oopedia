<?php

namespace App\Repositories\Interfaces;

interface MaterialRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllWithQuestions();
    public function getAllWithQuestionsAndConfigs();
    public function findBySlug($slug);
    public function getAllOrdered();
    public function findWithQuestionsShuffled($id);
    public function findWithQuestionsAndAnswers($id);
    public function getMaterialsForAdmin($search = null, $sort = 'created_at', $direction = 'asc');
}
