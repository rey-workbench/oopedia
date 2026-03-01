<script lang="ts">
    import { fade } from 'svelte/transition';
    import type { Snippet } from 'svelte';
    import { Info, CheckCircle2, AlertTriangle, AlertCircle, X } from 'lucide-svelte';

    type AlertVariant = 'info' | 'success' | 'warning' | 'danger' | 'primary';

    interface Props {
        variant?: AlertVariant;
        dismissible?: boolean;
        class?: string;
        children?: Snippet;
        ondismiss?: () => void;
    }

    let {
        variant = 'info',
        dismissible = false,
        class: className = '',
        children,
        ondismiss,
    }: Props = $props();

    let visible = $state(true);

    const variants: Record<AlertVariant, string> = {
        info: 'bg-primary-50 text-primary-800 border-primary-100',
        success: 'bg-emerald-50 text-emerald-800 border-emerald-100',
        warning: 'bg-amber-50 text-amber-800 border-amber-100',
        danger: 'bg-rose-50 text-rose-800 border-rose-100',
        primary: 'bg-primary-50 text-primary-800 border-primary-100',
    };

    const icons: Record<AlertVariant, any> = {
        info: Info,
        success: CheckCircle2,
        warning: AlertTriangle,
        danger: AlertCircle,
        primary: Info,
    };

    function dismiss() {
        visible = false;
        ondismiss?.();
    }
</script>

{#if visible}
    {@const IconComponent = icons[variant] || icons.info}
    <div
        transition:fade={{ duration: 200 }}
        class={`flex items-center rounded-2xl border p-4 ${variants[variant] || variants.info} ${className}`}
        role="alert"
    >
        <IconComponent size={20} class="mr-3" />
        <div class="flex-1 text-xs font-bold tracking-widest uppercase">
            {@render children?.()}
        </div>
        {#if dismissible}
            <button
                onclick={dismiss}
                type="button"
                aria-label="Dismiss alert"
                class="-mx-1.5 -my-1.5 ml-auto inline-flex h-8 w-8 items-center justify-center rounded-lg p-1.5 transition-colors hover:bg-white/20"
            >
                <X size={16} />
            </button>
        {/if}
    </div>
{/if}
