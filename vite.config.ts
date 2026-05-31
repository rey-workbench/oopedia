import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { svelte } from '@sveltejs/vite-plugin-svelte';
import inertia from '@inertiajs/vite';
import path from 'path';
import { fileURLToPath } from 'url';
import obfuscatorPlugin from 'vite-plugin-javascript-obfuscator';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

export default defineConfig({
    plugins: [
        svelte(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            refresh: true,
        }),
        tailwindcss(),
        inertia({ ssr: false }),
        // obfuscatorPlugin({
        //     include: ['resources/js/**/*.js', 'resources/js/**/*.ts', 'resources/js/**/*.svelte'],
        //     exclude: [/node_modules/],
        //     apply: 'build',
        //     options: {
        //         // Konfigurasi High Performance (Cepat, Ringan, Tidak Lemot)
        //         compact: true,
        //         controlFlowFlattening: false, // Wajib false agar tidak lag
        //         deadCodeInjection: false,
        //         debugProtection: false,
        //         disableConsoleOutput: true,
        //         identifierNamesGenerator: 'hexadecimal',
        //         log: false,
        //         renameGlobals: false,
        //         selfDefending: false, // Wajib false agar memory tidak bocor
        //         simplify: true,
        //         splitStrings: false, // Wajib false agar ukuran file tidak bengkak
        //         stringArray: true,
        //         stringArrayCallsTransform: false,
        //         stringArrayEncoding: [], // Tanpa encoding base64 agar browser tidak bekerja dua kali
        //         stringArrayIndexShift: true,
        //         stringArrayRotate: true,
        //         stringArrayShuffle: true,
        //         stringArrayWrappersCount: 1,
        //         stringArrayWrappersChainedCalls: true,
        //         stringArrayWrappersParametersMaxCount: 2,
        //         stringArrayWrappersType: 'variable',
        //         stringArrayThreshold: 0.75,
        //         unicodeEscapeSequence: false
        //     }
        // })
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            'lucide-svelte': '@lucide/svelte',
        },
    },
    server: {
        host: '127.0.0.1',
    },
});
