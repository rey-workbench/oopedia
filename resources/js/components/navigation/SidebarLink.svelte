<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { ChevronRight, Link2 } from 'lucide-svelte';
    import type { Snippet } from 'svelte';

    interface Props {
        id?: string | undefined;
        href?: string | undefined;
        icon?: any | undefined;
        active?: boolean | undefined;
        children?: Snippet | undefined;
        [key: string]: any;
    }

    let {
        id,
        href = '#',
        icon: Icon = Link2 as any,
        active = false,
        children,
        ...restProps
    }: Props = $props();

    const baseClasses =
        'flex items-center gap-4 px-4 py-3 rounded-2xl font-bold tracking-tight transition-all duration-100 group border-2 border-transparent border-b-4 active:translate-y-[2px] active:border-b-0 select-none';

    const themeClasses = $derived(
        active
            ? 'bg-primary-500 text-white border-primary-600 border-b-primary-700 translate-y-[2px] border-b-2'
            : 'text-slate-500 hover:text-primary-500 hover:bg-slate-50 hover:border-slate-200'
    );

    const iconContainerClasses = $derived(
        active ? 'bg-white/10' : 'bg-primary-50 group-hover:bg-primary-100/50'
    );

    const iconClasses = $derived(
        active ? 'text-white' : 'text-cosmos-muted group-hover:text-primary-500'
    );
</script>

<Link
    {id}
    {href}
    class="{baseClasses} {themeClasses}"
    aria-current={active ? 'page' : undefined}
    {...restProps}
>
    <div
        class="flex h-8 w-8 items-center justify-center rounded-xl {iconContainerClasses} shadow-sm transition-colors duration-300"
    >
        {#if typeof Icon === 'string'}
            <i class="{Icon} {iconClasses} transition-colors"></i>
        {:else}
            <div class={iconClasses}>
                <Icon size={18} strokeWidth={3} />
            </div>
        {/if}
    </div>
    <span class="flex-1 font-black">{@render children?.()}</span>

    {#if active}
        <ChevronRight size={14} class="opacity-50" />
    {/if}
</Link>
