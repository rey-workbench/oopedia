<script lang="ts">
    import { GripVertical } from 'lucide-svelte';

    let { text }: { text: string } = $props();

    function dragStart(e: DragEvent) {
        if (!text) {
            e.preventDefault();
            return;
        }
        e.dataTransfer?.setData('text/plain', `[${text}]`);
        e.dataTransfer?.setData(
            'text/html',
            `<span class="dnd-dropzone inline-block rounded border border-primary-200 bg-primary-50 px-2 py-1 mx-1 text-xs font-bold text-primary-700 shadow-sm" contenteditable="false" data-answer="${text}">[ ${text} ]</span>&nbsp;`
        );
    }
</script>

<span
    role="button"
    tabindex="0"
    class="flex h-6 w-6 cursor-grab items-center justify-center rounded-full bg-slate-200 text-xs font-bold text-slate-500 transition-colors hover:bg-slate-300"
    draggable="true"
    ondragstart={dragStart}
    title="Drag ini ke dalam kotak soal!"
>
    <GripVertical size={14} />
</span>
