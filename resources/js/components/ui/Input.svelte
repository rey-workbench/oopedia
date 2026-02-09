<script>
    /**
     * @file Input.svelte
     * @description A premium reusable input component for the Oopedia platform.
     */
    export let type = "text";
    export let value = "";
    export let placeholder = "";
    export let label = "";
    export let error = "";
    export let id = "";
    export let name = "";
    export let required = false;
    export let disabled = false;
    let className = "";
    export { className as class };
    export let autocomplete = undefined;

    // Generate a random ID if not provided
    const inputId = id || `input-${Math.random().toString(36).substr(2, 9)}`;
</script>

<div class={`space-y-2 w-full ${className}`}>
    {#if label}
        <label
            for={inputId}
            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-4"
        >
            {label}
            {#if required}<span class="text-rose-500">*</span>{/if}
        </label>
    {/if}

    <div class="relative group">
        <input
            {id}
            {name}
            {type}
            {placeholder}
            {required}
            {disabled}
            {autocomplete}
            bind:value
            on:input
            on:change
            on:focus
            on:blur
            class={`
        w-full px-6 py-4 rounded-[1.5rem] border-2 transition-all outline-none font-bold text-sm
        ${disabled ? "bg-slate-50 border-slate-50 text-slate-400 cursor-not-allowed" : "bg-white"}
        ${
            error
                ? "border-rose-100 bg-rose-50/30 text-rose-900 focus:border-rose-500 focus:ring-4 focus:ring-rose-50"
                : "border-slate-50 border-slate-100 hover:border-blue-200 focus:border-blue-600 focus:ring-8 focus:ring-blue-50"
        }
      `}
        />

        {#if error}
            <div
                class="absolute right-6 top-1/2 -translate-y-1/2 text-rose-500 animate-pulse"
            >
                <i class="fas fa-circle-exclamation"></i>
            </div>
        {/if}
    </div>

    {#if error}
        <p
            class="text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-4 transition-all animate-in fade-in slide-in-from-top-1"
        >
            {error}
        </p>
    {/if}
</div>
