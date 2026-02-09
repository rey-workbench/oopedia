<script>
    import { Link, page } from "@inertiajs/svelte";

    export let href = "#";
    export let icon = "fas fa-link";
    export let active = false;
    export let isAdmin = false;

    const baseClasses =
        "flex items-center gap-4 px-4 py-3.5 rounded-2xl font-bold tracking-tight transition-all duration-300 group";

    $: themeClasses = isAdmin
        ? active
            ? "bg-indigo-600/10 text-indigo-400 border-l-4 border-indigo-600 shadow-[inset_0_0_20px_rgba(79,70,229,0.05)]"
            : "text-slate-500 hover:text-white hover:bg-slate-800/50"
        : active
          ? "bg-blue-600 text-white shadow-xl shadow-blue-100 "
          : "text-slate-500 hover:text-blue-600 hover:bg-blue-50";

    $: iconContainerClasses = isAdmin
        ? active
            ? "bg-indigo-600/20"
            : "bg-slate-800 group-hover:bg-slate-700"
        : active
          ? "bg-white/20"
          : "bg-gray-100 group-hover:bg-blue-100";

    $: iconClasses = isAdmin
        ? active
            ? "text-indigo-400"
            : "text-slate-600 group-hover:text-indigo-400"
        : active
          ? "text-white"
          : "text-slate-400 group-hover:text-blue-600";
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

    {#if active && !isAdmin}
        <i class="fas fa-chevron-right text-[10px] opacity-50"></i>
    {/if}
</Link>
