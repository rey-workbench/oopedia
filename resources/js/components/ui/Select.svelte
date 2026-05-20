<script lang="ts">
    import { AlertCircle, ChevronDown } from '@lucide/svelte';
    import { generateStableId } from '@/utils/ids';

    interface Option {
        value: string | number;
        label: string;
        disabled?: boolean;
    }

    interface Props {
        value?: string | number | null;
        options?: Option[];
        placeholder?: string;
        label?: string;
        error?: string | undefined;
        id?: string;
        name?: string;
        required?: boolean;
        disabled?: boolean;
        class?: string;
        size?: 'sm' | 'md' | 'lg';
        onchange?: (value: string | number) => void;
        [key: string]: any;
    }

    let {
        value = $bindable(),
        options = [],
        placeholder = '',
        label = '',
        error = '',
        id = '',
        name = '',
        required = false,
        disabled = false,
        class: className = '',
        size = 'md',
        onchange,
        ...rest
    }: Props = $props();

    const selectId = $derived(id || generateStableId('select'));
    const errorId = $derived(`${selectId}-error`);

    const sizes = {
        sm: 'px-4 py-2.5 text-xs border-b-4',
        md: 'px-6 py-4 text-sm border-b-6',
        lg: 'px-6 py-5 text-base border-b-8',
    };

    function handleChange(e: Event) {
        const target = e.target as HTMLSelectElement;
        value = target.value;
        onchange?.(target.value);
    }
</script>

<div class={`w-full space-y-2.5 ${className}`}>
    {#if label}
        <label
            for={selectId}
            class={`ml-4 block text-xs font-black tracking-widest uppercase transition-colors ${error ? 'text-rose-500' : 'text-slate-500'}`}
        >
            {label}
            {#if required}<span class="ml-1 text-rose-500">*</span>{/if}
        </label>
    {/if}

    <div class="group relative">
        <select
            id={selectId}
            {name}
            {required}
            {disabled}
            bind:value
            onchange={handleChange}
            aria-invalid={error ? 'true' : undefined}
            aria-describedby={error ? errorId : undefined}
            {...rest}
            class={`
                w-full cursor-pointer appearance-none rounded-3xl border-2 font-bold transition-all outline-none
                ${sizes[size]}
                ${
                    disabled
                        ? 'cursor-not-allowed border-slate-200 bg-slate-50 text-slate-400 grayscale'
                        : error
                          ? 'border-rose-200 bg-rose-50/20 text-rose-900 focus:border-rose-500 focus:ring-4 focus:ring-rose-50'
                          : 'focus:border-primary-500 focus:ring-primary-100 border-slate-200 bg-white text-slate-900 hover:border-slate-300 focus:ring-4'
                }
            `}
        >
            {#if placeholder}
                <option value="" disabled selected={!value}>{placeholder}</option>
            {/if}
            {#each options as option}
                <option value={option.value} disabled={option.disabled}>{option.label}</option>
            {/each}
        </select>

        <ChevronDown
            size={18}
            class={`pointer-events-none absolute top-1/2 right-6 -translate-y-1/2 text-slate-400 transition-transform ${error ? 'text-rose-500' : ''}`}
        />

        {#if error}
            <div class="absolute top-1/2 right-12 -translate-y-1/2 animate-pulse text-rose-500">
                <AlertCircle size={20} />
            </div>
        {/if}
    </div>

    {#if error}
        <p
            id={errorId}
            role="alert"
            class="animate-in fade-in slide-in-from-top-1 ml-4 text-xs font-bold tracking-widest text-rose-500 uppercase transition-all"
        >
            {error}
        </p>
    {/if}
</div>
