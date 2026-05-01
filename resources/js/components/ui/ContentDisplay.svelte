<script lang="ts">
    import hljs from 'highlight.js';
    import DOMPurify from 'dompurify';

    interface Props {
        content: string;
        className?: string;
    }

    let { content = '', className = '' }: Props = $props();

    let container: HTMLElement;

    // Sanitize content while allowing table and code tags
    const safeContent = $derived(DOMPurify.sanitize(content, {
        ADD_TAGS: ['iframe', 'blockquote', 'table', 'thead', 'tbody', 'tr', 'th', 'td'],
        ADD_ATTR: ['allow', 'allowfullscreen', 'frameborder', 'scrolling']
    }));

    // Unified Enhancement Logic
    $effect(() => {
        if (safeContent && container) {
            processContent();
        }
    });

    function processContent() {
        // 1. Handle Code Blocks (Java Optimized)
        const codeBlocks = container.querySelectorAll('.ql-code-block-container, .ql-syntax, pre');
        codeBlocks.forEach((block) => {
            const el = block as HTMLElement;
            if (!el.dataset['enhanced']) {
                // Handle Quill divs, standard pre tags, and pre > code structures
                const quillLines = el.querySelectorAll('.ql-code-block');
                const nestedCode = el.querySelector('code');

                let text = '';
                if (quillLines.length > 0) {
                    text = Array.from(quillLines).map(l => (l as HTMLElement).innerText).join('\n');
                } else if (nestedCode) {
                    text = (nestedCode as HTMLElement).innerText;
                } else {
                    text = el.innerText;
                }
                
                try {
                    const highlighted = hljs.highlight(text, { language: 'java' }).value;
                    
                    // Create a robust structure with inline copy button
                    el.innerHTML = `
                        <div class="terminal-header">
                            <div class="terminal-dots">
                                <span class="dot-red"></span>
                                <span class="dot-yellow"></span>
                                <span class="dot-green"></span>
                            </div>
                            <button class="terminal-copy-btn" title="Salin Kode">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                            </button>
                        </div>
                        <div class="hljs-code-content">${highlighted}</div>
                    `;
                    
                    el.dataset['enhanced'] = 'true';
                    setupCopyLogic(el, text);
                } catch (e) {
                    console.error('Highlight error:', e);
                }
            }
        });

        // 2. Handle Tables
        const tables = container.querySelectorAll('table');
        tables.forEach(table => {
            if (!table.parentElement?.classList.contains('table-responsive-wrapper')) {
                const wrapper = document.createElement('div');
                wrapper.className = 'table-responsive-wrapper';
                table.parentNode?.insertBefore(wrapper, table);
                wrapper.appendChild(table);
            }
        });
    }

    function setupCopyLogic(el: HTMLElement, text: string) {
        const btn = el.querySelector('.terminal-copy-btn') as HTMLButtonElement;
        if (!btn) return;

        btn.onclick = async () => {
            await navigator.clipboard.writeText(text);
            const original = btn.innerHTML;
            btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#34d399" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>`;
            setTimeout(() => btn.innerHTML = original, 2000);
        };
    }
</script>

<div
    bind:this={container}
    id="oopedia-content-v2"
    class="pedagogical-content-root px-6 md:px-10 py-6 {className}"
>
    {@html safeContent}
</div>

<style>
    @reference "../../../css/app.css";

    /* 1. Typography & Layout */
    :global(.pedagogical-content-root) {
        @apply leading-relaxed text-slate-600 font-medium;
        font-family: var(--font-body) !important;
    }

    :global(.pedagogical-content-root h2) {
        @apply text-2xl md:text-3xl font-black text-slate-900 mt-12 mb-6 tracking-widest uppercase border-b-6 border-slate-100 pb-4 block;
    }

    :global(.pedagogical-content-root h3) {
        @apply text-xl font-extrabold text-slate-800 mt-8 mb-4 tracking-wider block;
    }

    /* 2. Tables */
    :global(.table-responsive-wrapper) {
        @apply my-10 overflow-hidden rounded-3xl border-2 border-b-8 border-slate-200 shadow-xl;
    }

    /* 3. High-Contrast Oopedia Pro Code Blocks */
    :global(.pedagogical-content-root .ql-code-block-container, 
    .pedagogical-content-root .ql-syntax, 
    .pedagogical-content-root pre) {
        @apply relative my-8 p-0 rounded-3xl bg-[#0c0c14] border-2 border-b-8 border-slate-900 shadow-2xl overflow-hidden block;
        font-family: var(--font-mono) !important;
    }

    :global(.pedagogical-content-root .hljs-code-content) {
        @apply px-8 pt-4 pb-4 overflow-x-auto;
        line-height: 1.8 !important;
        font-size: 0.95rem !important;
        color: #e2e8f0 !important; /* Brighter main text */
    }

    /* Terminal Header */
    :global(.pedagogical-content-root .terminal-header) {
        @apply h-11 bg-slate-900/60 border-b border-white/5 flex items-center justify-between px-6;
    }

    :global(.pedagogical-content-root .terminal-dots) {
        @apply flex gap-2;
    }

    :global(.pedagogical-content-root .terminal-dots span) {
        @apply w-3 h-3 rounded-full;
    }

    :global(.pedagogical-content-root .dot-red) { @apply bg-[#ff5f56]; }
    :global(.pedagogical-content-root .dot-yellow) { @apply bg-[#ffbd2e]; }
    :global(.pedagogical-content-root .dot-green) { @apply bg-[#27c93f]; }

    /* Inline Copy Button */
    :global(.pedagogical-content-root .terminal-copy-btn) {
        @apply p-2 rounded-lg text-slate-500 hover:text-white hover:bg-white/5 transition-all flex items-center justify-center;
    }

    /* Vibrant Oopedia Syntax Colors (High Contrast) */
    :global(.pedagogical-content-root .hljs-keyword) { color: #ff5242 !important; font-weight: 800 !important; } /* Oopedia Coral Neon */
    :global(.pedagogical-content-root .hljs-string) { color: #34d399 !important; } /* Success Green Neon */
    :global(.pedagogical-content-root .hljs-title.function_) { color: #60a5fa !important; } /* Info Blue Neon */
    :global(.pedagogical-content-root .hljs-title.class_) { color: #fbbf24 !important; } /* Warning Yellow Neon */
    :global(.pedagogical-content-root .hljs-comment) { color: #64748b !important; font-style: italic !important; } /* Muted Slate */
    :global(.pedagogical-content-root .hljs-number) { color: #f97316 !important; } /* Orange */
    :global(.pedagogical-content-root .hljs-attr) { color: #818cf8 !important; }
    :global(.pedagogical-content-root .hljs-type) { color: #fbbf24 !important; }
    :global(.pedagogical-content-root .hljs-meta) { color: #ff5242 !important; }

    /* 4. Blockquote */
    :global(.pedagogical-content-root blockquote) {
        @apply my-10 border-l-8 border-slate-900 bg-slate-50 p-10 italic text-slate-800 rounded-r-3xl shadow-inner;
    }
</style>
