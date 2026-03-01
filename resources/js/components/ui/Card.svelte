<script lang="ts">
    import type { Snippet } from 'svelte';

    interface Props {
        variant?: 'default' | 'glass' | 'none';
        shadow?: boolean;
        hover?: boolean;
        padding?: string;
        class?: string;
        children?: Snippet;
        header?: Snippet;
        footer?: Snippet;
        [key: string]: any;
    }

    let {
        variant = 'default',
        shadow = true,
        hover = true,
        padding = 'p-6',
        class: className = '',
        children,
        header,
        footer,
        ...rest
    }: Props = $props();

    const baseClasses = 'rounded-2xl transition-all duration-300 overflow-hidden';

    const variantClasses = $derived(
        variant === 'none' ? '' : variant === 'glass' ? 'glass' : 'bg-white border border-slate-100'
    );

    const shadowClasses = $derived(
        shadow
            ? variant === 'none'
                ? ''
                : variant === 'glass'
                  ? 'shadow-premium'
                  : 'shadow-soft'
            : ''
    );

    const hoverClasses = $derived(
        hover ? 'hover:shadow-premium hover:shadow-accent-950/10 hover:-translate-y-1' : ''
    );

    const classes = $derived(
        `${baseClasses} ${variantClasses} ${shadowClasses} ${hoverClasses} ${className}`
    );
</script>

<div class={classes} {...rest}>
    {#if header}
        <div class="flex items-center justify-between border-b border-slate-50 px-6 py-4">
            <div class="w-full">
                {@render header()}
            </div>
        </div>
    {/if}

    <div class={padding}>
        {@render children?.()}
    </div>

    {#if footer}
        <div class="border-t border-slate-100 bg-slate-50 px-6 py-4">
            {@render footer()}
        </div>
    {/if}
</div>
