<script lang="ts">
    import type { Snippet } from 'svelte';

    interface Props {
        variant?: 'default' | 'glass' | 'none';
        padding?: string;
        class?: string;
        children?: Snippet;
        header?: Snippet;
        footer?: Snippet;
        [key: string]: any;
    }

    let {
        variant = 'default',
        padding = 'p-6',
        class: className = '',
        children,
        header,
        footer,
        ...rest
    }: Props = $props();

    const baseClasses =
        'rounded-3xl transition-all duration-300 overflow-hidden border-2 border-b-6 border-slate-200';

    const variantClasses = $derived(
        variant === 'none'
            ? 'border-0 border-b-0'
            : variant === 'glass'
              ? 'bg-white/10 backdrop-blur-sm border-white/20 border-b-white/40'
              : 'bg-white border-slate-200 border-b-slate-300'
    );

    const classes = $derived(`${baseClasses} ${variantClasses} ${className}`);
</script>

<div class={classes} {...rest}>
    {#if header}
        <div class="border-cosmos-border border-b-2 px-6 py-5">
            <div class="w-full">
                {@render header()}
            </div>
        </div>
    {/if}

    <div class={padding}>
        {@render children?.()}
    </div>

    {#if footer}
        <div class="border-cosmos-border bg-primary-50/50 border-t-2 px-6 py-4">
            {@render footer()}
        </div>
    {/if}
</div>
