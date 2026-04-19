<script lang="ts">
    import { generateStableId } from '@/utils/ids';

    interface Props {
        checked?: boolean;
        label?: string;
        description?: string;
        id?: string;
        name?: string;
        required?: boolean;
        disabled?: boolean;
        class?: string;
        onchange?: (checked: boolean) => void;
        [key: string]: any;
    }

    let {
        checked = $bindable(false),
        label = '',
        description = '',
        id = '',
        name = '',
        required = false,
        disabled = false,
        class: className = '',
        onchange,
        ...rest
    }: Props = $props();

    const toggleId = $derived(id || generateStableId('toggle'));

    function handleChange(e: Event) {
        const target = e.target as HTMLInputElement;
        checked = target.checked;
        onchange?.(target.checked);
    }
</script>

<div class={`flex items-start gap-3 ${className}`}>
    <div class="relative flex items-center pt-1">
        <input
            type="checkbox"
            id={toggleId}
            {name}
            {required}
            {disabled}
            bind:checked
            onchange={handleChange}
            class="peer sr-only"
            {...rest}
        />

        <label
            for={toggleId}
            class={`
                relative inline-flex h-8 w-14 cursor-pointer items-center rounded-full border-2 transition-all
                ${disabled ? 'cursor-not-allowed opacity-50 grayscale' : ''}
                ${checked ? 'bg-primary-500 border-primary-600' : 'border-cosmos-border bg-slate-200'}
            `}
        >
            <span
                class={`
                    inline-block h-5 w-5 transform rounded-full border-b-2 border-black/10 bg-white transition-all duration-200 ease-out
                    ${checked ? 'translate-x-7' : 'translate-x-1'}
                `}
            ></span>
        </label>
    </div>

    {#if label || description}
        <div class="flex flex-col pt-0.5">
            {#if label}
                <label
                    for={toggleId}
                    class={`cursor-pointer text-xs font-bold tracking-widest uppercase ${disabled ? 'text-slate-400' : 'text-slate-700'}`}
                >
                    {label}
                    {#if required}<span class="text-rose-500">*</span>{/if}
                </label>
            {/if}
            {#if description}
                <span class="text-xs text-slate-500">{description}</span>
            {/if}
        </div>
    {/if}
</div>
