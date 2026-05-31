import '@/bootstrap';
import { createInertiaApp } from '@inertiajs/svelte';
import { mount } from 'svelte';

import '../css/app.css';

import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { initTutorials } from '@/tutorial';

initTutorials();

// Menangani Error Chunk Load (misalnya setelah deploy versi baru)
window.addEventListener('vite:preloadError', () => {
    console.warn('Terjadi kesalahan saat memuat module (chunk lama tidak ditemukan). Memuat ulang halaman...');
    window.location.reload();
});

createInertiaApp({
    progress: false,
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.svelte`,
            import.meta.glob<any>('./pages/**/*.svelte')
        ),
    setup({ el, App, props }) {
        if (el) {
            mount(App, { target: el, props });
        } else {
            console.error('Inertia setup called without an element (SSR context or error)');
        }
    },
});
