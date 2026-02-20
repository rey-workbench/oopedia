import { page } from "@inertiajs/svelte";
import { get } from "svelte/store";

/**
 * BaseState - Standardized base class for Svelte 5 states
 * Provides common utilities for page-level state management.
 */
export class BaseState {
    /**
     * Get authenticated user
     */
    get user() {
        return (get(page).props as any).auth?.user;
    }

    /**
     * Get flash messages
     */
    get flash() {
        return (get(page).props as any).flash || {};
    }

    /**
     * Get current error props
     */
    get errors() {
        return (get(page).props as any).errors || {};
    }

    /**
     * Common method to sync props if needed
     * (Prefer constructor injection, but this can be used for partial updates)
     */
    sync(data: any) {
        Object.assign(this, data);
    }
}
