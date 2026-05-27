<script lang="ts">
    import { AlertCircle, ChevronDown } from '@lucide/svelte';
    import { generateStableId } from '@/utils/ids';
    import { fly } from 'svelte/transition';

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
    }: Props = $props();

    let open = $state(false);
    let menuStyles = $state('');
    let containerRef: HTMLDivElement | undefined = $state();
    let menuRef: HTMLDivElement | undefined = $state();

    const selectId = $derived(id || generateStableId('select'));
    const errorId = $derived(`${selectId}-error`);

    const sizes = {
        sm: 'px-4 py-2.5 text-xs',
        md: 'px-4 py-3 text-sm',
        lg: 'px-5 py-4 text-base',
    };

    const selectedOption = $derived(options.find((opt) => String(opt.value) === String(value)));

    function updatePosition() {
        if (!containerRef) return;
        const rect = containerRef.getBoundingClientRect();
        const estimatedHeight = options.length * 46 + 16;

        let style = `width: ${rect.width}px; left: ${rect.left}px; `;
        if (rect.bottom + estimatedHeight > window.innerHeight && rect.top > estimatedHeight) {
            style += `bottom: ${window.innerHeight - rect.top + 8}px;`;
        } else {
            style += `top: ${rect.bottom + 8}px;`;
        }
        menuStyles = style;
    }

    function toggle() {
        if (disabled) return;
        if (!open) updatePosition();
        open = !open;
    }

    function handleScroll(e: Event) {
        if (menuRef && menuRef.contains(e.target as Node)) return;
        if (open) open = false;
    }

    function select(opt: Option) {
        if (opt.disabled) return;
        value = opt.value;
        onchange?.(opt.value);
        open = false;
    }

    $effect(() => {
        if (!open) return;

        function handleClickOutside(event: MouseEvent) {
            if (containerRef && !containerRef.contains(event.target as Node)) {
                open = false;
            }
        }

        document.addEventListener('click', handleClickOutside);
        document.addEventListener('scroll', handleScroll, true);
        window.addEventListener('resize', handleScroll);

        return () => {
            document.removeEventListener('click', handleClickOutside);
            document.removeEventListener('scroll', handleScroll, true);
            window.removeEventListener('resize', handleScroll);
        };
    });
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

    <div bind:this={containerRef} class="relative">
        <!-- Hidden native select for form submission -->
        <select
            id={selectId}
            {name}
            {required}
            bind:value
            class="sr-only"
            aria-hidden="true"
            tabindex="-1"
        >
            {#if placeholder}
                <option value="" disabled selected={!value}>{placeholder}</option>
            {/if}
            {#each options as option}
                <option value={option.value} disabled={option.disabled}>{option.label}</option>
            {/each}
        </select>

        <button
            type="button"
            id={selectId}
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
                            : 'hover:border-primary-400 border-cosmos-border border-b-4 bg-white text-slate-900 active:translate-y-[2px] active:border-b-2'
                }
            `}
        >
            <span class={selectedOption ? 'text-slate-900' : 'text-slate-400'}>
                {selectedOption?.label || placeholder || 'Pilih opsi'}
            </span>
            {#if error}
                <AlertCircle size={16} class="mr-1 text-rose-500" />
            {/if}
            <ChevronDown
                size={18}
                class={`text-slate-400 transition-transform ${open ? 'rotate-180' : ''}`}
            />
        </button>

        {#if open}
            <div
                bind:this={menuRef}
                style={menuStyles}
                class="border-cosmos-border fixed z-9999 overflow-hidden rounded-2xl border-2 bg-white shadow-2xl"
                role="listbox"
                transition:fly={{
                    y: menuStyles.includes('bottom') ? 8 : -8,
                    duration: 130,
                    opacity: 0,
                }}
            >
                <div class="divide-y divide-slate-50">
                    {#if options.length === 0}
                        <div
                            class="px-5 py-3.5 text-xs font-bold tracking-widest text-slate-400 uppercase"
                        >
                            Tidak ada opsi
                        </div>
                    {:else}
                        {#each options as opt (opt.value)}
                            <button
                                type="button"
                                role="option"
                                disabled={opt.disabled}
                                aria-selected={String(opt.value) === String(value)}
                                onclick={() => select(opt)}
                                class={`
                                    flex w-full items-center gap-3 px-5 py-3.5 text-left text-xs font-bold tracking-widest uppercase transition-colors
                                    ${opt.disabled ? 'cursor-not-allowed text-slate-300' : 'hover:bg-slate-50 text-slate-700'}
                                    ${String(opt.value) === String(value) ? 'bg-primary-50 text-primary-600' : ''}
                                `}
                            >
                                {opt.label}
                            </button>
                        {/each}
                    {/if}
                </div>
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
