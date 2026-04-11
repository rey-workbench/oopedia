import '@/bootstrap';
import { createInertiaApp } from '@inertiajs/svelte';
import { mount } from 'svelte';

import '../css/app.css';

import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { initTutorials } from '@/tutorial';

initTutorials();

createInertiaApp({
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.svelte`,
            import.meta.glob<any>('./pages/**/*.svelte')
        ),
    setup({ el, App, props }) {
        console.log('Inertia Setup', { el, App, props });
        if (el) {
            console.log('Mounting App to', el);
            mount(App, { target: el, props });
            console.log('App mounted successfully');
        } else {
            console.error('Inertia setup called without an element (SSR context or error)');
        }
    },
});
