import './bootstrap';
import { createInertiaApp } from '@inertiajs/svelte'
import { mount } from 'svelte'

// Import global styles if needed
import '../css/app.css';

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./pages/**/*.svelte");
        let path = `./pages/${name}/+page.svelte`;

        if (!pages[path] && name.endsWith('/Index')) {
            path = `./pages/${name.replace(/\/Index$/, '')}/+page.svelte`;
        }

        if (!pages[path]) {
            path = `./pages/${name}.svelte`;
        }

        const page = pages[path];

        if (!page) {
            throw new Error(`Component "${name}" not found.`);
        }
        return page();
    },
    setup({ el, App, props }) {
        if (el) {
            mount(App, { target: el, props });
        } else {
            console.error("Inertia target element not found");
        }
    },
});
