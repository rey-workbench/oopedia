<script lang="ts">
    import { FileText, ChevronRight } from '@lucide/svelte';
    import Panel from '@/components/ui/Panel.svelte';

    interface Props {
        items?: string[];
    }

    let { items = [] }: Props = $props();
</script>

<Panel
    variant="none"
    rounded="3xl"
    padding="p-10"
    class="hover:border-primary-300 group border-2 border-dashed border-slate-200 bg-slate-50/50 transition-all duration-500 hover:bg-white"
>
    <div class="mb-8 flex items-center gap-4">
        <div
            class="bg-primary-600 shadow-primary-100 flex h-14 w-14 items-center justify-center rounded-2xl text-white shadow-xl transition-transform group-hover:rotate-6"
        >
            <FileText size={28} />
        </div>
        <div>
            <h3
                class="flex items-center gap-2 text-xs font-black tracking-widest text-slate-500 uppercase"
            >
                Dokumentasi <div class="h-px w-8 bg-slate-200"></div>
            </h3>
            <h2
                class="group-hover:text-primary-700 text-xl font-black tracking-tight text-slate-900 transition-colors"
            >
                Petunjuk Impor Data
            </h2>
        </div>
    </div>

    <ul class="space-y-4">
        {#each items as item}
            <li
                class="flex items-start gap-4 rounded-2xl border border-transparent p-4 transition-colors hover:border-slate-100 hover:bg-slate-50"
            >
                <div
                    class="bg-primary-100 text-primary-600 mt-1 flex h-5 w-5 shrink-0 items-center justify-center rounded-full"
                >
                    <ChevronRight size={14} />
                </div>
                <div
                    class="selection:bg-primary-100 text-sm leading-relaxed font-semibold tracking-tight text-slate-700 uppercase"
                >
                    {@html item}
                </div>
            </li>
        {/each}
    </ul>

    {#if items.length === 0}
        <p
            class="py-4 text-center text-xs font-bold tracking-widest text-slate-400 uppercase italic"
        >
            Memuat instruksi...
        </p>
    {/if}
</Panel>
