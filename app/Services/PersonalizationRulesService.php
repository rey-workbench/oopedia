<?php

namespace App\Services;

use App\Models\StudentState;
use Illuminate\Support\Facades\Log;

/**
 * AdaptiveQuizRules (Refactored)
 * 
 * Implements the 11-Rule Adaptive System using Forward Chaining.
 * Based on Table 3.9 spec.
 */
class PersonalizationRulesService
{
    // ================== FACTS (G) ==================
    // Performance
    const G01_SCORE_CRITICAL = 'score_critical';   // Score < 40 (Severe Failure)
    const G02_SCORE_REMEDIAL = 'score_remedial';   // Score 40-69 (Needs Review)
    const G03_SCORE_STANDARD = 'score_standard';   // Score 70-89 (Passed)
    const G04_SCORE_MASTERY  = 'score_mastery';    // Score >= 90 (Excellent)
    
    // Time
    const G05_TIME_FAST      = 'time_fast';        // < 30s
    const G06_TIME_NORMAL    = 'time_normal';      // 30s - 120s
    const G07_TIME_SLOW      = 'time_slow';        // > 120s (Assumed G07 from context)

    // Style (From StudentState)
    const G08_STYLE_VISUAL   = 'style_visual';
    const G09_STYLE_TEXTUAL  = 'style_textual';
    const G10_WEAKNESS_SYNTAX= 'weakness_syntax'; 

    // Context / Navigation
    const G11_NO_HINT        = 'no_hint';
    const G12_HINT_USED      = 'hint_used';
    
    const G13_MODULE_1       = 'module_1';         // Context: Introduction/Module 1
    const G14_MODULE_FINAL   = 'module_final';     // Context: Final Project (G18 in table?)
    
    const G15_LEVEL_BEGINNER = 'level_beginner';   // Easy
    const G16_LEVEL_INTERMED = 'level_intermediate'; // Medium
    const G17_LEVEL_ADVANCED = 'level_advanced';   // Hard
    
    const G18_IS_PROJECT     = 'is_project';       // Final Project Question
    const G19_NEXT_LOCKED    = 'next_locked';
    const G21_PREV_UNLOCKED  = 'prev_unlocked';

    // ================== ACTIONS (H) ==================
    const H01_CRISIS_VISUAL    = 'intervention_visual';
    const H02_CRISIS_TEXTUAL   = 'intervention_textual';
    const H03_SYNTAX_RECOVERY  = 'syntax_recovery';
    const H04_STANDARD_PROMOTION = 'standard_promotion'; // H05 in table text but let's map logically
    const H05_STANDARD_PROMOTION_REAL = 'standard_promotion'; 
    const H06_ACCELERATED_JUMP = 'accelerated_jump';
    const H07_BACKTRACKING     = 'backtracking_penalty';
    const H08_MODULE_GRADUATION= 'module_graduation';
    const H09_GOLD_CERT        = 'gold_certificate';
    const H10_SILVER_CERT      = 'silver_certificate';
    const H11_BRONZE_CERT      = 'bronze_certificate';


