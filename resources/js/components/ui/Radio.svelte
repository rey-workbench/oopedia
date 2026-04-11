<script lang="ts">
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
        groupValue?: string | number;
        onchange?: (value: string | number) => void;
        [key: string]: any;
    }

    let {
        value = '',
        label = '',
        description = '',
        id = '',
        name = '',
        required = false,
        disabled = false,
        class: className = '',
        groupValue = $bindable(),
        onchange,
        ...rest
    }: Props = $props();

    const radioId = $derived(id || generateStableId('radio'));

    function handleChange() {
        if (disabled) return;
        groupValue = value;
        onchange?.(value);
    }

    const isChecked = $derived(String(groupValue) === String(value));

    const baseClasses =
        'relative flex items-center gap-4 w-full p-4 rounded-2xl border-2 transition-all duration-100 cursor-pointer select-none mb-2';
    const depthClasses = 'border-b-4 active:border-b-2 active:translate-y-[2px]';

    const checkedClasses = 'border-accent-500 bg-accent-50/30 text-accent-700 border-b-accent-700';
    const uncheckedClasses =
        'border-cosmos-border bg-white text-cosmos-text border-b-slate-200 hover:bg-slate-50';
    const disabledClasses = 'opacity-50 cursor-not-allowed grayscale';
</script>

<label
    for={radioId}
    class={`
        ${baseClasses} 
        ${!disabled ? depthClasses : ''} 
        ${isChecked ? checkedClasses : uncheckedClasses} 
        ${disabled ? disabledClasses : ''} 
        ${className}
    `}
>
    <input
        type="radio"
        id={radioId}
        {name}
        {value}
        {required}
        {disabled}
        checked={isChecked}
        class="hidden"
        onchange={handleChange}
        {...rest}
    />

    <div
        class={`flex h-6 min-w-6 items-center justify-center rounded-full border-2 transition-colors ${isChecked ? 'border-accent-500 bg-accent-500' : 'border-slate-200'}`}
    >
        <div
            class={`h-2.5 w-2.5 rounded-full bg-white transition-transform duration-200 ${isChecked ? 'scale-100' : 'scale-0'}`}
        ></div>
    </div>

    <div class="flex flex-1 flex-col">
        {#if label}
            <span
                class={`text-sm font-bold tracking-tight transition-colors ${isChecked ? 'text-accent-700' : 'text-cosmos-text'}`}
            >
                {label}
                {#if required}<span class="ml-1 text-rose-500">*</span>{/if}
            </span>
        {/if}
        {#if description}
            <span
                class={`text-xs opacity-70 transition-colors ${isChecked ? 'text-accent-600' : 'text-cosmos-muted'}`}
            >
                {description}
            </span>
        {/if}
    </div>

    {#if isChecked}
        <div
            class="bg-accent-500 ring-accent-100 absolute -top-1.5 -right-1.5 rounded-full border-2 border-white p-1 text-white shadow-sm ring-2"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="10"
                height="10"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="4"
                stroke-linecap="round"
                stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg
            >
        </div>
    {/if}
</label>
