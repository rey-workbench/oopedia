import type { User } from '@/types/models';

export interface FlashMessages {
    success?: string;
    error?: string;
    info?: string;
    warning?: string;
    status?: string;
}

export interface AuthData {
    user: User;
}

export interface SharedProps {
    auth: AuthData;
    flash: FlashMessages;
    errors: Record<string, string>;
}