    /**
     * Main Entry Point: Evaluate Rules based on State and Context
     */
    public function evaluate(array $state, bool $isCorrect, bool $usedHint, int $score = 0, int $timeSpent = 0, string $difficulty = 'beginner', string $materialType = 'quiz'): array
    {
        // 1. GATHER FACTS (Fase Evaluasi Kondisi)
        $facts = $this->gatherFacts($state, $isCorrect, $usedHint, $score, $timeSpent, $difficulty, $materialType);
        
        // 2. FORWARD CHAINING (Fase Pengambilan Keputusan)
        $triggeredRules = [];
        $actions = [];
        $newState = $state;

        // In this specific rule set, rules are priority-based or mutually exclusive.
        // We evaluate them in order of specificity (Crisis -> Jump -> Progression).
        
        $match = null;

        // --- INTERVENTION PATH (Crisis) ---
        
        // Rule 1: Visual Crisis Handler
        // G01 (Critical) AND G06 (Normal Time) AND G07 (Visual) AND G15 (Easy) ...
        // Note: We use flexible matching where key facts must be present.
        if ($this->hasFacts($facts, [self::G01_SCORE_CRITICAL, self::G06_TIME_NORMAL, self::G08_STYLE_VISUAL, self::G15_LEVEL_BEGINNER])) {
            $match = ['id' => 'Rule 1', 'name' => 'Visual Crisis Handler', 'action' => self::H01_CRISIS_VISUAL];
        }
        
        // Rule 2: Textual Crisis Handler
        elseif ($this->hasFacts($facts, [self::G01_SCORE_CRITICAL, self::G06_TIME_NORMAL, self::G09_STYLE_TEXTUAL, self::G15_LEVEL_BEGINNER])) {
            $match = ['id' => 'Rule 2', 'name' => 'Textual Crisis Handler', 'action' => self::H02_CRISIS_TEXTUAL];
        }

        // Rule 3: Syntax Recovery Loop
        // G02 (Remedial) AND G10 (Syntax Weakness) AND G16 (Medium) AND G12 (Hint Used)
        elseif ($this->hasFacts($facts, [self::G02_SCORE_REMEDIAL, self::G10_WEAKNESS_SYNTAX, self::G16_LEVEL_INTERMED, self::G12_HINT_USED])) {
            $match = ['id' => 'Rule 3', 'name' => 'Syntax Recovery Loop', 'action' => self::H03_SYNTAX_RECOVERY];
        }
        
        // Rule 6: Critical Backtracking
        // (G01 Critical AND G16 Medium) OR (G01 Critical AND G17 Advanced)
        elseif (
            ($this->hasFacts($facts, [self::G01_SCORE_CRITICAL, self::G16_LEVEL_INTERMED])) ||
            ($this->hasFacts($facts, [self::G01_SCORE_CRITICAL, self::G17_LEVEL_ADVANCED]))
        ) {
            $match = ['id' => 'Rule 6', 'name' => 'Critical Backtracking', 'action' => self::H07_BACKTRACKING];
        }

        // --- PROGRESSION PATH ---

        // Rule 5: The Genius Jump
        // G04 (Mastery) AND G05 (Fast) AND G11 (No Hint) AND G15 (Easy) AND G19 (Next Locked - implicit)
        elseif ($this->hasFacts($facts, [self::G04_SCORE_MASTERY, self::G05_TIME_FAST, self::G11_NO_HINT, self::G15_LEVEL_BEGINNER])) {
            $match = ['id' => 'Rule 5', 'name' => 'The Genius Jump', 'action' => self::H06_ACCELERATED_JUMP];
        }

        // Rule 7: Inter-Module Jump
        // G04 (Mastery) AND G05 (Fast) AND G11 (No Hint) AND G17 (Advanced)
        elseif ($this->hasFacts($facts, [self::G04_SCORE_MASTERY, self::G05_TIME_FAST, self::G11_NO_HINT, self::G17_LEVEL_ADVANCED])) {
            $match = ['id' => 'Rule 7', 'name' => 'Inter-Module Jump', 'action' => self::H08_MODULE_GRADUATION];
        }

        // Rule 4: Standard Progression
        // (G03 Standard AND G11 No Hint AND Easy/Medium)
        // Simplified: If Passed (Standard or Mastery)
        elseif (
            ($this->hasFacts($facts, [self::G03_SCORE_STANDARD, self::G11_NO_HINT])) ||
            ($this->hasFacts($facts, [self::G04_SCORE_MASTERY])) // Fallback if not genius
        ) {
            $match = ['id' => 'Rule 4', 'name' => 'Standard Progression', 'action' => self::H05_STANDARD_PROMOTION_REAL];
        }
        
        // --- CERTIFICATION PATH (Final Project) ---
        // Rule 8, 9, 10 check G18_IS_PROJECT
        if ($this->hasFacts($facts, [self::G18_IS_PROJECT])) {
            // Override previous match if it's a project
            if ($this->hasFacts($facts, [self::G04_SCORE_MASTERY, self::G11_NO_HINT])) {
                $match = ['id' => 'Rule 8', 'name' => 'Gold Certification', 'action' => self::H09_GOLD_CERT];
            } elseif ($this->hasFacts($facts, [self::G03_SCORE_STANDARD, self::G11_NO_HINT])) {
                 $match = ['id' => 'Rule 9', 'name' => 'Silver Certification', 'action' => self::H10_SILVER_CERT];
            } else {
                 $match = ['id' => 'Rule 10', 'name' => 'Bronze Certification', 'action' => self::H11_BRONZE_CERT];
            }
        }

        // Apply Logic
        if ($match) {
            $triggeredRules[] = $match;
            
            // Apply Effects to State
            $newState = $this->applyAction($newState, $match['action']);
        } else {
             // Default Fallback (Rule 11ish or keep going)
             // If Score Critical but no specific rule matched (e.g. Time Slow)?
             if ($this->hasFact($facts, self::G01_SCORE_CRITICAL)) {
                 $triggeredRules[] = ['id' => 'Rule 11', 'name' => 'General Intervention', 'action' => self::H01_CRISIS_VISUAL];
                 // No implicit fatigue warning in spec
             }
        }

        return [
            'triggered_rules' => collect($triggeredRules)->pluck('name')->toArray(),
            'new_state' => $newState,
            'facts' => $facts, // Optional: debug
            'action' => $match['action'] ?? null
        ];
    }

