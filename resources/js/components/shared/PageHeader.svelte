<script lang="ts">
    import type { Snippet } from 'svelte';

    interface Props {
        title: string;
        subtitle?: string | null;
        centered?: boolean;
        class?: string;
        actions?: Snippet;
        children?: Snippet;
    }

    let {
        title,
        subtitle = null,
        centered = false,
        class: className = '',
        actions,
        children,
    }: Props = $props();

    const classes = $derived(`${centered ? 'text-center' : ''} mb-8 ${className}`);
</script>

<div class={classes}>
    <h1
        class="font-display text-3xl leading-tight font-extrabold tracking-tight text-slate-900 md:text-4xl"
    >
        {title}
    </h1>

    <div
        class="mt-3 flex items-center gap-2 {centered ? 'justify-center' : ''}"
        role="presentation"
    >
        <div class="bg-primary-600 h-1.5 w-12 rounded-full"></div>
        <div class="h-1.5 w-4 rounded-full bg-slate-200"></div>
        <div class="h-1.5 w-2 rounded-full bg-slate-100"></div>
    </div>

    {#if subtitle}
        <p
            class="mt-4 max-w-3xl leading-relaxed font-medium text-slate-500 {centered
                ? 'mx-auto'
                : ''}"
        >
            {subtitle}
        </p>
    {/if}

    {#if actions || children}
        <div class="mt-6 flex flex-wrap gap-4 {centered ? 'justify-center' : ''}">
            {@render actions?.()}
            {@render children?.()}
        </div>
    {/if}
</div>
