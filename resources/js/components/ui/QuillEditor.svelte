<script lang="ts">
    import { onMount } from 'svelte';
    import Quill from 'quill';
    import hljs from 'highlight.js';
    import 'quill/dist/quill.snow.css';
    import 'highlight.js/styles/atom-one-dark.css';

    // Quill syntax module expects hljs on window
    if (typeof window !== 'undefined') {
        Object.assign(window, { hljs });
    }

    interface Props {
        value?: string;
        placeholder?: string;
        height?: string;
        oninput?: (html: string) => void;
    }

    let {
        value = $bindable(),
        placeholder = 'Tulis sesuatu...',
        height = '300px',
        oninput = () => {},
    }: Props = $props();

    // Ensure value is never undefined to prevent Svelte 5 props_invalid_value error
    if (value === undefined) value = '';

    let editorContainer: HTMLElement;
    let quill: Quill;

    onMount(() => {
        if (typeof window !== 'undefined') {
            Object.assign(window, { hljs });
        }

        quill = new Quill(editorContainer, {
            theme: 'snow',
            placeholder: placeholder,
            modules: {
                syntax: {
                    hljs: hljs,
                },
                toolbar: [
                    [{ header: [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['link', 'image', 'code-block'],
                    ['clean'],
                ],
            },
        });

        // Set initial content
        if (value) {
            quill.root.innerHTML = value;
        }

        quill.on('text-change', () => {
            const html = quill.root.innerHTML;
            value = html;
            oninput(html);
        });
    });
</script>

<style>
    :global(.ql-toolbar) {
        border-top: none !important;
        border-left: none !important;
        border-right: none !important;
        border-bottom: 2px solid var(--color-slate-200) !important;
        background-color: var(--color-slate-50) !important;
        padding: 0.75rem 1rem !important;
    }
    :global(.ql-container) {
        border: none !important;
        font-size: 1rem;
        font-family: var(--font-sans, inherit) !important;
    }
    :global(.ql-editor) {
        padding: 1.5rem;
        font-family: inherit;
        min-height: 200px;
    }
    :global(.ql-editor.ql-blank::before) {
        color: var(--color-slate-400) !important;
        font-style: normal !important;
    }
    :global(.ql-snow.ql-toolbar button:hover, .ql-snow .ql-toolbar button:focus) {
        color: var(--color-primary-600) !important;
    }
    :global(.ql-snow.ql-toolbar button.ql-active, .ql-snow .ql-toolbar button.ql-active) {
        color: var(--color-primary-600) !important;
    }
    :global(
        .ql-snow.ql-toolbar button:hover .ql-stroke,
        .ql-snow .ql-toolbar button:focus .ql-stroke,
        .ql-snow.ql-toolbar button.ql-active .ql-stroke,
        .ql-snow .ql-toolbar button.ql-active .ql-stroke
    ) {
        stroke: var(--color-primary-600) !important;
    }
    :global(
        .ql-snow.ql-toolbar button:hover .ql-fill,
        .ql-snow .ql-toolbar button:focus .ql-fill,
        .ql-snow.ql-toolbar button.ql-active .ql-fill,
        .ql-snow .ql-toolbar button.ql-active .ql-fill
    ) {
        fill: var(--color-primary-600) !important;
    }
</style>

<div
    class="quill-wrapper focus-within:border-primary-500 focus-within:ring-primary-100 overflow-hidden rounded-3xl border-2 border-b-6 border-slate-200 bg-white transition-all focus-within:ring-4 hover:border-slate-300"
>
    <div bind:this={editorContainer} style="height: {height};"></div>
</div>
