<script lang="ts">
    interface Props {
        icon: any;
        title: string;
        items?: string[];
    }

    let { icon, title, items = [] }: Props = $props();
</script>

<div class="relative overflow-hidden rounded-[2rem] bg-slate-900 p-8 text-white">
    <div class="bg-primary-500/10 absolute -top-10 -right-10 h-32 w-32 rounded-full blur-2xl"></div>
    <div class="relative z-10 flex items-start gap-4">
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/10">
            {#if typeof icon === 'string'}
                <i class="{icon} text-primary-400"></i>
            {:else}
                {@const IconComponent = icon as any}
                <IconComponent size={24} class="text-primary-400" />
            {/if}
        </div>
        <div>
            <h4 class="mb-3 text-sm font-bold tracking-widest uppercase">
                {title}
            </h4>
            <ul class="space-y-2 text-xs leading-relaxed font-medium text-slate-400">
                {#each items as item}
                    <li>• {item}</li>
                {/each}
            </ul>
        </div>
    </div>
</div>
