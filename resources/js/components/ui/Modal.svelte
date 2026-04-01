<script lang="ts">
    import { onMount } from 'svelte';
    import { fade, scale } from 'svelte/transition';
    import { createFocusTrap } from 'focus-trap';
    import type { Snippet } from 'svelte';

    interface Props {
        show: boolean;
        maxWidth?: 'sm' | 'md' | 'lg' | 'xl' | '2xl';
        closeable?: boolean;
        onclose?: () => void;
        children?: Snippet;
    }

    let { show = false, maxWidth = '2xl', closeable = true, onclose, children }: Props = $props();

    const maxWidthClasses: Record<string, string> = {
        sm: 'sm:max-w-sm',
        md: 'sm:max-w-md',
        lg: 'sm:max-w-lg',
        xl: 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
    };

    let dialogEl: HTMLElement | undefined = $state();
    let trap: ReturnType<typeof createFocusTrap> | null = null;

    function close() {
        if (closeable) {
            onclose?.();
        }
    }

    function handleKeydown(e: KeyboardEvent) {
        if (e.key === 'Escape' && show) close();
    }

    $effect(() => {
        if (show && dialogEl) {
            // Small delay so the dialog is fully rendered before trapping focus
            setTimeout(() => {
                trap = createFocusTrap(dialogEl!, {
                    escapeDeactivates: true,
                    onDeactivate: close,
                    initialFocus: false,
                    allowOutsideClick: true,
                });
                trap.activate();
            }, 50);
        } else {
            trap?.deactivate();
            trap = null;
        }
    });

    onMount(() => {
        return () => {
            trap?.deactivate();
        };
    });
</script>

<svelte:window onkeydown={handleKeydown} />

{#if show}
    <div
        class="fixed inset-0 z-999 flex items-center justify-center overflow-y-auto px-4 py-6 sm:px-0"
        transition:fade={{ duration: 200 }}
    >
        <div
            class="fixed inset-0 transform transition-all"
            onclick={close}
            role="button"
            tabindex="0"
            onkeydown={(e) => e.key === 'Enter' && close()}
            aria-label="Close modal backdrop"
        >
            <div class="absolute inset-0 bg-slate-900/60 opacity-100 backdrop-blur-sm"></div>
        </div>

        <div
            bind:this={dialogEl}
            role="dialog"
            aria-modal="true"
            class={`mb-6 transform overflow-hidden rounded-3xl bg-white shadow-2xl transition-all sm:mx-auto sm:w-full ${maxWidthClasses[maxWidth]}`}
            transition:scale={{ duration: 200, start: 0.95 }}
        >
            {@render children?.()}
        </div>
    </div>
{/if}
