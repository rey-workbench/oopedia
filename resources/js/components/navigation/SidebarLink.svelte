<script>
    import { Link, page } from "@inertiajs/svelte";
    import { ChevronRight, Link2 } from "lucide-svelte";

    export let href = "#";
    export let icon = Link2;
    export let active = false;

    const baseClasses =
        "flex items-center gap-4 px-4 py-3.5 rounded-2xl font-bold tracking-tight transition-all duration-300 group";

    $: themeClasses = active
        ? "bg-primary-600 text-white shadow-xl shadow-accent-500/20 "
        : "text-slate-500 hover:text-accent-600 hover:bg-accent-50";

    $: iconContainerClasses = active
        ? "bg-accent-500/20 shadow-[0_0_15px_rgba(124,58,237,0.3)]"
        : "bg-gray-100 group-hover:bg-accent-100";

    $: iconClasses = active
        ? "text-accent-400"
        : "text-slate-400 group-hover:text-accent-600";
</script>

<Link {href} class="{baseClasses} {themeClasses}" {...$$restProps}>
    <div
        class="w-8 h-8 rounded-xl flex items-center justify-center {iconContainerClasses} transition-colors duration-300"
    >
        {#if typeof icon === "string"}
            <i class="{icon} {iconClasses} transition-colors"></i>
        {:else}
            <div class={iconClasses}>
                <svelte:component this={icon} size={18} strokeWidth={2.5} />
            </div>
        {/if}
    </div>
    <span class="flex-1"><slot /></span>

    {#if active}
        <ChevronRight size={14} class="opacity-50" />
    {/if}
</Link>
