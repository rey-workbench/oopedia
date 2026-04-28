import { page } from '@inertiajs/svelte';
import type { SharedProps } from '@/types';

/**
 * BaseState - Standardized base class for Svelte 5 states
 * Provides common utilities for page-level state management.
 */
export class BaseState {
    /**
     * Safe assignment that skips getters and reserved Inertia props.
     * MUST be called in subclass constructor AFTER fields are declared
     * to avoid "private member" errors with Svelte 5 runes.
     */
    protected hydrate(data?: Record<string, unknown>) {
        if (!data) return;

        const reserved = ['user', 'isGuest', 'flash', 'errors', 'auth', 'ziggy'];
        const filteredData = Object.keys(data)
            .filter((key) => !reserved.includes(key))
            .reduce(
                (obj, key) => {
                    obj[key] = data[key];
                    return obj;
                },
                {} as Record<string, unknown>
            );

        Object.assign(this, filteredData);
    }

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
     */
    sync(data: Record<string, unknown>) {
        this.hydrate(data);
    }
}
