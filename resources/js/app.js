import './bootstrap';
import { createInertiaApp } from '@inertiajs/svelte'
import { mount } from 'svelte'

// Import global styles if needed
import '../css/app.css';

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./pages/**/*.svelte");
        const page = pages[`./pages/${name}.svelte`];
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
