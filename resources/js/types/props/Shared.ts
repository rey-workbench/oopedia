import type { User } from '../models/User';

/**
 * resources/js/types/props/Shared.ts
 * Injected into every page via HandleInertiaRequests
 */

export interface SharedProps {
    auth: {
        user: User | null;
        role: string | null;
    };
    flash: {
        success: string | null;
        error: string | null;
        info: string | null;
        warning: string | null;
    };
    errors: Record<string, string>;
}

export type PageProps<T = Record<string, unknown>> = T & SharedProps;
