<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { ChevronRight, Link2 } from 'lucide-svelte';

    let { href = '#', icon: Icon = Link2, active = false, children, ...restProps } = $props();

    const baseClasses =
        'flex items-center gap-4 px-4 py-3.5 rounded-2xl font-bold tracking-tight transition-all duration-300 group';

    const themeClasses = $derived(
        active
            ? 'bg-primary-600 text-white shadow-xl shadow-accent-500/20 '
            : 'text-slate-500 hover:text-accent-600 hover:bg-accent-50'
    );

    const iconContainerClasses = $derived(
        active
            ? 'bg-accent-500/20 shadow-[0_0_15px_rgba(124,58,237,0.3)]'
            : 'bg-gray-100 group-hover:bg-accent-100'
    );

    const iconClasses = $derived(
        active ? 'text-accent-400' : 'text-slate-400 group-hover:text-accent-600'
    );
</script>

<Link
    {href}
    class="{baseClasses} {themeClasses}"
    aria-current={active ? 'page' : undefined}
    {...restProps}
>
    <div
        class="flex h-8 w-8 items-center justify-center rounded-xl {iconContainerClasses} transition-colors duration-300"
    >
        {#if typeof Icon === 'string'}
            <i class="{Icon} {iconClasses} transition-colors"></i>
        {:else}
            <div class={iconClasses}>
                <Icon size={18} strokeWidth={2.5} />
            </div>
        {/if}
    </div>
    <span class="flex-1">{@render children?.()}</span>

    {#if active}
        <ChevronRight size={14} class="opacity-50" />
    {/if}
</Link>
