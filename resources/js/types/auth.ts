// =============================================================================
// Auth Domain (Synced with App\Models\User & Role)
// =============================================================================

export interface Role {
    id: string;
    role_name: 'superadmin' | 'dosen' | 'mahasiswa' | 'guest';
    created_at?: string;
    updated_at?: string;
}

export interface User {
    id: string;
    name: string;
    email: string;
    role_id: string;
    is_approved: boolean;
    remember_token: string | null;
    created_at: string;
    updated_at: string;
    role?: Role;
}

export interface AuthData {
    user: User | null;
    role: string | null;
    permissions: string[];
}

export interface FlashMessages {
    success: string | null;
    error: string | null;
    warning: string | null;
    info: string | null;
}
