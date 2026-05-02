import type { Media } from '../core';
import type { User } from './User';

/**
 * resources/js/types/models/Material.ts
 *
 * Mirrors the Material Eloquent model and its quiz-context extensions
 */

export interface Material {
    id: string;
    title: string;
    content: string;
    cover_image?: string | null;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    cover_media?: Media | null;
    media?: Media[];

    // Extensions from controllers
    is_locked?: boolean;
    is_final_project?: boolean;
    creator?: User;
    total_questions?: number;
    completed_questions?: number;
    student_count?: number;
    completion_rate?: number;
    progress_percentage?: number;
    module_id?: string | null;
    status?: 'not_started' | 'in_progress' | 'completed';
    last_accessed?: string | null;
}
