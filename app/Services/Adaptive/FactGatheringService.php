<?php

namespace App\Services\Adaptive;

use App\Models\StudentState;
use App\Repositories\ProgressRepository;
use App\Repositories\QuestionRepository;

/**
 * FactGatheringService
 * 
 * Responsible for gathering facts (G01-G25) from student state and context.
 * Facts are used by adaptive rules for decision making.
 */
class FactGatheringService
{
    public function __construct(
        protected ProgressRepository $progressRepo,
        protected QuestionRepository $questionRepo
    ) {}
    
    /**
     * Gather all facts (G01-G25) from student state and context.
     */
    public function gatherFacts(
        StudentState $studentState,
        bool $isCorrect,
        bool $usedHint,
        int $score,
        int $timeSpent,
        string $difficulty,
        int $questionId,
        int $materialId,
        ?int $moduleId = null
    ): array {
        $facts = [];
        
        // G01-G04: Score Facts
        $facts = array_merge($facts, $this->getScoreFacts($score, $isCorrect));
        
        // G05-G06: Time Facts
        $facts = array_merge($facts, $this->getTimeFacts($timeSpent));
        
        // G07-G08: Learning Style Facts
        $facts = array_merge($facts, $this->getLearningStyleFacts($studentState));
        
        // G09-G10: Error Type Facts
        $facts = array_merge($facts, $this->getErrorTypeFacts($studentState, $questionId, $isCorrect));
        
        // G11-G12: Hint Facts
        $facts[] = $usedHint ? 'G12' : 'G11';
        
        // G13-G25: Module Facts
        if ($moduleId) {
            $facts[] = $this->getModuleFact($moduleId);
        }
        
        // G15-G17: Difficulty Facts
        $facts[] = $this->getDifficultyFact($difficulty);
        
        // G18: Final Project (check difficulty='final')
        if ($difficulty === 'final') {
            $facts[] = 'G18';
        }
        
        // G19-G21: Unlock Status Facts
        $facts = array_merge($facts, $this->getUnlockStatusFacts($studentState, $materialId));
        
        // G22: Persistent Fail
        if ($this->isPersistentFail($studentState->user_id, $questionId)) {
            $facts[] = 'G22';
        }
        
        return array_unique($facts);
    }
    
    /**
     * Get score-based facts (G01-G04).
     */
    protected function getScoreFacts(int $score, bool $isCorrect): array
    {
        // Normalize score: correct answers get at least 70, wrong get max 69
        $finalScore = $isCorrect ? max($score, 70) : min($score, 69);
        
        if ($finalScore < 40) return ['G01']; // Critical
        if ($finalScore < 70) return ['G02']; // Remedial
        if ($finalScore < 90) return ['G03']; // Standard
        return ['G04']; // Mastery
    }
    
    /**
     * Get time-based facts (G05-G06).
     */
    protected function getTimeFacts(int $timeSpent): array
    {
        // Default allocated time: 60 seconds
        // TODO: Get from question metadata if available
        $allocatedTime = 60;
        $percentage = ($timeSpent / $allocatedTime) * 100;
        
        return $percentage < 50 ? ['G05'] : ['G06']; // Fast : Normal
    }
    
    /**
     * Get learning style facts (G07-G08).
     */
    protected function getLearningStyleFacts(StudentState $state): array
    {
        $style = $state->learning_style ?? 'visual';
        return $style === 'visual' ? ['G07'] : ['G08'];
    }
    
    /**
     * Get error type facts (G09-G10).
     * Uses question_type from database.
     */
    protected function getErrorTypeFacts(StudentState $state, int $questionId, bool $isCorrect): array
    {
        if ($isCorrect) {
            return [];
        }
        
        // Get question type from database
        $question = $this->questionRepo->find($questionId);
        $questionType = $question?->type ?? 'teori';
        
        // Syntax questions → G09, Theory/Logic questions → G10
        return $questionType === 'sintaks' ? ['G09'] : ['G10'];
    }
    
    /**
     * Get module fact (G13-G25).
     */
    protected function getModuleFact(int $moduleId): string
    {
        $moduleMap = [
            1 => 'G13', // Foundation
            2 => 'G14', // Encapsulation
            3 => 'G23', // Inheritance
            4 => 'G24', // Polymorphism
            5 => 'G25', // Abstraction
        ];
        
        return $moduleMap[$moduleId] ?? 'G13';
    }
    
    /**
     * Get difficulty fact (G15-G17).
     */
    protected function getDifficultyFact(string $difficulty): string
    {
        $difficultyMap = [
            'beginner' => 'G15',
            'medium' => 'G16',
            'hard' => 'G17',
        ];
        
        return $difficultyMap[$difficulty] ?? 'G15';
    }
    
    /**
     * Get unlock status facts (G19-G21).
     */
    protected function getUnlockStatusFacts(StudentState $state, int $materialId): array
    {
        $facts = [];
        
        // Check if next material is locked
        $unlockedModules = $state->unlocked_modules ?? [];
        $nextMaterialId = $materialId + 1;
        
        if (!in_array($nextMaterialId, $unlockedModules)) {
            $facts[] = 'G19'; // Next Locked
        } else {
            $facts[] = 'G20'; // Next Unlocked
        }
        
        // Check if previous material is unlocked
        $prevMaterialId = $materialId - 1;
        if ($prevMaterialId > 0 && in_array($prevMaterialId, $unlockedModules)) {
            $facts[] = 'G21'; // Prev Unlocked
        }
        
        return $facts;
    }
    
    /**
     * Check if student has persistent failures (G22).
     */
    protected function isPersistentFail(int $userId, int $questionId): bool
    {
        $consecutiveFails = $this->progressRepo->getConsecutiveFailures($userId, $questionId);
        return $consecutiveFails >= 3;
    }
}
