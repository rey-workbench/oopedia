<script>
    import { createEventDispatcher } from "svelte";
    import { fade } from "svelte/transition";

    export let variant = "info"; // info, success, warning, danger
    export let dismissible = false;
    export let className = "";

    const dispatch = createEventDispatcher();
    let visible = true;

    const variants = {
        info: "bg-blue-50 text-blue-800 border-blue-100",
        success: "bg-emerald-50 text-emerald-800 border-emerald-100",
        warning: "bg-amber-50 text-amber-800 border-amber-100",
        danger: "bg-rose-50 text-rose-800 border-rose-100",
        primary: "bg-blue-50 text-blue-800 border-blue-100",
    };

    const icons = {
        info: "fas fa-info-circle",
        success: "fas fa-check-circle",
        warning: "fas fa-exclamation-triangle",
        danger: "fas fa-exclamation-circle",
        primary: "fas fa-info-circle",
    };

    function dismiss() {
        visible = false;
        dispatch("dismiss");
    }
</script>

{#if visible}
    <div
        transition:fade={{ duration: 200 }}
        class={`flex items-center p-4 border rounded-2xl ${variants[variant] || variants.info} ${className}`}
        role="alert"
    >
        <i class={`${icons[variant] || icons.info} mr-3 text-lg`}></i>
        <div class="flex-1 text-xs font-bold uppercase tracking-widest">
            <slot />
        </div>
        {#if dismissible}
            <button
                on:click={dismiss}
                type="button"
                aria-label="Dismiss alert"
                class="ml-auto -mx-1.5 -my-1.5 rounded-lg p-1.5 inline-flex h-8 w-8 hover:bg-white/20 transition-colors items-center justify-center"
            >
                <i class="fas fa-times text-sm"></i>
            </button>
        {/if}
    </div>
{/if}
