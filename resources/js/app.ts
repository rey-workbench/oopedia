import '@/bootstrap';
import { createInertiaApp } from '@inertiajs/svelte';
import { mount } from 'svelte';

import '../css/app.css';

const pages = import.meta.glob('./pages/**/*.svelte');

createInertiaApp({
    resolve: (name) => {
        const key = Object.keys(pages).find((k) => {
            return (
                k === `./pages/${name}.svelte` ||
                k === `./pages/${name}/Index.svelte` ||
                k === `./pages/${name}/+page.svelte` ||
                k.endsWith(`/pages/${name}.svelte`) ||
                k.endsWith(`/pages/${name}/Index.svelte`) ||
                k.endsWith(`/pages/${name}/+page.svelte`)
            );
        });

        const resolved = key ? pages[key] : null;

        if (!resolved) {
            console.error(`Inertia Resolve Failed: "${name}"`);
            console.log('Available Pages:', Object.keys(pages));
            throw new Error(`Component "${name}" not found.`);
        }

        return (resolved as any)();
    },
    setup({ el, App, props }) {
        if (el) {
            mount(App, { target: el, props });
        } else {
            console.error('Inertia target element not found');
        }
    },
});
