<script lang="ts">
    import Panel from '@/components/ui/Panel.svelte';

    interface Props {
        icon: any;
        title: string;
        items?: string[];
    }

    let { icon, title, items = [] }: Props = $props();
</script>

<Panel rounded="3xl" class="border border-slate-800 shadow-2xl shadow-slate-950/20" padding="p-8">
    <div class="flex items-start gap-5">
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/10 shadow-inner">
            {#if typeof icon === 'string'}
                <i class="{icon} text-primary-400"></i>
            {:else}
                {@const IconComponent = icon as any}
                <IconComponent size={24} class="text-primary-400" />
            {/if}
        </div>
        <div class="space-y-4">
            <h4 class="text-sm font-black tracking-[0.2em] text-white uppercase opacity-90">
                {title}
            </h4>
            <ul class="space-y-3">
                {#each items as item (item)}
                    <li class="flex items-center gap-3 text-xs font-bold tracking-wide text-slate-400 transition-colors hover:text-slate-200">
                        <div class="h-1 w-1 shrink-0 rounded-full bg-primary-500/50"></div>
                        <span>{item}</span>
                    </li>
                {/each}
            </ul>
        </div>
    </div>
</Panel>
