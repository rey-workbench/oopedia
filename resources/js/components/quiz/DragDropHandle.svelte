<script lang="ts">
    import { GripVertical } from 'lucide-svelte';

    interface Props {
        data: string;
    }

    let { data }: Props = $props();

    function dragStart(event: DragEvent) {
        if (event.dataTransfer) {
            event.dataTransfer.setData('text/plain', data);
            event.dataTransfer.effectAllowed = 'copy';
        }
    }
</script>

<span
    role="button"
    tabindex="0"
    class="drag-handle-item hover:bg-primary-50 hover:text-primary-600 flex h-8 w-8 cursor-grab items-center justify-center rounded-xl bg-slate-100 text-slate-400 shadow-sm transition-all hover:scale-110 active:cursor-grabbing"
    draggable="true"
    ondragstart={dragStart}
    title="Drag ini ke dalam kotak soal!"
>
    <GripVertical size={16} />
</span>
