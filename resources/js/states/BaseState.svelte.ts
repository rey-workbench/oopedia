import { page } from '@inertiajs/svelte';
import type { SharedProps } from '@/types';

/**
 * BaseState - Standardized base class for Svelte 5 states
 * Provides common utilities for page-level state management.
 */
export class BaseState {
    /**
     * Get authenticated user
     */
    get user() {
        return (page.props as unknown as SharedProps).auth?.user;
    }

    /**
     * Check if user is guest
     */
    get isGuest() {
        return !this.user;
    }

    /**
     * Get flash messages
     */
    get flash() {
        return (page.props as unknown as SharedProps).flash || {};
    }

    /**
     * Get current error props
     */
    get errors() {
        return (page.props as unknown as SharedProps).errors || {};
    }

    /**
     * Common method to sync props if needed
     * (Prefer constructor injection, but this can be used for partial updates)
     */
    sync(data: Record<string, unknown>) {
        Object.assign(this, data);
    }
}
