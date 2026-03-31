<script lang="ts">
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

    const radioId = $derived(id || `radio-${Math.random().toString(36).slice(2, 11)}`);

    function handleChange(e: Event) {
        const target = e.target as HTMLInputElement;
        groupValue = target.value as string | number;
        onchange?.(target.value as string | number);
    }

    const isChecked = $derived(String(groupValue) === String(value));
</script>

<div class={`flex items-start gap-3 ${className}`}>
    <div class="relative flex items-center">
        <input
            type="radio"
            id={radioId}
            {name}
            {value}
            {required}
            {disabled}
            checked={isChecked}
            onchange={handleChange}
            class="peer checked:border-primary-600 checked:bg-primary-600 focus:ring-primary-50 hover:border-primary-400 h-5 w-5 cursor-pointer appearance-none rounded-full border-2 border-slate-200 transition-all focus:ring-4 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
            {...rest}
        />

        <div
            class="pointer-events-none absolute top-1/2 left-1/2 h-2.5 w-2.5 -translate-x-1/2 -translate-y-1/2 rounded-full bg-white opacity-0 transition-opacity peer-checked:opacity-100"
        ></div>
    </div>

    {#if label || description}
        <div class="flex flex-col">
            {#if label}
                <label
                    for={radioId}
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
