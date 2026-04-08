<script lang="ts">
    import type { Snippet } from 'svelte';

    interface Breadcrumb {
        label: string;
        href?: string;
    }

    interface Props {
        title: string;
        subtitle?: string | null;
        centered?: boolean;
        class?: string;
        breadcrumbs?: Breadcrumb[];
        actions?: Snippet;
        children?: Snippet;
    }

    let {
        title,
        subtitle = null,
        centered = false,
        class: className = '',
        breadcrumbs = [],
        actions,
        children,
    }: Props = $props();

    const classes = $derived(`${centered ? 'text-center' : ''} mb-8 ${className}`);
</script>

<div class={classes}>
    {#if breadcrumbs.length > 0}
        <nav class="mb-4 flex items-center gap-2 text-xs font-black tracking-widest uppercase">
            {#each breadcrumbs as breadcrumb, i}
                {#if i > 0}
                    <span class="text-slate-300">/</span>
                {/if}
                {#if breadcrumb.href}
                    <a
                        href={breadcrumb.href}
                        class="hover:text-primary-600 text-slate-400 transition-colors"
                    >
                        {breadcrumb.label}
                    </a>
                {:else}
                    <span class="text-slate-900">{breadcrumb.label}</span>
                {/if}
            {/each}
        </nav>
    {/if}

    <h1
        class="font-display text-3xl leading-tight font-extrabold tracking-tight text-slate-900 md:text-4xl"
    >
        {title}
    </h1>

    <div
        class="mt-3 flex items-center gap-2 {centered ? 'justify-center' : ''}"
        role="presentation"
    >
        <div class="h-2 w-16 rounded-full bg-slate-900"></div>
        <div class="h-2 w-5 rounded-full bg-slate-200"></div>
        <div class="h-2 w-3 rounded-full bg-slate-100"></div>
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
