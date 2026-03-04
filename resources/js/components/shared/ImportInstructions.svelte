<script lang="ts">
    import { FileText, ChevronRight } from 'lucide-svelte';
    import Panel from '@/components/ui/Panel.svelte';

    interface Props {
        items?: string[];
    }

    let { items = [] }: Props = $props();
</script>

<Panel variant="none" rounded="3xl" padding="p-10" class="border-2 border-dashed border-slate-200 bg-slate-50/50 hover:border-primary-300 hover:bg-white transition-all duration-500 group">
    <div class="mb-8 flex items-center gap-4">
        <div
            class="bg-primary-600 text-white flex h-14 w-14 items-center justify-center rounded-2xl shadow-xl shadow-primary-100 group-hover:rotate-6 transition-transform"
        >
            <FileText size={28} />
        </div>
        <div>
            <h3 class="text-xs font-black tracking-widest text-slate-500 uppercase flex items-center gap-2">
                Dokumentasi <div class="h-px w-8 bg-slate-200"></div>
            </h3>
            <h2 class="text-xl font-black tracking-tight text-slate-900 group-hover:text-primary-700 transition-colors">Petunjuk Impor Data</h2>
        </div>
    </div>
    
    <ul class="space-y-4">
        {#each items as item}
            <li class="flex items-start gap-4 p-4 rounded-2xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">
                <div class="mt-1 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-primary-100 text-primary-600">
                    <ChevronRight size={14} />
                </div>
                <div class="text-sm font-semibold text-slate-700 leading-relaxed uppercase tracking-tight selection:bg-primary-100">
                    {@html item}
                </div>
            </li>
        {/each}
    </ul>
    
    {#if items.length === 0}
        <p class="text-center py-4 text-xs font-bold tracking-widest text-slate-400 uppercase italic">
            Memuat instruksi...
        </p>
    {/if}
</Panel>
