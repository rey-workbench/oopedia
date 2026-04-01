import { writable } from 'svelte/store';

export interface Toast {
    id: string;
    type: 'success' | 'error' | 'info' | 'warning';
    title?: string;
    message: string;
    duration?: number;
    dismissible?: boolean;
}

function createToastStore() {
    const { subscribe, update } = writable<Toast[]>([]);

    function add(toast: Omit<Toast, 'id'>): string {
        const id = crypto.randomUUID();
        const newToast: Toast = { ...toast, id };

        update((toasts) => [...toasts, newToast]);

        const duration = toast.duration ?? 5000;
        if (duration > 0) {
            setTimeout(() => remove(id), duration);
        }

        return id;
    }

    function remove(id: string) {
        update((toasts) => toasts.filter((t) => t.id !== id));
    }

    function success(message: string, options?: Partial<Omit<Toast, 'id' | 'type' | 'message'>>) {
        return add({ type: 'success', message, ...options });
    }

    function error(message: string, options?: Partial<Omit<Toast, 'id' | 'type' | 'message'>>) {
        return add({ type: 'error', message, duration: 8000, ...options });
    }

    function info(message: string, options?: Partial<Omit<Toast, 'id' | 'type' | 'message'>>) {
        return add({ type: 'info', message, ...options });
    }

    function warning(message: string, options?: Partial<Omit<Toast, 'id' | 'type' | 'message'>>) {
        return add({ type: 'warning', message, ...options });
    }

    function clear() {
        update(() => []);
    }

    return {
        subscribe,
        add,
        remove,
        success,
        error,
        info,
        warning,
        clear,
    };
}

export const toasts = createToastStore();
