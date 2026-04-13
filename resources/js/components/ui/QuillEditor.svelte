<script lang="ts">
    import { onMount } from 'svelte';
    import hljs from 'highlight.js';
    import 'quill/dist/quill.snow.css';
    import 'highlight.js/styles/atom-one-dark.css';

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

    if (value === undefined) value = '';

    let editorContainer: HTMLElement;
    let quillInstance: import('quill').default | null = null;
    let csrfToken = $state('');

    onMount(async () => {
        if (typeof window === 'undefined') return;

        // Get CSRF token
        csrfToken =
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        try {
            const Quill = (await import('quill')).default;

            quillInstance = new Quill(editorContainer, {
                theme: 'snow',
                placeholder: placeholder,
                modules: {
                    syntax: { hljs },
                    toolbar: {
                        container: [
                            [{ header: [1, 2, 3, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ list: 'ordered' }, { list: 'bullet' }],
                            ['link', 'image', 'code-block'],
                            ['clean'],
                        ],
                        handlers: {
                            image: imageHandler,
                        },
                    },
                },
            });

            if (value) {
                quillInstance.root.innerHTML = value;
            }

            quillInstance.on('text-change', () => {
                const html = quillInstance!.root.innerHTML;
                value = html;
                oninput(html);
            });
        } catch (e) {
            console.error('Failed to load Quill:', e);
        }
    });

    async function imageHandler() {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';

        input.onchange = async () => {
            if (!input.files || input.files.length === 0 || !quillInstance) return;

            const file = input.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('file', file);

            try {
                const response = await fetch('/admin/media/upload', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });

                const data = await response.json();

                if (data.success && data.url) {
                    const range = quillInstance.getSelection();
                    quillInstance.insertEmbed(range?.index || 0, 'image', data.url);
                } else {
                    console.error('Upload failed:', data.message);
                    alert('Gagal mengupload gambar: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Image upload error:', error);
                alert('Gagal mengupload gambar');
            }
        };

        input.click();
    }
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
