import type { Question, Media, User } from './index';

export interface Material {
    id: string;
    title: string;
    description?: string;
    content: string | null;
    module_id: string | null;
    is_final_project: boolean;
    is_locked?: boolean;
    total_questions?: number;
    student_count?: number;
    progress_percentage?: number;
    completed_questions?: number;
    created_by: string | null;
    created_at: string;
    updated_at: string;
    questions?: Question[];
    media?: Media[];
    creator?: User;
}
