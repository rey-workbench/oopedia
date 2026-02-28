<script  lang="ts">
    export let variant = "default";
    export let shadow = true;
    export let hover = true;
    export let padding = "p-6";
    let className = "";
    export { className as class }; // support custom classes

    const baseClasses =
        "rounded-2xl transition-all duration-300 overflow-hidden";

    $: variantClasses =
        variant === "none"
            ? ""
            : variant === "glass"
              ? "glass"
              : "bg-white border border-slate-100";
    $: shadowClasses = shadow
        ? variant === "none"
            ? ""
            : variant === "glass"
              ? "shadow-premium"
              : "shadow-soft"
        : "";
    $: hoverClasses = hover
        ? "hover:shadow-premium hover:shadow-accent-950/10 hover:-translate-y-1"
        : "";
    $: classes = `${baseClasses} ${variantClasses} ${shadowClasses} ${hoverClasses} ${className}`;
</script>

<div class={classes} {...$$restProps}>
    {#if $$slots.header}
        <div
            class="px-6 py-4 border-b border-slate-50 flex items-center justify-between"
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
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
            <slot name="footer" />
        </div>
    {/if}
</div>
