<script>
    import { onMount, createEventDispatcher } from "svelte";
    import Quill from "quill";
    import "quill/dist/quill.snow.css";

    export let value = "";
    export let placeholder = "Tulis sesuatu...";
    export let height = "300px";

    let editorContainer;
    let quill;
    const dispatch = createEventDispatcher();

    onMount(() => {
        quill = new Quill(editorContainer, {
            theme: "snow",
            placeholder: placeholder,
            modules: {
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
            dispatch("input", html);
        });
    });

    // Watch for external value changes (optional, but good for reactivity)
    $: if (quill && value !== quill.root.innerHTML) {
        // careful with cursor position here, but for simple init it's ok
        // quill.root.innerHTML = value;
    }
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
        border-bottom: 1px solid #e2e8f0 !important;
        background-color: #f8fafc;
    }
    :global(.ql-container) {
        border: none !important;
        font-size: 1rem;
    }
    :global(.ql-editor) {
        padding: 1.5rem;
        font-family: inherit;
    }
</style>
