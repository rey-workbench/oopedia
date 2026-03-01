<script lang="ts">
    import { FileText } from "lucide-svelte";

    interface Props {
        items?: string[];
    }

    let { items = [] }: Props = $props();
</script>

<div
    class="p-8 bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-200 space-y-4"
>
    <div class="flex items-center gap-3 mb-4">
        <div
            class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center"
        >
            <FileText size={20} />
        </div>
        <h3 class="font-bold text-slate-900 uppercase tracking-widest text-sm">
            Petunjuk Impor
        </h3>
    </div>
    <ul class="space-y-3 text-xs font-medium text-slate-600 leading-relaxed">
        {#each items as item}
            <li class="flex items-start gap-2">
                <span class="text-primary-600 font-bold mt-0.5">•</span>
                {@html item}
            </li>
        {/each}
    </ul>
</div>
