import hljs from 'highlight.js';

export function enhanceCodeBlocks(container: HTMLElement) {
    if (!container) return;

    const codeBlocks = container.querySelectorAll('.ql-code-block-container');

    codeBlocks.forEach((block) => {
        const el = block as HTMLElement;

        // Apply Syntax Highlighting
        if (!el.dataset['highlighted']) {
            // Quill structural lines are usually divs. InnerText usually handles this,
            // but we can be more explicit to ensure newlines are preserved.
            const lines = Array.from(el.querySelectorAll('.ql-code-block')).map(
                (line) => (line as HTMLElement).innerText
            );

            const text = lines.length > 0 ? lines.join('\n') : el.innerText;

            const highlighted = hljs.highlightAuto(text).value;
            el.innerHTML = highlighted;
            el.dataset['highlighted'] = 'true';
        }

        // Prevent double injection of copy button
        if (el.querySelector('.copy-btn')) return;

        // Ensure relative positioning for absolute button placement
        el.style.position = 'relative';

        const button = document.createElement('button');
        button.className =
            'copy-btn absolute top-3 right-3 px-3 py-1.5 text-xs font-semibold bg-white/10 hover:bg-white/20 backdrop-blur-sm text-white border border-white/20 rounded-lg shadow-sm transition-all duration-200 z-10 flex items-center gap-2';
        button.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-copy"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
            <span>Copy</span>
        `;
        button.type = 'button';
        button.style.cursor = 'pointer';

        button.addEventListener('click', async () => {
            const text = el.innerText;

            try {
                await navigator.clipboard.writeText(text);
                button.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check"><path d="M20 6 9 17l-5-5"/></svg>
                    <span>Copied!</span>
                `;
                button.classList.add('text-green-400');

                setTimeout(() => {
                    button.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-copy"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                        <span>Copy</span>
                    `;
                    button.classList.remove('text-green-400');
                }, 2000);
            } catch (err) {
                console.error('Failed to copy text: ', err);
                button.textContent = 'Error';
            }
        });

        el.appendChild(button);
    });
}
