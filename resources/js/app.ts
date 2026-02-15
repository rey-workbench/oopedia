import './bootstrap';
import { createInertiaApp } from '@inertiajs/svelte'
import { mount } from 'svelte'

// Import global styles if needed
import '../css/app.css';

const pages = import.meta.glob("./pages/**/*.svelte");

createInertiaApp({
    resolve: (name) => {
        let path = `./pages/${name}/+page.svelte`;

        // Handle Laravel cases where Index is explicitly passed
        if (!pages[path] && name.endsWith('/Index')) {
            const stripped = name.replace(/\/Index$/, '');
            path = `./pages/${stripped}/+page.svelte`;
        }

        // Fallback to simple name.svelte
        if (!pages[path]) {
            path = `./pages/${name}.svelte`;
        }

        const page = pages[path] as () => Promise<any>;

        if (!page) {
            console.error(`Inertia Resolve Failed: "${name}"`);
            console.log("Attempted Path:", path);
            console.log("Available Pages:", Object.keys(pages));
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
