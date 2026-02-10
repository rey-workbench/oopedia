<script>
    export let title = "";
    export let subtitle = null;
    export let centered = false;
    let className = "";
    export { className as class };

    $: classes = `${centered ? "text-center" : ""} mb-8 ${className}`;
</script>

<div class={classes} {...$$restProps}>
    <h1
        class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 leading-tight font-display"
    >
        {title}
    </h1>

    <div
        class="flex items-center gap-2 mt-3 {centered ? 'justify-center' : ''}"
        role="presentation"
    >
        <div class="h-1.5 w-12 bg-blue-600 rounded-full"></div>
        <div class="h-1.5 w-4 bg-slate-200 rounded-full"></div>
        <div class="h-1.5 w-2 bg-slate-100 rounded-full"></div>
    </div>

    {#if subtitle}
        <p
            class="mt-4 text-slate-500 font-medium leading-relaxed max-w-3xl {centered
                ? 'mx-auto'
                : ''}"
        >
            {subtitle}
        </p>
    {/if}

    {#if $$slots.actions || $$slots.default}
        <div
            class="mt-6 flex flex-wrap gap-4 {centered ? 'justify-center' : ''}"
        >
            <slot name="actions" />
            <slot />
        </div>
    {/if}
</div>
