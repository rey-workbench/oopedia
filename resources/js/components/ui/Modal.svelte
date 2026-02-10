<script>
    import { createEventDispatcher } from "svelte";
    import { fade, scale } from "svelte/transition";

    export let show = false;
    export let maxWidth = "2xl"; // sm, md, lg, xl, 2xl
    export let closeable = true;

    const dispatch = createEventDispatcher();

    function close() {
        if (closeable) {
            dispatch("close");
        }
    }

    function handleKeydown(e) {
        if (e.key === "Escape" && show) {
            close();
        }
    }

    const maxWidthClass = {
        sm: "sm:max-w-sm",
        md: "sm:max-w-md",
        lg: "sm:max-w-lg",
        xl: "sm:max-w-xl",
        "2xl": "sm:max-w-2xl",
    }[maxWidth];
</script>

<svelte:window on:keydown={handleKeydown} />

{#if show}
    <div
        class="fixed inset-0 z-[999] overflow-y-auto px-4 py-6 sm:px-0 flex items-center justify-center"
        transition:fade={{ duration: 200 }}
    >
        <div
            class="fixed inset-0 transform transition-all"
            on:click={close}
            role="button"
            tabindex="0"
            on:keydown={(e) => e.key === "Enter" && close()}
            aria-label="Close modal backdrop"
        >
            <div
                class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm opacity-100"
            ></div>
        </div>

        <div
            class={`mb-6 bg-white rounded-[2.5rem] shadow-2xl overflow-hidden transform transition-all sm:w-full sm:mx-auto ${maxWidthClass}`}
            transition:scale={{ duration: 200, start: 0.95 }}
        >
            <slot />
        </div>
    </div>
{/if}
