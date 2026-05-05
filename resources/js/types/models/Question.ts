import type { QuestionDifficulty, QuestionType } from '../core';

/**
 * resources/js/types/models/Question.ts
 *
 * Shapes synchronized with backend Resources:
 * - QuestionResource
 * - Answer model mapping in QuestionResource
 */

// ==============================================================
// Answer
// ==============================================================

export interface Answer {
    id: string;
    answer_text: string;
    is_correct: boolean;
    explanation: string | null;
    /** For drag_and_drop questions — the draggable text label */
    drag_source: string | null;
    /** For drag_and_drop questions — the target zone identifier */
    drag_target: string | null;
    /** For fill_in_the_blank questions */
    blank_position: number | null;
}

// ==============================================================
// Question
// ==============================================================

export interface Question {
    id: string;
    material_id: string;
    question_text: string;
    question_type: QuestionType;
    difficulty: QuestionDifficulty;
    hint: string | null;
    answers: Answer[];
    /** For fill_in_the_blank — character count of the correct answer, supplied by QuestionResource */
    blank_length: number | null;
}

// ==============================================================
// UserAttempt (Latest QuizAttempt data attached to Question)
// ==============================================================

export interface UserAttempt {
    score: number;
    is_correct: boolean;
    answer_id: string | null;
    user_response: string | null;
    attempt_number: number;
    time_spent: number;
}

export interface QuestionWithAttempt extends Question {
    user_attempt: UserAttempt | null;
}

// ==============================================================
// QuizService::getLevelProgress()
// Each level item returned in the levels[] array
// ==============================================================

export interface LevelItem {
    level: number;
    question_id: string;
    status: 'completed' | 'unlocked' | 'locked';
}

// ==============================================================
// Admin — StudentState missing questions per material
// Used in AdminStudentController
// ==============================================================

export interface MissingQuestionsItem {
    material_title: string;
    missing_count: number;
}
