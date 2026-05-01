<script lang="ts">
    import type { Snippet } from 'svelte';

    let {
        type,
        title,
        icon: Icon,
        isError = false,
        hasDropZone = true,
        isDraggingOver = false,
        isDropError = false,
        isWrapper = false,
        ondragover,
        ondragleave,
        ondrop,
        children,
        headerContent,
    } = $props<{
        type: 'if' | 'control' | 'looks' | 'motion' | 'data';
        title: string;
        icon: any;
        isError?: boolean;
        hasDropZone?: boolean;
        isDraggingOver?: boolean;
        isDropError?: boolean;
        isWrapper?: boolean;
        ondragover?: (e: DragEvent) => void;
        ondragleave?: (e: DragEvent) => void;
        ondrop?: (e: DragEvent) => void;
        children?: Snippet;
        headerContent?: Snippet;
    }>();
</script>

<style>
    /* Theme Variables - Aligned with app.css global tokens */
    /* IF (WHEN) - Brand Yellow / Amber */
    .scratch-if {
        --block-bg: #ff9c2d; /* brand-yellow */
        --block-border: #e68a24;
    }
    /* CONTROL (SAY) - Accent Warm Coral */
    .scratch-control {
        --block-bg: #ff5242; /* accent-500 */
        --block-border: #e63e2a;
    }
    /* MOTION (DO) - Info Blue / Indigo */
    .scratch-motion {
        --block-bg: #3b82f6; /* info-500 */
        --block-border: #2563eb;
    }
    /* LOOKS (DEDUCE) - Purple / Violet */
    .scratch-looks {
        --block-bg: #8b5cf6;
        --block-border: #7c3aed;
    }
    /* DATA (METADATA) - Primary Deep Ink */
    .scratch-data {
        --block-bg: #0c0c14; /* primary-500 */
        --block-border: #000000;
    }

    /* Base Scratch Block */
    .scratch-block {
        position: relative;
        border-radius: 1rem; /* radius-duo */
        color: white;
        font-weight: 800;
        margin-bottom: 8px;
        background: var(--block-bg);
        border: 2px solid var(--block-border);
        box-shadow: inset 0 -4px 0 rgba(0, 0, 0, 0.25); /* Duo thick bottom border effect */
    }

    .scratch-header {
        padding: 12px 16px;
        min-height: 52px;
    }

    /* Top Notch (Convex) - Playful jigsaw look */
    .scratch-block::before {
        content: '';
        position: absolute;
        top: -10px;
        left: 24px;
        width: 28px;
        height: 12px;
        background: var(--block-bg);
        border-radius: 6px 6px 0 0;
        border: 2px solid var(--block-border);
        border-bottom: none;
        z-index: 1;
    }

    /* Bottom Notch (Concave) for standalone blocks */
    .scratch-block:not(.is-wrapper)::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 24px;
        width: 28px;
        height: 12px;
        background: #fdfdfb; /* cosmos-bg from app.css */
        border-radius: 6px 6px 0 0;
        border-top: 2px solid var(--block-border);
        z-index: 2;
    }

    /* --- C-BLOCK STYLES (isWrapper) --- */
    .scratch-block.is-wrapper {
        padding-bottom: 0;
    }

    .scratch-wrapper-body {
        margin-left: 20px;
        border-left: 20px solid var(--block-bg);
        padding: 12px 12px 8px 12px;
        background: transparent;
        box-shadow: inset 2px 0 0 rgba(255, 255, 255, 0.1);
    }

    .scratch-inner-container {
        min-height: 32px;
    }

    .scratch-wrapper-footer {
        height: 28px;
        margin-left: 20px;
        background: var(--block-bg);
        border-radius: 0 0 1rem 1rem;
        border-top: 2px solid rgba(0, 0, 0, 0.15);
        box-shadow: inset 0 -4px 0 rgba(0, 0, 0, 0.25);
        position: relative;
        width: 140px;
    }

    /* Footer notch logic */
    .scratch-wrapper-footer::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 4px;
        width: 28px;
        height: 12px;
        background: #fdfdfb;
        border-radius: 6px 6px 0 0;
        border-top: 2px solid var(--block-border);
        z-index: 2;
    }

    @keyframes shake {
        0%,
        100% {
            transform: translateX(0);
        }
        25% {
            transform: translateX(-6px);
        }
        75% {
            transform: translateX(6px);
        }
    }
    :global(.shake-error) {
        animation: shake 0.2s ease-in-out 0s 2;
        background: #ef4444 !important; /* error-500 */
        border-color: #dc2626 !important;
    }
</style>

<div
    class="scratch-block scratch-{type} {isError ? 'shake-error' : ''} {isWrapper
        ? 'is-wrapper'
        : ''}"
>
    <!-- HEADER BAR -->
    <div
        class="scratch-header flex items-center gap-3 {type === 'control' && !isWrapper
            ? 'flex-col items-stretch space-y-3'
            : ''}"
    >
        {#if type === 'control' && !isWrapper}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <Icon size={18} fill="currentColor" />
                    <span class="font-display tracking-tight">{title}</span>
                </div>
            </div>
            {@render children?.()}
        {:else}
            <Icon size={18} fill="currentColor" />
            <span class="font-display tracking-tight whitespace-nowrap">{title}</span>

            {#if hasDropZone}
                <!-- svelte-ignore a11y_no_static_element_interactions -->
                <div
                    class="flex min-h-[40px] flex-1 flex-wrap gap-2 rounded-xl bg-black/10 p-2 transition-all {isDraggingOver
                        ? isDropError
                            ? 'bg-rose-500/30 ring-2 ring-rose-500'
                            : 'bg-black/20 ring-2 ring-white/50'
                        : ''} {type === 'motion' ? 'items-center' : ''}"
                    {ondragover}
                    {ondragleave}
                    {ondrop}
                >
                    {#if isWrapper}
                        {@render headerContent?.()}
                    {:else}
                        {@render children?.()}
                    {/if}
                </div>
            {:else if isWrapper}
                {@render headerContent?.()}
            {:else}
                {@render children?.()}
            {/if}
        {/if}
    </div>

    <!-- C-BLOCK WRAPPER BODY -->
    {#if isWrapper}
        <div class="scratch-wrapper-body">
            <div class="scratch-inner-container flex flex-col gap-2">
                {@render children?.()}
            </div>
        </div>
        <div class="scratch-wrapper-footer"></div>
    {/if}
</div>
