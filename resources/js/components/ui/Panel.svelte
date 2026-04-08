<script lang="ts">
    import type { Snippet } from 'svelte';

    /**
     * @file Panel.svelte
     * @description A premium reusable dark panel component for sections, heros, and info containers.
     */
    interface Props {
        variant?: 'dark' | 'glass' | 'none';
        rounded?: 'none' | 'xl' | '2xl' | '3xl' | 'full';
        padding?: string;
        glow?: boolean;
        class?: string;
        children?: Snippet;
        [key: string]: any;
    }

    let {
        variant = 'dark',
        rounded = '3xl',
        padding = 'p-8',
        glow = true,
        class: className = '',
        children,
        ...rest
    }: Props = $props();

    const roundedClasses = {
        none: 'rounded-none',
        xl: 'rounded-xl',
        '2xl': 'rounded-2xl',
        '3xl': 'rounded-[2.5rem]',
        full: 'rounded-[3rem]',
    };

    const variantClasses = {
        dark: 'bg-slate-900 text-white',
        glass: 'glass text-slate-900',
        none: '',
    };

    const classes = $derived(
        `relative overflow-hidden border-2 border-cosmos-border transition-all ${roundedClasses[rounded] || roundedClasses['3xl']} ${variantClasses[variant] || variantClasses.dark} ${className}`
    );
</script>

<div class={classes} {...rest}>
    {#if variant === 'dark' && glow}
        <div
            class="bg-primary-500/10 pointer-events-none absolute -top-24 -right-24 h-96 w-96 rounded-full blur-[100px]"
        ></div>
        <div
            class="bg-accent-500/10 pointer-events-none absolute -bottom-24 -left-24 h-96 w-96 rounded-full blur-[100px]"
        ></div>
    {/if}

    <div class="relative z-10 {padding}">
        {@render children?.()}
    </div>
</div>
