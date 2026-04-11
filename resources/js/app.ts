import '@/bootstrap';
import { createInertiaApp } from '@inertiajs/svelte';
import { mount } from 'svelte';

import '../css/app.css';

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob('./pages/**/*.svelte', { eager: true });
        const page = pages[`./pages/${name}.svelte`];

        if (!page) {
            console.error(`Inertia Resolve Failed: "${name}"`);
            console.log('Available Pages:', Object.keys(pages));
            throw new Error(`Component "${name}" not found.`);
        }

        return page;
    },
    setup({ el, App, props }) {
        if (el) {
            mount(App, { target: el, props });
        } else {
            console.error('Inertia target element not found');
        }
    },
});