    protected function gatherFacts($state, $isCorrect, $usedHint, $score, $timeSpent, $difficulty, $materialType): array
    {
        $facts = [];

        // 1. Score Facts
        // Map Boolean isCorrect to "Score" categories if score is just 0/100
        // Or specific score if provided.
        // Assuming strict: 0 = Critical, 100 = Mastery?
        // Let's use logic:
        // Critical: < 40 (Wrong)
        // Remedial: 40-69 (Wrong but close? or partial?)
        // Standard: 70-89 (Correct)
        // Mastery: 90-100 (Correct)
        
        $finalScore = $score;
        if ($score == 0 && !$isCorrect) $finalScore = 0;
        if ($score == 0 && $isCorrect) $finalScore = 100; // Default

        if ($finalScore < 40) $facts[] = self::G01_SCORE_CRITICAL;
        elseif ($finalScore < 70) $facts[] = self::G02_SCORE_REMEDIAL;
        elseif ($finalScore < 90) $facts[] = self::G03_SCORE_STANDARD;
        else $facts[] = self::G04_SCORE_MASTERY;

        // 2. Time Facts
        if ($timeSpent < 30) $facts[] = self::G05_TIME_FAST;
        elseif ($timeSpent <= 120) $facts[] = self::G06_TIME_NORMAL;
        else $facts[] = self::G07_TIME_SLOW;

        // 3. Style Facts (From State)
        $style = $state['learning_style'] ?? 'visual'; // Default
        if ($style === 'visual') $facts[] = self::G08_STYLE_VISUAL;
        else $facts[] = self::G09_STYLE_TEXTUAL;

        // 4. Weakness (Mock logic or from state)
        // In future, analyze question tags. 
        // For now, if Difficulty Medium AND Wrong -> Trigger Syntax Weakness?
        if (!$isCorrect && $difficulty === 'medium') {
            $facts[] = self::G10_WEAKNESS_SYNTAX;
        }

        // 5. Context Facts
        if ($usedHint) $facts[] = self::G12_HINT_USED;
        else $facts[] = self::G11_NO_HINT;

        if ($difficulty === 'beginner') $facts[] = self::G15_LEVEL_BEGINNER;
        elseif ($difficulty === 'medium') $facts[] = self::G16_LEVEL_INTERMED;
        elseif ($difficulty === 'hard') $facts[] = self::G17_LEVEL_ADVANCED;

        if ($materialType === 'project') $facts[] = self::G18_IS_PROJECT;

        return $facts;
    }

    protected function hasFact(array $facts, string $fact): bool
    {
        return in_array($fact, $facts);
    }

    protected function hasFacts(array $facts, array $requiredFacts): bool
    {
        foreach ($requiredFacts as $req) {
            if (!in_array($req, $facts)) return false;
        }
        return true;
    }

    protected function applyAction(array $state, string $action): array
    {
        // Mutate state based on Action
        switch ($action) {
            case self::H01_CRISIS_VISUAL:
                $state['recommendation'] = 'Visual Remediation';
                $state['next_action'] = 'remedial';
                break;
            case self::H06_ACCELERATED_JUMP:
                $state['fast_track_active'] = 1;
                $state['xp'] = ($state['xp'] ?? 0) + 50; // Bonus
                $state['message'] = 'Genius Jump Activated!';
                break;
            case self::H07_BACKTRACKING:
                $state['status'] = 'downgrade_level';
                $state['xp'] = max(0, ($state['xp'] ?? 0) - 10);
                break;
            case self::H09_GOLD_CERT:
                $state['certification'] = 'Gold';
                $state['xp'] += 500;
                break;
            // ... Add others
        }
        return $state;
    }
}
