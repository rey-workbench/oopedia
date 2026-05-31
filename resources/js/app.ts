import '@/bootstrap';
import { createInertiaApp } from '@inertiajs/svelte';
import { mount } from 'svelte';

import '../css/app.css';

import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { initTutorials } from '@/tutorial';

initTutorials();

// Membersihkan flag reload jika berhasil masuk tanpa error
sessionStorage.removeItem('chunk_reloaded');

// Menangani Error Chunk Load (misalnya setelah deploy versi baru)
window.addEventListener('vite:preloadError', () => {
    console.warn('Terjadi kesalahan saat memuat module (chunk lama tidak ditemukan). Memuat ulang halaman...');
    window.location.reload();
});

createInertiaApp({
    progress: false,
    resolve: async (name) => {
        try {
            return await resolvePageComponent(
                `./pages/${name}.svelte`,
                import.meta.glob<any>('./pages/**/*.svelte')
            );
        } catch (error: any) {
            if (error.name === 'TypeError' || error.message.includes('Failed to fetch')) {
                if (!sessionStorage.getItem('chunk_reloaded')) {
                    sessionStorage.setItem('chunk_reloaded', 'true');
                    console.warn('Chunk tidak ditemukan atau diblokir. Melakukan hard reload 1x...');
                    window.location.reload();
                    return new Promise(() => {}); // Gantung svelte agar tidak crash
                } else {
                    console.error('Hard reload gagal mengatasi error fetch modul. Menghentikan loop reload.');
                    sessionStorage.removeItem('chunk_reloaded');
                }
            }
            throw error;
        }
    },
    setup({ el, App, props }) {
        if (el) {
            mount(App, { target: el, props });
        } else {
            console.error('Inertia setup called without an element (SSR context or error)');
        }
    },
});
