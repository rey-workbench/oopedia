<script lang="ts">
    import type { Component } from 'svelte';

    interface Props {
        title: string;
        value: string | number;
        icon: Component<any> | string;
        variant?: 'primary' | 'success' | 'danger' | 'warning';
        footer?: string;
    }

    let { title, value, icon, variant = 'primary', footer = '' }: Props = $props();

    const variants = {
        primary: 'bg-accent-50 text-accent-600',
        success: 'bg-emerald-50 text-emerald-600',
        danger: 'bg-rose-50 text-rose-600',
        warning: 'bg-amber-50 text-amber-600',
    };

    const iconVariants = {
        primary: 'bg-accent-100 text-accent-600',
        success: 'bg-emerald-100 text-emerald-600',
        danger: 'bg-rose-100 text-rose-600',
        warning: 'bg-amber-100 text-amber-600',
    };

    const bgVariantClass = $derived(variants[variant] || variants.primary);
    const iconVariantClass = $derived(iconVariants[variant] || iconVariants.primary);
    const indicatorColor = $derived(bgVariantClass.split(' ')[0]?.replace('50', '500') ?? '');
    const textColor = $derived(bgVariantClass.replace('bg-', 'text-'));
</script>

<div
    class="group relative overflow-hidden rounded-3xl border border-slate-100 bg-white p-6 shadow-xl shadow-slate-200/50 transition-transform duration-300 hover:-translate-y-1"
>
    <div class="absolute top-0 right-0 p-4 opacity-10 {textColor}">
        {#if typeof icon === 'string'}
            <i class="{icon} text-6xl transition-transform duration-500 group-hover:scale-110"></i>
        {:else}
            {@const IconComponent = icon as any}
            <div class="scale-[4] transition-transform duration-500 group-hover:scale-[4.5]">
                <IconComponent size={24} strokeWidth={2.5} />
            </div>
        {/if}
    </div>

    <div class="relative z-10">
        <div
            class="glass mb-6 flex h-14 w-14 items-center justify-center rounded-2xl shadow-sm {iconVariantClass}"
        >
            {#if typeof icon === 'string'}
                <i class="{icon} text-xl"></i>
            {:else}
                {@const IconComponent = icon as any}
                <IconComponent size={24} strokeWidth={2.5} />
            {/if}
        </div>

        <h3 class="mb-2 text-[10px] font-bold tracking-wider text-slate-600 uppercase">
            {title}
        </h3>
        <div class="font-display mb-2 text-4xl font-black tracking-tight text-slate-900">
            {value}
        </div>

        {#if footer}
            <div class="flex items-center gap-2">
                <div class="h-1.5 w-1.5 rounded-full {indicatorColor}"></div>
                <p class="text-[10px] font-bold tracking-widest text-slate-500 uppercase">
                    {footer}
                </p>
            </div>
        {/if}
    </div>
</div>
