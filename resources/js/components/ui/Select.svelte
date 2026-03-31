<script lang="ts">
    import { AlertCircle, ChevronDown, Check } from 'lucide-svelte';

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

    const selectId = $derived(id || `select-${Math.random().toString(36).slice(2, 11)}`);
    const errorId = $derived(`${selectId}-error`);

    const sizes = {
        sm: 'px-4 py-2.5 text-xs',
        md: 'px-6 py-4 text-sm',
        lg: 'px-6 py-5 text-base',
    };

    function handleChange(e: Event) {
        const target = e.target as HTMLSelectElement;
        value = target.value;
        onchange?.(target.value);
    }

    const selectedOption = $derived(options.find((opt) => String(opt.value) === String(value)));
</script>

<div class={`w-full space-y-2 ${className}`}>
    {#if label}
        <label
            for={selectId}
            class="ml-4 block text-[10px] font-bold tracking-widest text-slate-500 uppercase"
        >
            {label}
            {#if required}<span class="text-rose-500">*</span>{/if}
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
                w-full cursor-pointer appearance-none rounded-[1.5rem] border-2 font-bold tracking-widest uppercase transition-all outline-none
                ${sizes[size]}
                ${
                    disabled
                        ? 'cursor-not-allowed border-slate-50 bg-slate-50 text-slate-400'
                        : error
                          ? 'border-rose-100 bg-rose-50/30 text-rose-900 focus:border-rose-500 focus:ring-4 focus:ring-rose-50'
                          : 'hover:border-primary-200 focus:border-primary-600 focus:ring-primary-50 border-slate-100 bg-white text-slate-900 focus:ring-8'
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
            class="animate-in fade-in slide-in-from-top-1 ml-4 text-[9px] font-bold tracking-widest text-rose-500 uppercase transition-all"
        >
            {error}
        </p>
    {/if}
</div>
