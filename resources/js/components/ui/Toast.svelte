<script lang="ts">
    import { fade, fly } from 'svelte/transition';
    import { CheckCircle, AlertCircle, Info, X, AlertTriangle } from 'lucide-svelte';

    interface Toast {
        id: string;
        type: 'success' | 'error' | 'info' | 'warning';
        title?: string;
        message: string;
        duration?: number;
        dismissible?: boolean;
    }

    interface Props {
        toasts?: Toast[];
        onremove?: (id: string) => void;
        position?:
            | 'top-right'
            | 'top-left'
            | 'bottom-right'
            | 'bottom-left'
            | 'top-center'
            | 'bottom-center';
    }

    let { toasts = [], onremove = () => {}, position = 'top-right' }: Props = $props();

    const colors = {
        success: 'bg-emerald-50 text-emerald-900 border-emerald-200',
        error: 'bg-rose-50 text-rose-900 border-rose-200',
        info: 'bg-primary-50 text-primary-900 border-primary-200',
        warning: 'bg-amber-50 text-amber-950 border-amber-200',
    };

    const iconColors = {
        success: 'text-emerald-500',
        error: 'text-rose-500',
        info: 'text-indigo-500',
        warning: 'text-amber-500',
    };

    const positionClasses = {
        'top-right': 'top-4 right-4',
        'top-left': 'top-4 left-4',
        'bottom-right': 'bottom-4 right-4',
        'bottom-left': 'bottom-4 left-4',
        'top-center': 'top-4 left-1/2 -translate-x-1/2',
        'bottom-center': 'bottom-4 left-1/2 -translate-x-1/2',
    };

    function handleRemove(id: string) {
        onremove(id);
    }
</script>

<div
    class="fixed z-9999 flex w-full max-w-sm flex-col gap-3 {positionClasses[
        position
    ]} pointer-events-none"
>
    {#each toasts as toast (toast.id)}
        <div
            in:fly={{ y: 20, duration: 300 }}
            out:fade={{ duration: 200 }}
            class="pointer-events-auto flex w-full items-start gap-4 rounded-3xl border-2 p-5 {colors[
                toast.type
            ]}"
            role="alert"
        >
            {#if toast.type === 'success'}
                <div class="shrink-0 {iconColors[toast.type]}"><CheckCircle size={20} /></div>
            {:else if toast.type === 'error'}
                <div class="shrink-0 {iconColors[toast.type]}"><AlertCircle size={20} /></div>
            {:else if toast.type === 'warning'}
                <div class="shrink-0 {iconColors[toast.type]}"><AlertTriangle size={20} /></div>
            {:else}
                <div class="shrink-0 {iconColors[toast.type]}"><Info size={20} /></div>
            {/if}
            <div class="flex-1">
                {#if toast.title}
                    <p class="text-xs font-extrabold tracking-widest uppercase">{toast.title}</p>
                {/if}
                <p class="text-xs font-bold {toast.title ? 'mt-1 opacity-80' : ''}">
                    {toast.message}
                </p>
            </div>
            {#if toast.dismissible !== false}
                <button
                    type="button"
                    class="shrink-0 rounded-xl p-1.5 transition-all hover:bg-black/5 active:translate-y-0.5"
                    aria-label="Dismiss"
                    onclick={() => handleRemove(toast.id)}
                >
                    <X size={16} />
                </button>
            {/if}
        </div>
    {/each}
</div>
