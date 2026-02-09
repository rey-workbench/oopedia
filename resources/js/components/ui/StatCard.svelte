<script>
    export let title;
    export let value;
    export let icon;
    export let variant = "primary"; // primary, success, danger, warning
    export let footer = "";

    const variants = {
        primary: "bg-blue-50 text-blue-600",
        success: "bg-emerald-50 text-emerald-600",
        danger: "bg-rose-50 text-rose-600",
        warning: "bg-amber-50 text-amber-600",
    };

    const iconVariants = {
        primary: "bg-blue-100 text-blue-600",
        success: "bg-emerald-100 text-emerald-600",
        danger: "bg-rose-100 text-rose-600",
        warning: "bg-amber-100 text-amber-600",
    };
</script>

<div
    class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300"
>
    <div
        class={`absolute top-0 right-0 p-4 opacity-10 ${variants[variant].replace("bg-", "text-")}`}
    >
        {#if typeof icon === "string"}
            <i
                class={`${icon} text-6xl group-hover:scale-110 transition-transform duration-500`}
            ></i>
        {:else}
            <div
                class="scale-[4] group-hover:scale-[4.5] transition-transform duration-500"
            >
                <svelte:component this={icon} size={24} strokeWidth={2.5} />
            </div>
        {/if}
    </div>

    <div class="relative z-10">
        <div
            class={`w-14 h-14 rounded-2xl flex items-center justify-center mb-6 glass shadow-sm ${iconVariants[variant]}`}
        >
            {#if typeof icon === "string"}
                <i class={`${icon} text-xl`}></i>
            {:else}
                <svelte:component this={icon} size={24} strokeWidth={2.5} />
            {/if}
        </div>

        <h3
            class="text-slate-400 font-bold text-[10px] uppercase tracking-wider mb-2"
        >
            {title}
        </h3>
        <div
            class="text-4xl font-black text-slate-900 mb-2 font-display tracking-tight"
        >
            {value}
        </div>

        {#if footer}
            <div class="flex items-center gap-2">
                <div
                    class={`w-1.5 h-1.5 rounded-full ${variants[variant].replace("bg-", "bg-").split(" ")[0].replace("50", "500")}`}
                ></div>
                <p
                    class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                >
                    {footer}
                </p>
            </div>
        {/if}
    </div>
</div>
