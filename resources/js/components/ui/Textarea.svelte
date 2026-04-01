<script lang="ts">
    import { AlertCircle } from 'lucide-svelte';

    /**
     * @file Textarea.svelte
     * @description A premium reusable textarea component for the Oopedia platform.
     */
    interface Props {
        value?: string;
        placeholder?: string;
        label?: string;
        error?: string | undefined;
        id?: string;
        name?: string;
        required?: boolean;
        disabled?: boolean;
        rows?: number;
        class?: string;
        [key: string]: any;
    }

    let {
        value = $bindable(),
        placeholder = '',
        label = '',
        error = '',
        id = '',
        name = '',
        required = false,
        disabled = false,
        rows = 4,
        class: className = '',
        ...rest
    }: Props = $props();

    // Generate a stable ID if not provided
    const textareaId = $derived(id || `textarea-${Math.random().toString(36).slice(2, 11)}`);
    const errorId = $derived(`${textareaId}-error`);
</script>

<div class={`w-full space-y-2 ${className}`}>
    {#if label}
        <label
            for={textareaId}
            class="ml-4 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
        >
            {label}
            {#if required}<span class="text-rose-500">*</span>{/if}
        </label>
    {/if}

    <div class="group relative">
        <textarea
            id={textareaId}
            {name}
            {placeholder}
            {required}
            {disabled}
            {rows}
            bind:value
            aria-invalid={error ? 'true' : undefined}
            aria-describedby={error ? errorId : undefined}
            {...rest}
            class={`
                w-full rounded-xl border-2 px-6 py-4 text-sm font-bold transition-all outline-none resize-none
                ${disabled ? 'cursor-not-allowed border-slate-50 bg-slate-50 text-slate-400' : 'bg-white'}
                ${
                    error
                        ? 'border-rose-100 bg-rose-50/30 text-rose-900 focus:border-rose-500 focus:ring-4 focus:ring-rose-50'
                        : 'hover:border-primary-200 focus:border-primary-600 focus:ring-primary-50 border-slate-50 focus:ring-8'
                }
            `}
        ></textarea>

        {#if error}
            <div class="absolute top-6 right-6 animate-pulse text-rose-500">
                <AlertCircle size={20} />
            </div>
        {/if}
    </div>

    {#if error}
        <p
            id={errorId}
            role="alert"
            class="animate-in fade-in slide-in-from-top-1 ml-4 text-[9px] font-bold tracking-widest text-rose-500 uppercase transition-all"
        >
            {error}
        </p>
    {/if}
</div>
