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

    const safeContent = $derived(DOMPurify.sanitize(content, {
        ADD_TAGS: ['iframe', 'blockquote', 'table', 'thead', 'tbody', 'tr', 'th', 'td', 'section', 'aside', 'ul', 'li', 'ol', 'span'],
        ADD_ATTR: ['allow', 'allowfullscreen', 'frameborder', 'scrolling', 'class']
    }));

    $effect(() => {
        if (safeContent && container) {
            processContent();
        }
    });

    function processContent() {
        // 1. Handle Code Blocks
        const codeBlocks = container.querySelectorAll('.ql-code-block-container, .ql-syntax, pre');
        codeBlocks.forEach((block) => {
            const el = block as HTMLElement;
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
                            <button class="terminal-copy-btn" title="Salin Kode">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                            </button>
                        </div>
                        <div class="hljs-code-content">${highlighted}</div>
                    `;
                    el.dataset['enhanced'] = 'true';
                    setupCopyLogic(el, text);
                } catch (e) { console.error(e); }
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

        // 3. Handle Blockquotes (Watermark Lamp)
        const quotes = container.querySelectorAll('blockquote');
        quotes.forEach(quote => {
            if (!quote.dataset['enhanced']) {
                const watermark = document.createElement('div');
                watermark.className = 'quote-watermark-lamp';
                watermark.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A5 5 0 0 0 8 8c0 1.3.5 2.6 1.5 3.5.8.8 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/></svg>`;
                quote.appendChild(watermark);
                quote.dataset['enhanced'] = 'true';
            }
        });

        // 4. SMART DETECTION: Detect Checklist automatically
        const allLists = container.querySelectorAll('ul');
        allLists.forEach(ul => {
            const prevHeading = ul.previousElementSibling;
            const isChecklistHeader = prevHeading && (prevHeading.tagName === 'H2' || prevHeading.tagName === 'H3') && 
                                     (prevHeading.textContent?.toLowerCase().includes('checklist') || 
                                      prevHeading.textContent?.toLowerCase().includes('penguasaan'));
            
            const isLastList = ul === Array.from(allLists).pop();

            if (isChecklistHeader || isLastList) {
                ul.classList.add('detected-checklist');
                const items = ul.querySelectorAll('li');
                items.forEach(li => {
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
                            if (ghostEl) ghostEl.style.transform = 'scale(0.8)';
                            setTimeout(() => {
                                showGhostPointer = false;
                                quizBtn.classList.remove('highlight-pulse');
                            }, 500);
                        }, 1000);
                    }
                }, 800);
            }
        }, 1500);
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
    id="oopedia-content-v3"
    class="pedagogical-content-root px-6 md:px-10 py-6 {className}"
>
    {@html safeContent}
</div>

