<script lang="ts">
    import { ChevronDown } from 'lucide-svelte';
    import { generateStableId } from '@/utils/ids';

    interface Option {
        value: string | number;
        label: string;
        disabled?: boolean;
        icon?: any;
    }

    interface Props {
        value?: string | number | null;
        options?: Option[];
        placeholder?: string;
        label?: string;
        error?: string;
        id?: string;
        name?: string;
        required?: boolean;
        disabled?: boolean;
        class?: string;
        size?: 'sm' | 'md' | 'lg';
        onchange?: (value: string | number) => void;
    }

    let {
        value = $bindable(),
        options = [],
        placeholder = 'Pilih opsi',
        label = '',
        error = '',
        id = '',
        name = '',
        required = false,
        disabled = false,
        class: className = '',
        size = 'md',
        onchange,
    }: Props = $props();

    let open = $state(false);
    let containerRef: HTMLDivElement | undefined = $state();

    const selectId = $derived(id || generateStableId('dropdown'));
    const errorId = $derived(`${selectId}-error`);

    const sizes = {
        sm: 'px-3 py-2 text-xs',
        md: 'px-4 py-3 text-sm',
        lg: 'px-5 py-4 text-base',
    };
    const selectedOption = $derived(options.find((opt) => String(opt.value) === String(value)));

    function toggle() {
        if (!disabled) open = !open;
    }

    function select(opt: Option) {
        if (opt.disabled) return;
        value = opt.value;
        onchange?.(opt.value);
        open = false;
    }

    function handleClickOutside(e: MouseEvent) {
        if (containerRef && !containerRef.contains(e.target as Node)) {
            open = false;
        }
    }

    $effect(() => {
        if (!open) return;

        document.addEventListener('click', handleClickOutside);
        return () => document.removeEventListener('click', handleClickOutside);
    });
</script>

<div class={`w-full ${className}`}>
    {#if label}
        <label
            for={selectId}
            class="mb-2 ml-4 block text-[10px] font-bold tracking-widest text-slate-500 uppercase"
        >
            {label}
            {#if required}<span class="text-rose-500">*</span>{/if}
        </label>
    {/if}

    <div bind:this={containerRef} class="relative">
        <button
            type="button"
            id={selectId}
            {name}
            {disabled}
            onclick={toggle}
            aria-haspopup="listbox"
            aria-expanded={open}
            aria-describedby={error ? errorId : undefined}
            class={`
                flex w-full items-center justify-between rounded-2xl border-2 font-bold tracking-widest uppercase transition-all outline-none
                ${sizes[size]}
                ${
                    disabled
                        ? 'cursor-not-allowed border-slate-50 bg-slate-50 text-slate-400'
                        : error
                          ? 'border-rose-100 bg-rose-50/30 text-rose-900 ring-rose-50 hover:border-rose-300 focus:border-rose-500'
                          : open
                            ? 'border-primary-500 ring-primary-100 bg-white text-slate-900 ring-4'
                            : 'hover:border-primary-400 border-cosmos-border bg-white text-slate-900'
                }
            `}
        >
            <span class={selectedOption ? '' : 'text-slate-400'}>
                {selectedOption?.label || placeholder}
            </span>
            <ChevronDown
                size={18}
                class={`text-slate-400 transition-transform ${open ? 'rotate-180' : ''}`}
            />
        </button>

        {#if open}
            <div
                class="border-cosmos-border absolute z-50 mt-2 w-full overflow-hidden rounded-2xl border-2 bg-white"
                role="listbox"
            >
                {#if options.length === 0}
                    <div class="px-4 py-3 text-sm text-slate-400">Tidak ada opsi</div>
                {:else}
                    {#each options as opt (opt.value)}
                        <button
                            type="button"
                            role="option"
                            disabled={opt.disabled}
                            aria-selected={String(opt.value) === String(value)}
                            onclick={() => select(opt)}
                            class={`
                                flex w-full items-center gap-2 px-4 py-3 text-left text-[10px] font-bold tracking-widest uppercase transition-colors
                                ${opt.disabled ? 'cursor-not-allowed text-slate-300' : 'hover:bg-primary-50/50 text-slate-700'}
                                ${String(opt.value) === String(value) ? 'bg-primary-50 text-primary-600' : ''}
                            `}
                        >
                            {#if opt.icon}
                                <span class="text-primary-600">{opt.icon}</span>
                            {/if}
                            {opt.label}
                        </button>
                    {/each}
                {/if}
            </div>
        {/if}
    </div>

    {#if error}
        <p
            id={errorId}
            role="alert"
            class="mt-2 ml-4 text-[9px] font-bold tracking-widest text-rose-500 uppercase"
        >
            {error}
        </p>
    {/if}
</div>
