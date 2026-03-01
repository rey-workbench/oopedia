import { defineConfig } from 'eslint-define-config';
import ts from '@typescript-eslint/eslint-plugin';
import tsParser from '@typescript-eslint/parser';
import svelte from 'eslint-plugin-svelte';
import svelteParser from 'svelte-eslint-parser';

/** Browser / DOM globals for no-undef rule */
const browserGlobals = {
    window: 'readonly',
    document: 'readonly',
    navigator: 'readonly',
    HTMLElement: 'readonly',
    HTMLDivElement: 'readonly',
    HTMLInputElement: 'readonly',
    HTMLTextAreaElement: 'readonly',
    HTMLSelectElement: 'readonly',
    HTMLButtonElement: 'readonly',
    HTMLFormElement: 'readonly',
    Event: 'readonly',
    InputEvent: 'readonly',
    MouseEvent: 'readonly',
    KeyboardEvent: 'readonly',
    DragEvent: 'readonly',
    SubmitEvent: 'readonly',
    FocusEvent: 'readonly',
    PointerEvent: 'readonly',
    setTimeout: 'readonly',
    clearTimeout: 'readonly',
    setInterval: 'readonly',
    clearInterval: 'readonly',
    requestAnimationFrame: 'readonly',
    cancelAnimationFrame: 'readonly',
    fetch: 'readonly',
    FormData: 'readonly',
    FileReader: 'readonly',
    File: 'readonly',
    Blob: 'readonly',
    URL: 'readonly',
    URLSearchParams: 'readonly',
    console: 'readonly',
    alert: 'readonly',
    confirm: 'readonly',
    prompt: 'readonly',
    localStorage: 'readonly',
    sessionStorage: 'readonly',
    IntersectionObserver: 'readonly',
    MutationObserver: 'readonly',
    ResizeObserver: 'readonly',
    DOMParser: 'readonly',
    history: 'readonly',
    location: 'readonly',
    performance: 'readonly',
    crypto: 'readonly',
    globalThis: 'readonly',
    self: 'readonly',
    Promise: 'readonly',
    Map: 'readonly',
    Set: 'readonly',
    WeakMap: 'readonly',
    WeakSet: 'readonly',
    Symbol: 'readonly',
    Proxy: 'readonly',
    Reflect: 'readonly',
    Int8Array: 'readonly',
    Uint8Array: 'readonly',
    Float32Array: 'readonly',
    Float64Array: 'readonly',
};

/** Shared TypeScript rules applied to all blocks */
const tsRules = {
    '@typescript-eslint/no-explicit-any': 'warn',
    '@typescript-eslint/no-unused-vars': [
        'warn',
        {
            varsIgnorePattern: '^_',
            argsIgnorePattern: '^_',
            caughtErrorsIgnorePattern: '^_',
            ignoreRestSiblings: true,
        },
    ],
    '@typescript-eslint/no-require-imports': 'warn',
    'no-undef': 'off',
};

export default defineConfig([
    // Ignore non-source directories
    {
        ignores: ['vendor/**', 'public/**', 'node_modules/**', 'bootstrap/cache/**', 'storage/**'],
    },

    // ---- TypeScript files (.ts, .svelte.ts) ----
    {
        files: ['resources/js/**/*.ts'],
        plugins: { '@typescript-eslint': ts },
        languageOptions: {
            parser: tsParser,
            parserOptions: {
                sourceType: 'module',
            },
            globals: browserGlobals,
        },
        rules: tsRules,
    },

    // ---- Svelte files (.svelte) ----
    {
        files: ['resources/js/**/*.svelte'],
        plugins: {
            svelte,
            '@typescript-eslint': ts,
        },
        languageOptions: {
            parser: svelteParser,
            parserOptions: {
                parser: tsParser,
                extraFileExtensions: ['.svelte'],
                sourceType: 'module',
            },
            globals: browserGlobals,
        },
        rules: {
            ...svelte.configs.recommended.rules,
            ...tsRules,
        },
    },
]);
