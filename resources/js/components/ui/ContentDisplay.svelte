<script>
    import { onMount, tick } from "svelte";
    import { enhanceCodeBlocks } from "../../utils/codeBlockEnhancer";
    import "highlight.js/styles/atom-one-dark.css";

    export let content = "";

    let contentContainer;

    $: if (content && contentContainer) {
        tick().then(() => enhanceCodeBlocks(contentContainer));
    }
</script>

<div
    class="prose prose-slate max-w-none
    prose-headings:font-display prose-headings:font-extrabold prose-headings:tracking-tight prose-headings:text-slate-900
    prose-p:text-slate-600 prose-p:leading-relaxed prose-p:font-sans
    prose-a:text-primary-600 prose-a:font-bold prose-a:no-underline hover:prose-a:underline
    prose-strong:text-slate-900 prose-strong:font-bold
    prose-ul:list-disc prose-ul:pl-5
    "
    bind:this={contentContainer}
>
    {@html content || ""}
</div>

<style>
    /* Force fonts from global theme */
    :global(.prose) {
        font-family: var(--font-sans, "Inter", sans-serif);
    }

    :global(.prose h1, .prose h2, .prose h3, .prose h4) {
        font-family: var(--font-display, "Poppins", sans-serif);
    }

    :global(code) {
        font-family: "JetBrains Mono", "Consolas", "Monaco", monospace !important;
    }

    /* Inline code style - matching theme */
    :global(p code),
    :global(li code),
    :global(span code) {
        background-color: var(--color-primary-50, #f0f7ff) !important;
        color: var(--color-primary-600, #026fb1) !important;
        padding: 0.2rem 0.4rem !important;
        border-radius: 0.5rem !important;
        font-size: 0.875em !important;
        font-weight: 700 !important;
        border: 1px solid var(--color-primary-100, #e0effe) !important;
    }

    /* Quill Code Block Styling - Integration with global theme */
    :global(.ql-code-block-container) {
        background-color: #282c34 !important; /* Atom One Dark background */
        color: #abb2bf !important;
        padding: 2.5rem 1.5rem 1.5rem 1.5rem !important;
        border-radius: 1rem !important;
        border: 1px solid rgba(0, 0, 0, 0.2) !important;
        border-left: 6px solid var(--color-primary-500, #0e8ad9) !important;
        font-family: "JetBrains Mono", "Fira Code", "Consolas", monospace !important;
        position: relative !important;
        margin-top: 2rem !important;
        margin-bottom: 2rem !important;
        overflow-x: auto !important;
        white-space: pre !important;
        display: block !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4) !important;
        line-height: 1.6 !important;
        font-size: 0.9rem !important;
        tab-size: 4 !important;
    }

    /* Decorative dots (macOS style) */
    :global(.ql-code-block-container::before) {
        content: "";
        position: absolute;
        top: 0.85rem;
        left: 1.1rem;
        width: 3.2rem;
        height: 0.75rem;
        background-image: radial-gradient(
                circle,
                #ff5f56 0.25rem,
                transparent 0.25rem
            ),
            radial-gradient(circle, #ffbd2e 0.25rem, transparent 0.25rem),
            radial-gradient(circle, #27c93f 0.25rem, transparent 0.25rem);
        background-size: 1rem 1rem;
        background-position:
            0 0,
            1rem 0,
            2rem 0;
        background-repeat: no-repeat;
        opacity: 0.8;
    }

    :global(.ql-code-block) {
        background-color: transparent !important;
        color: inherit !important;
        padding: 0 !important;
        font-family: inherit !important;
    }

    /* Blockquote styling */
    :global(.prose blockquote) {
        border-left: 4px solid var(--color-primary-500) !important;
        background-color: var(--color-primary-50) !important;
        color: var(--color-primary-800) !important;
        padding: 1rem 1.5rem !important;
        border-radius: 0 1rem 1rem 0 !important;
        font-style: italic !important;
        font-weight: 500 !important;
    }

    /* Images within content */
    :global(.prose img) {
        border-radius: 1.5rem !important;
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1) !important;
        margin: 2rem auto !important;
    }

    /* Lists item spacing */
    :global(.prose li) {
        margin-top: 0.5rem !important;
        margin-bottom: 0.5rem !important;
    }
</style>
