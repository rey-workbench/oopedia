import { router } from '@inertiajs/svelte';

export const route = {
    visit: (url: string, options = {}) => router.visit(url, options),
    post: (url: string, data = {}, options = {}) => router.post(url, data, options),
    put: (url: string, data = {}, options = {}) => router.put(url, data, options),
    patch: (url: string, data = {}, options = {}) => router.patch(url, data, options),
    delete: (url: string, options = {}) => router.delete(url, options),
    reload: (options = {}) => router.reload(options),
} as const;

export function navigateTo(url: string, options?: Parameters<typeof router.visit>[1]) {
    router.visit(url, options);
}

export function redirectTo(url: string, method: 'post' | 'get' = 'post', data = {}) {
    if (method === 'post') {
        router.post(url, data);
    } else {
        router.get(url, data);
    }
}
