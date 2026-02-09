<script>
    export let variant = "default";
    export let shadow = true;
    export let hover = true;
    export let padding = "p-8";
    let className = "";
    export { className as class }; // support custom classes

    const baseClasses =
        "rounded-[2.5rem] transition-all duration-500 overflow-hidden";

    $: variantClasses =
        variant === "glass" ? "glass" : "bg-white border border-slate-100";
    $: shadowClasses = shadow
        ? variant === "glass"
            ? "shadow-premium"
            : "shadow-soft"
        : "";
    $: hoverClasses = hover ? "hover:shadow-premium hover:-translate-y-1" : "";
    $: classes = `${baseClasses} ${variantClasses} ${shadowClasses} ${hoverClasses} ${className}`;
</script>

<div class={classes} {...$$restProps}>
    {#if $$slots.header}
        <div
            class="px-8 py-6 border-b border-slate-50 flex items-center justify-between"
        >
            <div class="w-full">
                <slot name="header" />
            </div>
        </div>
    {/if}

    <div class={padding}>
        <slot />
    </div>

    {#if $$slots.footer}
        <div class="px-8 py-6 bg-slate-50 border-t border-slate-100">
            <slot name="footer" />
        </div>
    {/if}
</div>
