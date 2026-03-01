<script lang="ts">
    import type { Component } from "svelte";

    interface Props {
        title: string;
        value: string | number;
        icon: Component<any> | string;
        variant?: "primary" | "success" | "danger" | "warning";
        footer?: string;
    }

    let {
        title,
        value,
        icon,
        variant = "primary",
        footer = "",
    }: Props = $props();

    const variants = {
        primary: "bg-accent-50 text-accent-600",
        success: "bg-emerald-50 text-emerald-600",
        danger: "bg-rose-50 text-rose-600",
        warning: "bg-amber-50 text-amber-600",
    };

    const iconVariants = {
        primary: "bg-accent-100 text-accent-600",
        success: "bg-emerald-100 text-emerald-600",
        danger: "bg-rose-100 text-rose-600",
        warning: "bg-amber-100 text-amber-600",
    };

    const bgVariantClass = $derived(variants[variant] || variants.primary);
    const iconVariantClass = $derived(
        iconVariants[variant] || iconVariants.primary,
    );
    const indicatorColor = $derived(
        bgVariantClass.split(" ")[0]?.replace("50", "500") ?? "",
    );
    const textColor = $derived(bgVariantClass.replace("bg-", "text-"));
</script>

<div
    class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300"
>
    <div class="absolute top-0 right-0 p-4 opacity-10 {textColor}">
        {#if typeof icon === "string"}
            <i
                class="{icon} text-6xl group-hover:scale-110 transition-transform duration-500"
            ></i>
        {:else}
            {@const IconComponent = icon as any}
            <div
                class="scale-[4] group-hover:scale-[4.5] transition-transform duration-500"
            >
                <IconComponent size={24} strokeWidth={2.5} />
            </div>
        {/if}
    </div>

    <div class="relative z-10">
        <div
            class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6 glass shadow-sm {iconVariantClass}"
        >
            {#if typeof icon === "string"}
                <i class="{icon} text-xl"></i>
            {:else}
                {@const IconComponent = icon as any}
                <IconComponent size={24} strokeWidth={2.5} />
            {/if}
        </div>

        <h3
            class="text-slate-600 font-bold text-[10px] uppercase tracking-wider mb-2"
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
                <div class="w-1.5 h-1.5 rounded-full {indicatorColor}"></div>
                <p
                    class="text-[10px] font-bold text-slate-500 uppercase tracking-widest"
                >
                    {footer}
                </p>
            </div>
        {/if}
    </div>
</div>
