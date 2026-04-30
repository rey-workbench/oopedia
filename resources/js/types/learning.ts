import type { 
    QuestionType, 
    DifficultyLevel, 
    LearningStyle, 
    Media 
} from './core';

export type { QuestionType, DifficultyLevel, LearningStyle, Media };

// =============================================================================
// Learning Models (Synced with App\Models\Material, Question, Answer)
// =============================================================================

export interface Material {
    id: string;
    title: string;
    content: string | null;
    module_id: string | null;
    is_final_project: boolean;
    is_locked?: boolean;
    total_questions?: number;
    student_count?: number;
    progress_percentage?: number;
    completed_questions?: number;
    media?: Media[];
    creator?: { name: string };
    updated_at?: string;
}

export interface Question {
    id: string;
    material_id: string;
    content: string;
    question_text: string;
    question_type: QuestionType;
    difficulty: DifficultyLevel;
    learning_style: LearningStyle;
    hint: string | null;
    media_id: string | null;
    created_at?: string;
    updated_at?: string;
    answers?: Answer[];
    media?: Media;
}

export interface Answer {
    id: string;
    question_id: string;
    content: string;
    answer_text: string;
    is_correct: boolean;
    explanation: string | null;
    drag_source?: string;
    drag_target?: string;
    created_at?: string;
    updated_at?: string;
}

export interface QuizAttempt {
    id: string;
    user_id: string;
    question_id: string;
    answer_id: string | null;
    user_response: string | null;
    is_correct: boolean;
    score: number;
    attempt_number: number;
    time_spent: number | null;
    created_at: string;
    updated_at: string;
}

export interface Certification {
    id: string;
    material_id: string;
    material_title: string;
    type: 'gold' | 'silver' | 'bronze';
    issued_at: string | null;
}

// =============================================================================
// Computed Learning Shapes
// =============================================================================

export interface DifficultyStats {
    correct: number;
    total: number;
    configured_total: number;
    percentage: number;
}

export interface MaterialWithStats {
    material: Material;
    stats: {
        overall: DifficultyStats;
        beginner: DifficultyStats;
        medium: DifficultyStats;
        hard: DifficultyStats;
    };
}

export interface UserAttempt {
    is_correct: boolean;
    answer_id: string;
    attempt_number: number;
    score: number;
}

export type QuestionWithAttempt = Question & {
    user_attempt: UserAttempt | null;
};
