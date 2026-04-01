<script lang="ts">
    import Card from '@/components/ui/Card.svelte';
    import type { Component } from 'svelte';

    type IconType = Component<{ size?: number; strokeWidth?: number }> | string;

    type StatVariant = 'primary' | 'success' | 'danger' | 'warning';

    interface Props {
        title: string;
        value: string | number;
        icon?: IconType;
        variant?: StatVariant;
        footer?: string;
        class?: string;
    }

    let {
        title,
        value,
        icon,
        variant = 'primary',
        footer,
        class: className = '',
    }: Props = $props();

    const variantStyles: Record<StatVariant, string> = {
        primary: 'bg-primary-100 text-primary-600',
        success: 'bg-emerald-100 text-emerald-600',
        danger: 'bg-rose-100 text-rose-600',
        warning: 'bg-amber-100 text-amber-600',
    };

    const dotStyles: Record<StatVariant, string> = {
        primary: 'bg-primary-500',
        success: 'bg-emerald-500',
        danger: 'bg-rose-500',
        warning: 'bg-amber-500',
    };
</script>

<Card hover={true} class="group relative overflow-hidden {className}">
    <div class="absolute top-0 right-0 p-4 text-slate-400 opacity-10">
        {#if icon && typeof icon !== 'string'}
            {@const IconComponent = icon}
            <div class="scale-[4] transition-transform duration-500 group-hover:scale-[4.5]">
                <IconComponent size={24} strokeWidth={2.5} />
            </div>
        {/if}
    </div>

    <div class="relative z-10">
        <div
            class="mb-6 flex h-14 w-14 items-center justify-center rounded-2xl shadow-sm {variantStyles[
                variant
            ]}"
        >
            {#if icon}
                {#if typeof icon === 'string'}
                    <i class={icon}></i>
                {:else}
                    {@const IconComponent = icon}
                    <IconComponent size={24} strokeWidth={2.5} />
                {/if}
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
                <div class="h-1.5 w-1.5 rounded-full {dotStyles[variant]}"></div>
                <p class="text-[10px] font-bold tracking-widest text-slate-500 uppercase">
                    {footer}
                </p>
            </div>
        {/if}
    </div>
</Card>
