import '@/bootstrap';
import { createInertiaApp } from '@inertiajs/svelte';
import { mount } from 'svelte';
import { render } from 'svelte/server';

import '../css/app.css';

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob<any>('./pages/**/*.svelte', { eager: true });
        const page = pages[`./pages/${name}.svelte`];

        if (!page) {
            console.error(`Inertia Resolve Failed: "${name}"`);
            console.log('Available Pages:', Object.keys(pages));
            throw new Error(`Component "${name}" not found.`);
        }

        return page.default || page;
    },
    setup({ el, App, props }) {
        if (el) {
            mount(App, { target: el, props });
            return;
        }

        const result = render(App, { props });
        return {
            body: result.html,
            head: result.head,
        };
    },
});


