<script lang="ts">
    import { onMount } from "svelte";
    import Quill from "quill";
    import hljs from "highlight.js";
    import "quill/dist/quill.snow.css";
    import "highlight.js/styles/atom-one-dark.css";

    // Quill syntax module expects hljs on window
    if (typeof window !== "undefined") {
        (window as any).hljs = hljs;
    }

    interface Props {
        value?: string;
        placeholder?: string;
        height?: string;
        oninput?: (html: string) => void;
    }

    let {
        value = $bindable(""),
        placeholder = "Tulis sesuatu...",
        height = "300px",
        oninput = () => {},
    }: Props = $props();

    let editorContainer: HTMLElement;
    let quill: any;

    onMount(() => {
        quill = new Quill(editorContainer, {
            theme: "snow",
            placeholder: placeholder,
            modules: {
                syntax: true,
                toolbar: [
                    [{ header: [1, 2, 3, false] }],
                    ["bold", "italic", "underline", "strike"],
                    [{ list: "ordered" }, { list: "bullet" }],
                    ["link", "image", "code-block"],
                    ["clean"],
                ],
            },
        });

        // Set initial content
        if (value) {
            quill.root.innerHTML = value;
        }

        quill.on("text-change", () => {
            const html = quill.root.innerHTML;
            value = html;
            oninput(html);
        });
    });
</script>

<div
    class="quill-wrapper bg-white rounded-2xl border border-slate-200 overflow-hidden"
>
    <div bind:this={editorContainer} style="height: {height};"></div>
</div>

<style>
    :global(.ql-toolbar) {
        border-top: none !important;
        border-left: none !important;
        border-right: none !important;
        border-bottom: 1px solid var(--color-primary-100, #e2e8f0) !important;
        background-color: var(--color-primary-50, #f8fafc) !important;
        border-radius: 1.25rem 1.25rem 0 0 !important;
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
        color: var(--color-slate-400, #94a3b8) !important;
        font-style: normal !important;
    }
    :global(
            .ql-snow.ql-toolbar button:hover,
            .ql-snow .ql-toolbar button:focus
        ) {
        color: var(--color-primary-600) !important;
    }
    :global(
            .ql-snow.ql-toolbar button.ql-active,
            .ql-snow .ql-toolbar button.ql-active
        ) {
        color: var(--color-primary-600) !important;
    }
</style>
