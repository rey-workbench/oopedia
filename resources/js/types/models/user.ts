import type { Role } from './role';

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
