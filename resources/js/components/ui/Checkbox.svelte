<script lang="ts">
    import { Check } from 'lucide-svelte';
    import { generateStableId } from '@/utils/ids';

    interface Props {
        checked?: boolean;
        value?: string | number;
        label?: string;
        description?: string;
        id?: string;
        name?: string;
        required?: boolean;
        disabled?: boolean;
        class?: string;
        inputClass?: string;
        variant?: 'default' | 'toggle';
        onchange?: (checked: boolean) => void;
        [key: string]: any;
    }

    let {
        checked = $bindable(false),
        value = '',
        label = '',
        description = '',
        id = '',
        name = '',
        required = false,
        disabled = false,
        class: className = '',
        inputClass = '',
        variant = 'default',
        onchange,
        ...rest
    }: Props = $props();

    const checkboxId = $derived(id || generateStableId('checkbox'));

    function handleChange(e: Event) {
        const target = e.target as HTMLInputElement;
        checked = target.checked;
        onchange?.(target.checked);
    }

    const inputClasses = $derived.by(() => {
        const base = 'peer appearance-none shrink-0 cursor-pointer';
        const variantClass =
            variant === 'toggle'
                ? 'sr-only'
                : 'h-5 w-5 rounded border-2 border-slate-200 transition-all';
        const disabledClass = disabled
            ? 'cursor-not-allowed opacity-50'
            : 'hover:border-primary-400';

        let stateClass = '';
        if (variant === 'default') {
            stateClass =
                'checked:border-primary-600 checked:bg-primary-600 focus:ring-4 focus:ring-primary-50 focus:outline-none';
        } else {
            stateClass =
                'peer-checked:bg-primary-600 peer-checked:border-primary-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:absolute after:top-[2px] after:left-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-slate-300 after:bg-white after:transition-all after:content-[""] focus:ring-4 focus:ring-primary-50 focus:outline-none';
        }

        return `${base} ${variantClass} ${disabledClass} ${stateClass} ${inputClass}`;
    });
</script>

<div class={`flex items-start gap-3 ${className}`}>
    <div class="relative flex items-center">
        <input
            type="checkbox"
            id={checkboxId}
            {name}
            {value}
            {required}
            {disabled}
            bind:checked
            onchange={handleChange}
            class={inputClasses}
            {...rest}
        />

        {#if variant === 'default'}
            <div
                class="pointer-events-none absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-0 transition-opacity peer-checked:opacity-100"
            >
                <Check size={14} class="text-white" strokeWidth={3} />
            </div>
        {/if}
    </div>

    {#if label || description}
        <div class="flex flex-col">
            {#if label}
                <label
                    for={checkboxId}
                    class={`cursor-pointer text-xs font-bold tracking-widest uppercase ${disabled ? 'text-slate-400' : 'text-slate-700'}`}
                >
                    {label}
                    {#if required}<span class="text-rose-500">*</span>{/if}
                </label>
            {/if}
            {#if description}
                <span class="text-[10px] text-slate-500">{description}</span>
            {/if}
        </div>
    {/if}
</div>
