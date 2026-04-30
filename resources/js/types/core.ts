// =============================================================================
// Primitives & Enums (Synced with App\Enums)
// =============================================================================

export type QuestionType = 'radio_button' | 'drag_and_drop' | 'fill_in_the_blank';
export type DifficultyLevel = 'beginner' | 'medium' | 'hard' | 'final';
export type ContentCategory = 'teori' | 'sintaks' | 'mixed';
export type LearningStyle = 'visual' | 'textual' | 'mixed';
export type MediaType = 'image' | 'video' | 'file';
export type StudentLevel = 'Pemula' | 'Menengah' | 'Ahli';

// =============================================================================
// Common Models
// =============================================================================

export interface Media {
    id: string;
    name: string;
    file_path: string;
    file_type: MediaType;
    collection_name: string;
    size: number;
    mime_type: string;
    // Aliases for compatibility
    media_type?: MediaType;
    media_url?: string;
    full_url?: string;
    created_at?: string;
    updated_at?: string;
}

export interface Pagination<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    first_page_url: string;
    last_page_url: string;
    next_page_url: string | null;
    prev_page_url: string | null;
    path: string;
    links: PaginatorLink[];
}

export interface PaginatorLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface Toast {
    id: string;
    message: string;
    type: 'success' | 'error' | 'info' | 'warning';
    duration?: number;
}
