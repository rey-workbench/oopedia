<script lang="ts">
    import hljs from 'highlight.js';
    import DOMPurify from 'dompurify';
    import { fade, scale } from 'svelte/transition';
    import { MousePointer2 } from 'lucide-svelte';

    interface Props {
        content: string;
        className?: string;
    }

    let { content = '', className = '' }: Props = $props();

    let container: HTMLElement;
    let showHooray = $state(false);
    let showGhostPointer = $state(false);
    let ghostPos = $state({ x: 0, y: 0 });

    const safeContent = $derived(
        DOMPurify.sanitize(content, {
            ADD_TAGS: [
                'iframe',
                'blockquote',
                'table',
                'thead',
                'tbody',
                'tr',
                'th',
                'td',
                'section',
                'aside',
                'ul',
                'li',
                'ol',
                'span',
                'code',
                'pre',
                'strong',
                'em',
            ],
            ADD_ATTR: ['allow', 'allowfullscreen', 'frameborder', 'scrolling', 'class'],
        })
    );

    $effect(() => {
        if (safeContent && container) {
            processContent();
        }
    });

    function processContent() {
        // 1. Handle Code Blocks with Duolingo-Dark Theme
        const codeBlocks = container.querySelectorAll('pre, code');
        codeBlocks.forEach((block) => {
            const el = block as HTMLElement;
            if (el.tagName === 'CODE' && el.parentElement?.tagName === 'PRE') return;

            if (!el.dataset['enhanced']) {
                const text = el.innerText.trim();
                try {
                    const highlighted = hljs.highlight(text, { language: 'java' }).value;
                    el.innerHTML = `
                        <div class="terminal-header">
                            <div class="terminal-dots">
                                <span class="dot-red"></span>
                                <span class="dot-yellow"></span>
                                <span class="dot-green"></span>
                            </div>
                            <div class="terminal-title">java code</div>
                            <button class="terminal-copy-btn" title="Salin Kode">
                                <span class="copy-icon"></span>
                            </button>
                        </div>
                        <div class="hljs-code-content">${highlighted}</div>
                    `;
                    el.dataset['enhanced'] = 'true';
                    setupCopyLogic(el, text);
                } catch (e) {
                    console.error(e);
                }
            }
        });

        // 2. Handle Tables (Duolingo Style)
        const tables = container.querySelectorAll('table');
        tables.forEach((table) => {
            if (!table.parentElement?.classList.contains('table-responsive-wrapper')) {
                const wrapper = document.createElement('div');
                wrapper.className = 'table-responsive-wrapper';
                table.parentNode?.insertBefore(wrapper, table);
                wrapper.appendChild(table);
            }
        });

        // 3. Handle Blockquotes (Insight Bubble)
        const quotes = container.querySelectorAll('blockquote');
        quotes.forEach((quote) => {
            if (!quote.dataset['enhanced']) {
                const watermark = document.createElement('div');
                watermark.className = 'quote-watermark-lamp';
                watermark.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A5 5 0 0 0 8 8c0 1.3.5 2.6 1.5 3.5.8.8 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/></svg>`;
                quote.appendChild(watermark);
                quote.dataset['enhanced'] = 'true';
            }
        });

        // 4. SMART DETECTION: Duolingo Interactive Checklist
        const allLists = container.querySelectorAll('ul');
        allLists.forEach((ul) => {
            const prevHeading = ul.previousElementSibling;
            const isChecklistHeader =
                prevHeading &&
                (prevHeading.tagName === 'H2' || prevHeading.tagName === 'H3') &&
                (prevHeading.textContent?.toLowerCase().includes('checklist') ||
                    prevHeading.textContent?.toLowerCase().includes('penguasaan'));

            const isLastList = ul === Array.from(allLists).pop();

            if (isChecklistHeader || isLastList) {
                ul.classList.add('duo-checklist');
                const items = ul.querySelectorAll('li');
                items.forEach((li) => {
                    if (!li.dataset['interactive']) {
                        li.addEventListener('click', (e) => {
                            li.classList.toggle('is-completed');
                            checkCompletion(ul as HTMLElement, e);
                        });
                        li.dataset['interactive'] = 'true';
                    }
                });
            }
        });
    }

    function checkCompletion(list: HTMLElement, event: MouseEvent) {
        const total = list.querySelectorAll('li').length;
        const completed = list.querySelectorAll('li.is-completed').length;

        if (total > 0 && total === completed) {
            ghostPos = { x: event.clientX, y: event.clientY };
            triggerHooray();
        }
    }

    function triggerHooray() {
        showHooray = true;
        setTimeout(() => {
            showHooray = false;
            showGhostPointer = true;

            const quizSection = document.getElementById('quiz-entry-section');
            if (quizSection) {
                const quizBtn = quizSection.querySelector('a, button');
                quizSection.scrollIntoView({ behavior: 'smooth', block: 'center' });

                setTimeout(() => {
                    if (quizBtn) {
                        const rect = quizBtn.getBoundingClientRect();
                        ghostPos = { x: rect.left + rect.width / 2, y: rect.top + rect.height / 2 };
                        quizBtn.classList.add('highlight-pulse');

                        setTimeout(() => {
                            const ghostEl = document.querySelector('.ghost-pointer') as HTMLElement;
                            if (ghostEl) ghostEl.style.transform = 'scale(0.7)';
                            setTimeout(() => {
                                showGhostPointer = false;
                                quizBtn.classList.remove('highlight-pulse');
                            }, 500);
                        }, 1000);
                    }
                }, 800);
            }
        }, 1800);
    }

    function setupCopyLogic(el: HTMLElement, text: string) {
        const btn = el.querySelector('.terminal-copy-btn') as HTMLButtonElement;
        const iconContainer = btn?.querySelector('.copy-icon');
        if (!btn || !iconContainer) return;

        iconContainer.innerHTML =
            '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>';

        btn.onclick = async () => {
            await navigator.clipboard.writeText(text);
            iconContainer.innerHTML =
                '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#34d399" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>';
            btn.classList.add('copied');
            setTimeout(() => {
                iconContainer.innerHTML =
                    '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>';
                btn.classList.remove('copied');
            }, 2000);
        };
    }
</script>

<style lang="postcss">
    @reference "../../../css/app.css";

    /* 1. Duolingo Typography */
    :global(.pedagogical-content-root) {
        @apply leading-relaxed font-bold text-slate-700;
        font-family: var(--font-body) !important;
    }

    :global(.pedagogical-content-root h2) {
        @apply mt-20 mb-10 text-3xl font-black tracking-tight text-slate-900 md:text-5xl;
    }
    :global(.pedagogical-content-root h3) {
        @apply mt-12 mb-6 text-2xl font-black text-slate-800;
    }
    :global(.pedagogical-content-root p) {
        @apply mb-8 text-xl leading-[1.8] text-slate-600/90;
    }

    /* 2. Duolingo-Style Checklist (Chunky Buttons) */
    :global(.pedagogical-content-root ul.duo-checklist) {
        @apply my-12 list-none space-y-5 p-0;
    }
    :global(.pedagogical-content-root ul.duo-checklist li) {
        @apply flex cursor-pointer items-center gap-6 rounded-3xl border-2 border-b-6 border-slate-200 bg-white p-6 text-lg font-black text-slate-800 transition-all select-none hover:bg-slate-50 active:translate-y-1 active:border-b-2;
    }
    :global(.pedagogical-content-root ul.duo-checklist li::before) {
        content: '';
        @apply flex h-8 w-8 shrink-0 items-center justify-center rounded-xl border-4 border-slate-200 bg-white transition-all;
    }
    :global(.pedagogical-content-root ul.duo-checklist li.is-completed) {
        @apply border-b-6 border-emerald-400 bg-emerald-50 text-emerald-900 shadow-none;
    }
    :global(.pedagogical-content-root ul.duo-checklist li.is-completed::before) {
        content: '✓';
        @apply scale-110 border-emerald-500 bg-emerald-500 text-[16px] font-black text-white;
    }

    /* 3. Chunky Terminal Blocks */
    :global(.pedagogical-content-root pre) {
        @apply relative my-12 flex flex-col overflow-hidden rounded-[2rem] border-2 border-b-8 border-slate-900 bg-[#1a1a2e] p-0 shadow-2xl;
    }
    :global(.terminal-header) {
        @apply flex h-14 items-center justify-between border-b-2 border-white/5 bg-white/5 px-6;
    }
    :global(.terminal-dots) {
        @apply flex items-center gap-2;
    }
    :global(.terminal-dots span) {
        @apply h-3.5 w-3.5 rounded-full;
    }
    :global(.dot-red) {
        @apply bg-[#ff4b4b];
    }
    :global(.dot-yellow) {
        @apply bg-[#ffc800];
    }
    :global(.dot-green) {
        @apply bg-[#58cc02];
    }

    :global(.terminal-title) {
        @apply absolute left-1/2 -translate-x-1/2 text-[12px] font-black tracking-[0.3em] text-white/30 uppercase select-none;
    }
    :global(.terminal-copy-btn) {
        @apply flex h-10 w-10 items-center justify-center rounded-xl bg-white/5 text-white/40 transition-all hover:bg-white/10 hover:text-white;
    }

    /* Syntax Highlighting */
    :global(.hljs-code-content) {
        @apply overflow-x-auto px-10 py-8 font-mono text-[16px] leading-[1.7] text-[#e0e0e0];
    }
    :global(.hljs-keyword) {
        @apply text-[#ff79c6];
    }
    :global(.hljs-title) {
        @apply text-[#50fa7b];
    }
    :global(.hljs-string) {
        @apply text-[#f1fa8c];
    }
    :global(.hljs-comment) {
        @apply text-[#6272a4] italic;
    }

    /* 4. Duolingo-Style Tables */
    :global(.table-responsive-wrapper) {
        @apply my-12 overflow-x-auto rounded-[2rem] border-2 border-b-8 border-slate-200 shadow-xl;
    }
    :global(.pedagogical-content-root table) {
        @apply min-w-full border-collapse bg-white text-left;
    }
    :global(.pedagogical-content-root th) {
        @apply border-b-2 border-slate-100 bg-slate-50 px-8 py-6 text-[12px] font-black tracking-widest text-slate-500 uppercase;
    }
    :global(.pedagogical-content-root td) {
        @apply border-b border-slate-50 px-8 py-6 text-[16px] font-extrabold text-slate-800;
    }

    /* 5. Insight Bubbles */
    :global(.pedagogical-content-root blockquote) {
        @apply relative my-12 overflow-hidden rounded-3xl border-2 border-b-8 border-sky-200 bg-sky-50 p-10 text-sky-900 italic;
    }
    :global(.quote-watermark-lamp) {
        @apply pointer-events-none absolute -right-4 -bottom-10 h-52 w-52 -rotate-12 text-sky-500/10;
    }

    :global(.highlight-pulse) {
        animation: highlightPulse 2s infinite !important;
    }
    @keyframes highlightPulse {
        0% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(88, 204, 2, 0.4);
        }
        70% {
            transform: scale(1.08);
            box-shadow: 0 0 0 25px rgba(88, 204, 2, 0);
        }
        100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(88, 204, 2, 0);
        }
    }
</style>

<div
    bind:this={container}
    id="oopedia-content-v5"
    class="pedagogical-content-root px-6 py-10 md:px-16 {className}"
>
    {@html safeContent}
</div>

{#if showHooray}
    <div
        class="pointer-events-none fixed inset-0 z-9999 flex flex-col items-center justify-center bg-white/40 backdrop-blur-sm"
        transition:fade={{ duration: 300 }}
    >
        <div in:scale={{ start: 0.8, duration: 600, delay: 100 }}>
            <h1
                class="text-center text-7xl font-black tracking-tighter text-emerald-500 italic drop-shadow-xl select-none md:text-9xl"
            >
                Hooray!
            </h1>
            <p class="mt-4 text-center text-2xl font-bold text-emerald-700 md:text-3xl">
                Kamu sudah paham materi ini!
            </p>
        </div>
    </div>
{/if}

{#if showGhostPointer}
    <div
        class="ghost-pointer pointer-events-none fixed z-10000 transition-all duration-1200 ease-[cubic-bezier(0.34,1.56,0.64,1)]"
        style="left: {ghostPos.x}px; top: {ghostPos.y}px;"
        transition:fade
    >
        <div class="relative flex items-center justify-center">
            <MousePointer2
                size={42}
                class="rotate-15 fill-emerald-500 text-white drop-shadow-[0_8px_16px_rgba(0,0,0,0.2)]"
            />
            <div
                class="absolute inset-0 animate-pulse rounded-full bg-emerald-400/20 blur-2xl"
            ></div>
        </div>
    </div>
{/if}
