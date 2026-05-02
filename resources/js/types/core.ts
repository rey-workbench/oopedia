/**
 * resources/js/types/core.ts
 *
 * Primitive enums and utility types shared across all domains.
 * These do NOT mirror specific backend response shapes —
 * they are building blocks used by models and props.
 */

// ==============================================================
// Enums — mirror PHP Enums in app/Enums/
// ==============================================================

/** Mirrors App\Enums\Lms\QuestionDifficulty */
export type QuestionDifficulty = 'beginner' | 'medium' | 'hard' | 'final';

/** Mirrors App\Enums\Lms\QuestionType */
export type QuestionType = 'radio_button' | 'drag_and_drop' | 'fill_in_the_blank';

/** Mirrors App\Enums\Lms\MediaType */
export type MediaType = 'image' | 'video' | 'file';

/** Mirrors App\Enums\User\RoleName */
export type AppRole = 'superadmin' | 'dosen' | 'mahasiswa' | 'guest';

// ==============================================================
// Pagination — mirrors Laravel LengthAwarePaginator
// ==============================================================

export interface PaginatorLink {
    url: string | null;
    label: string;
    active: boolean;
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

// ==============================================================
// Media — mirrors Media Eloquent model
// ==============================================================

export interface Media {
    id: string;
    name: string;
    file_path: string;
    file_type: MediaType;
    collection_name: string;
    size: number;
    mime_type: string;
    media_url?: string;
    full_url?: string;
    created_at: string;
    updated_at: string;
}

// ==============================================================
// Form state helpers (used by FormState.svelte.ts only)
// ==============================================================

export interface FormStateOptions {
    isEdit?: boolean;
    showSuccessToast?: string | boolean;
    showErrorToast?: boolean;
}

export interface FormSubmitOptions {
    forceFormData?: boolean;
    _method?: string;
    onSuccess?: () => void;
    onError?: (errors: Record<string, string>) => void;
    onFinish?: () => void;
    showSuccessToast?: string | boolean;
    showErrorToast?: boolean;
    [key: string]: unknown;
}
