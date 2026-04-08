<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { ChevronRight, Link2 } from 'lucide-svelte';

    let { href = '#', icon: Icon = Link2, active = false, children, ...restProps } = $props();

    const baseClasses =
        'flex items-center gap-4 px-4 py-3.5 rounded-2xl font-bold tracking-tight transition-all duration-200 group';

    const themeClasses = $derived(
        active
            ? 'bg-primary-500 text-white'
            : 'text-cosmos-muted hover:text-primary-500 hover:bg-primary-50'
    );

    const iconContainerClasses = $derived(
        active
            ? 'bg-white/10'
            : 'bg-primary-50 group-hover:bg-primary-100/50'
    );

    const iconClasses = $derived(
        active ? 'text-white' : 'text-cosmos-muted group-hover:text-primary-500'
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
