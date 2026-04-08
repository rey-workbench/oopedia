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

    const baseClasses = 'rounded-2xl transition-all duration-200 overflow-hidden';

    const variantClasses = $derived(
        variant === 'none'
            ? ''
            : variant === 'glass'
              ? 'bg-white/10 backdrop-blur-sm border border-white/20'
              : 'bg-cosmos-bg border border-cosmos-border'
    );

    const shadowClasses = '';

    const hoverClasses = $derived(hover ? 'hover:border-primary-500/30' : '');

    const classes = $derived(
        `${baseClasses} ${variantClasses} ${shadowClasses} ${hoverClasses} ${className}`
    );
</script>

<div class={classes} {...rest}>
    {#if header}
        <div class="border-b border-cosmos-border px-6 py-4">
            <div class="w-full">
                {@render header()}
            </div>
        </div>
    {/if}

    <div class={padding}>
        {@render children?.()}
    </div>

    {#if footer}
        <div class="border-t border-cosmos-border bg-primary-50/30 px-6 py-4">
            {@render footer()}
        </div>
    {/if}
</div>
