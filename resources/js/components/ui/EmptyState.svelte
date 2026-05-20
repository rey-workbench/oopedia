<script lang="ts">
    import type { Snippet } from 'svelte';
    import { FolderOpen } from '@lucide/svelte';

    interface Props {
        title?: string;
        description?: string;
        icon?: any;
        class?: string;
        children?: Snippet;
    }

    let {
        title = 'Data Tidak Ditemukan',
        description = 'Maaf, kami tidak dapat menemukan data yang Anda cari saat ini.',
        icon = FolderOpen,
        class: className = '',
        children,
    }: Props = $props();
</script>

<div
    class="border-cosmos-border flex flex-col items-center justify-center rounded-3xl border-2 border-dashed bg-white px-6 py-20 text-center {className}"
>
    <div
        class="bg-primary-50/50 text-primary-200 mb-8 flex h-24 w-24 items-center justify-center rounded-[2rem] text-4xl"
    >
        {#if typeof icon === 'string'}
            <i class={icon}></i>
        {:else if icon}
            {@const IconComponent = icon as any}
            <IconComponent size={48} strokeWidth={1.5} />
        {/if}
    </div>

    <h3 class="mb-3 text-2xl font-bold tracking-widest text-slate-900 uppercase">
        {title}
    </h3>

    <p class="mx-auto max-w-sm leading-relaxed font-medium text-slate-400">
        {description}
    </p>

    <div class="mt-10">
        {@render children?.()}
    </div>
</div>
