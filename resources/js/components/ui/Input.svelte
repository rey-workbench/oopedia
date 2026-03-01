<script lang="ts">
    import { AlertCircle } from "lucide-svelte";

    /**
     * @file Input.svelte
     * @description A premium reusable input component for the Oopedia platform.
     */
    interface Props {
        type?: string;
        value?: string;
        placeholder?: string;
        label?: string;
        error?: string;
        id?: string;
        name?: string;
        required?: boolean;
        disabled?: boolean;
        class?: string;
        autocomplete?: any;
        [key: string]: any;
    }

    let {
        type = "text",
        value = $bindable(""),
        placeholder = "",
        label = "",
        error = "",
        id = "",
        name = "",
        required = false,
        disabled = false,
        class: className = "",
        autocomplete = undefined,
        ...rest
    }: Props = $props();

    // Generate a stable ID if not provided
    const inputId = $derived(
        id || `input-${Math.random().toString(36).slice(2, 11)}`,
    );
    const errorId = $derived(`${inputId}-error`);
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
            id={inputId}
            {name}
            {type}
            {placeholder}
            {required}
            {disabled}
            {autocomplete}
            bind:value
            aria-invalid={error ? "true" : undefined}
            aria-describedby={error ? errorId : undefined}
            {...rest}
            class={`
        w-full px-6 py-4 rounded-[1.5rem] border-2 transition-all outline-none font-bold text-sm
        ${disabled ? "bg-slate-50 border-slate-50 text-slate-400 cursor-not-allowed" : "bg-white"}
        ${
            error
                ? "border-rose-100 bg-rose-50/30 text-rose-900 focus:border-rose-500 focus:ring-4 focus:ring-rose-50"
                : "border-slate-50 border-slate-100 hover:border-primary-200 focus:border-primary-600 focus:ring-8 focus:ring-primary-50"
        }
      `}
        />

        {#if error}
            <div
                class="absolute right-6 top-1/2 -translate-y-1/2 text-rose-500 animate-pulse"
            >
                <AlertCircle size={20} />
            </div>
        {/if}
    </div>

    {#if error}
        <p
            id={errorId}
            role="alert"
            class="text-[9px] font-bold text-rose-500 uppercase tracking-widest ml-4 transition-all animate-in fade-in slide-in-from-top-1"
        >
            {error}
        </p>
    {/if}
</div>
