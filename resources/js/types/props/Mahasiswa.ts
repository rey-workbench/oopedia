import type { SharedProps } from './Shared';
import type { RecentActivity, LeaderboardEntry, Material, Question } from '@/types';

/**
 * resources/js/types/props/Mahasiswa.ts
 */

export interface MahasiswaDashboardProps extends SharedProps {
    total_materials: number;
    total_questions: number;
    easy_questions: number;
    medium_questions: number;
    hard_questions: number;
    material_progress_percentage: number;
    question_progress_percentage: number;
    completed_materials: number;
    in_progress_materials: number;
    total_material_progress: number;
    total_answered: number;
    total_correct_questions: number;
    recent_activities: RecentActivity[];
    all_materials: Material[];
    current_user_rank: LeaderboardEntry | null;
    certifications: Record<string, string>;
}

export interface QuestionShowProps extends SharedProps {
    material: Material;
    questions?: Question[];
    current_question?: Question | null;
    current_question_number?: number;
    total_questions?: number;
    answered_count?: number;
    material_answered_count?: number;
    level_progress?: any[];
    difficulty?: string;
    is_guest?: boolean;
    student_state?: any; // StudentSessionState
    feedback?: any;
}