{#if showHooray}
    <div 
        class="fixed inset-0 z-[9999] flex items-center justify-center pointer-events-none"
        transition:fade={{ duration: 200 }}
    >
        <h1 
            class="text-6xl md:text-8xl font-black text-primary-500 drop-shadow-[0_10px_20px_rgba(255,82,66,0.3)] select-none italic tracking-tighter text-center"
            transition:scale={{ start: 0.5, duration: 400 }}
        >
            Hooray! Kamu Paham
        </h1>
    </div>
{/if}

{#if showGhostPointer}
    <div 
        class="ghost-pointer fixed z-[10000] pointer-events-none transition-all duration-[1000ms] ease-in-out"
        style="left: {ghostPos.x}px; top: {ghostPos.y}px;"
        transition:fade
    >
        <div class="relative flex items-center justify-center">
            <MousePointer2 size={48} class="text-white fill-black/20 drop-shadow-[0_4px_12px_rgba(0,0,0,0.3)] rotate-[15deg]" />
            <div class="absolute inset-0 bg-white/20 rounded-full blur-xl animate-pulse"></div>
        </div>
    </div>
{/if}

<style>
    @reference "../../../css/app.css";

    /* 1. Core Typography */
    :global(.pedagogical-content-root) {
        @apply leading-relaxed text-slate-600 font-medium;
        font-family: var(--font-body) !important;
    }

    :global(.pedagogical-content-root h2) { @apply text-2xl md:text-3xl font-black text-slate-900 mt-12 mb-6 tracking-widest uppercase border-b-6 border-slate-100 pb-4 block; }
    :global(.pedagogical-content-root h3) { @apply text-xl font-extrabold text-slate-800 mt-8 mb-4 tracking-wider block; }
    :global(.pedagogical-content-root p) { @apply mb-6 text-lg; }

    /* 2. Detected Checklist Styles */
    :global(.pedagogical-content-root ul.detected-checklist) { @apply space-y-3 my-8 p-0 list-none; }
    :global(.pedagogical-content-root ul.detected-checklist li) { 
        @apply flex items-center gap-4 p-5 bg-white text-slate-700 rounded-2xl font-bold border-2 border-slate-100 cursor-pointer transition-all hover:bg-slate-50 hover:border-primary-200 select-none; 
    }
    :global(.pedagogical-content-root ul.detected-checklist li::before) { 
        content: ""; 
        @apply w-6 h-6 rounded-full border-2 border-slate-300 flex-shrink-0 flex items-center justify-center transition-all bg-white;
    }
    :global(.pedagogical-content-root ul.detected-checklist li.is-completed) { 
        @apply bg-primary-50/50 text-primary-900 border-primary-200 shadow-sm scale-[0.98]; 
    }
    :global(.pedagogical-content-root ul.detected-checklist li.is-completed::before) { 
        content: "✓"; 
        @apply bg-primary-500 border-primary-500 text-white text-[12px] font-black; 
    }

    /* Standard Lists */
    :global(.pedagogical-content-root ul:not(.detected-checklist)) { @apply list-disc pl-8 mb-6 space-y-2; }

    /* 3. Code Blocks */
    :global(.pedagogical-content-root pre) {
        @apply relative my-8 p-0 rounded-3xl bg-[#0c0c14] border-2 border-b-8 border-slate-900 shadow-2xl overflow-hidden flex flex-col;
    }
    :global(.hljs-code-content) { @apply px-8 pt-4 pb-4 overflow-x-auto text-[#e2e8f0] text-sm leading-relaxed; }
    :global(.terminal-header) { @apply h-11 bg-slate-900/60 border-b border-white/5 flex items-center justify-between pl-6 pr-3; }
    :global(.terminal-dots) { @apply flex items-center gap-1.5; }
    :global(.terminal-dots span) { @apply w-3 h-3 rounded-full; }
    :global(.dot-red) { @apply bg-[#ff5f56]; }
    :global(.dot-yellow) { @apply bg-[#ffbd2e]; }
    :global(.dot-green) { @apply bg-[#27c93f]; }

    /* 4. Tables & Quotes */
    :global(.pedagogical-content-root table) { @apply min-w-full bg-white text-left border-collapse rounded-3xl overflow-hidden my-10 border-2 border-slate-200; }
    :global(.pedagogical-content-root th) { @apply px-8 py-5 bg-slate-50 font-black text-slate-600 uppercase tracking-widest text-[11px] border-b-2 border-slate-200; }
    :global(.pedagogical-content-root td) { @apply px-8 py-5 border-b border-slate-100 text-slate-700 text-sm font-semibold; }
    :global(.pedagogical-content-root blockquote) { @apply my-8 bg-slate-50 border-y border-r border-slate-100 border-l-8 border-primary-500 p-8 italic text-slate-700 rounded-3xl relative overflow-hidden; }
    :global(.quote-watermark-lamp) { @apply absolute -right-6 -bottom-8 w-40 h-40 text-primary-500/15 -rotate-12 pointer-events-none; }

    /* Interactive Effects */
    :global(.highlight-pulse) { animation: highlightPulse 2s infinite !important; }
    @keyframes highlightPulse {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 82, 66, 0.5); }
        70% { transform: scale(1.08); box-shadow: 0 0 0 20px rgba(255, 82, 66, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 82, 66, 0); }
    }
</style>
