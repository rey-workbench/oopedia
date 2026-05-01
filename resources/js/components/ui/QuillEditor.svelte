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

            quillInstance.root.classList.add('oopedia-wysiwyg');

            if (value) {
                quillInstance.root.innerHTML = value;
            }

            quillInstance.on('text-change', () => {
                let html = quillInstance!.root.innerHTML;
                
                // Cleanup: Create a virtual DOM to process tags consistently
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                
                // 1. Convert Quill Code Blocks to Semantic <pre><code class="language-java">
                const codeContainers = tempDiv.querySelectorAll('.ql-code-block-container');
                codeContainers.forEach(container => {
                    const lines = container.querySelectorAll('.ql-code-block');
                    const codeText = Array.from(lines).map(l => (l as HTMLElement).innerText).join('\n');
                    
                    const pre = document.createElement('pre');
                    const code = document.createElement('code');
                    code.className = 'language-java';
                    code.innerText = codeText;
                    pre.appendChild(code);
                    
                    container.parentNode?.replaceChild(pre, container);
                });

                // 2. Standardize Tables (Remove all proprietary classes)
                const tables = tempDiv.querySelectorAll('table');
                tables.forEach(table => {
                    table.removeAttribute('class');
                    table.querySelectorAll('td, th, tr, thead, tbody').forEach(el => {
                        (el as HTMLElement).removeAttribute('class');
                        (el as HTMLElement).removeAttribute('style');
                    });
                });

                // 3. Clean up Blockquotes
                const blockquotes = tempDiv.querySelectorAll('blockquote');
                blockquotes.forEach(bq => {
                    bq.removeAttribute('class');
                    bq.removeAttribute('style');
                });

                // 4. Remove any other Quill-specific classes or empty tags
                tempDiv.querySelectorAll('[class^="ql-"]').forEach(el => {
                    const element = el as HTMLElement;
                    // Only remove class, keep the element if it's not a container we already handled
                    element.removeAttribute('class');
                });

                const cleanHtml = tempDiv.innerHTML;
                value = cleanHtml;
                oninput(cleanHtml);
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

<div
    class="quill-wrapper focus-within:border-primary-500 focus-within:ring-primary-100 overflow-hidden rounded-3xl border-2 border-b-6 border-slate-200 bg-white transition-all focus-within:ring-4 hover:border-slate-300"
>
    <div bind:this={editorContainer} style="height: {height};"></div>
</div>
