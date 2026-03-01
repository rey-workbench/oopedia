<script lang="ts">
    import { FileText } from 'lucide-svelte';

    interface Props {
        items?: string[];
    }

    let { items = [] }: Props = $props();
</script>

<div class="space-y-4 rounded-[2rem] border-2 border-dashed border-slate-200 bg-slate-50 p-8">
    <div class="mb-4 flex items-center gap-3">
        <div
            class="bg-primary-50 text-primary-600 flex h-10 w-10 items-center justify-center rounded-xl"
        >
            <FileText size={20} />
        </div>
        <h3 class="text-sm font-bold tracking-widest text-slate-900 uppercase">Petunjuk Impor</h3>
    </div>
    <ul class="space-y-3 text-xs leading-relaxed font-medium text-slate-600">
        {#each items as item}
            <li class="flex items-start gap-2">
                <span class="text-primary-600 mt-0.5 font-bold">•</span>
                {@html item}
            </li>
        {/each}
    </ul>
</div>
