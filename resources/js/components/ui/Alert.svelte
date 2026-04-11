<script lang="ts">
    import { fade } from 'svelte/transition';
    import type { Snippet } from 'svelte';
    import { Info, CheckCircle2, AlertTriangle, AlertCircle, X } from 'lucide-svelte';

    type AlertVariant = 'info' | 'success' | 'warning' | 'danger' | 'primary';

    interface Props {
        id?: string;
        variant?: AlertVariant;
        dismissible?: boolean;
        class?: string;
        children?: Snippet;
        ondismiss?: () => void;
    }

    let {
        id,
        variant = 'info',
        dismissible = false,
        class: className = '',
        children,
        ondismiss,
    }: Props = $props();

    let visible = $state(true);

    const variants: Record<AlertVariant, string> = {
        info: 'bg-primary-50 text-primary-900 border-primary-200',
        success: 'bg-emerald-50 text-emerald-900 border-emerald-200',
        warning: 'bg-amber-50 text-amber-900 border-amber-200',
        danger: 'bg-rose-50 text-rose-900 border-rose-200',
        primary: 'bg-primary-50 text-primary-900 border-primary-200',
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
        {id}
        transition:fade={{ duration: 200 }}
        class={`flex items-center rounded-2xl border-2 p-4 ${variants[variant] || variants.info} ${className}`}
        role="alert"
    >
        <IconComponent size={20} class="mr-3 shrink-0" />
        <div class="flex-1 text-xs font-bold tracking-tight">
            {@render children?.()}
        </div>
        {#if dismissible}
            <button
                onclick={dismiss}
                type="button"
                aria-label="Dismiss alert"
                class="ml-3 inline-flex h-8 w-8 items-center justify-center rounded-xl p-1.5 transition-all hover:bg-white/50 active:translate-y-0.5"
            >
                <X size={16} />
            </button>
        {/if}
    </div>
{/if}
