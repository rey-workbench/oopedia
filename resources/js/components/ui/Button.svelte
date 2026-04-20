<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import type { Snippet } from 'svelte';

    type ButtonVariant =
        | 'primary'
        | 'secondary'
        | 'dark'
        | 'gradient'
        | 'glass'
        | 'danger'
        | 'success'
        | 'warning'
        | 'ghost'
        | 'outline';
    type ButtonSize = 'sm' | 'md' | 'lg' | 'xl';

    interface Props {
        id?: string | undefined;
        variant?: ButtonVariant;
        size?: ButtonSize;
        type?: 'button' | 'submit' | 'reset';
        disabled?: boolean;
        icon?: any;
        iconPosition?: 'left' | 'right';
        href?: string | null;
        class?: string;
        ariaLabel?: string;
        children?: Snippet;
        [key: string]: unknown;
    }

    let {
        id,
        variant = 'primary',
        size = 'md',
        type = 'button',
        disabled = false,
        icon = null,
        iconPosition = 'left',
        href = null,
        class: className = '',
        ariaLabel = undefined,
        children,
        ...rest
    }: Props = $props();

    const baseClasses =
        'group inline-flex items-center justify-center font-black tracking-widest transition-all duration-150 disabled:opacity-50 disabled:pointer-events-none rounded-2xl border-2 border-b-6 uppercase select-none';

    const variants: Record<ButtonVariant, string> = {
        primary:
            'bg-primary-500 text-white border-primary-500 border-b-primary-950 hover:bg-primary-600 hover:border-primary-600 hover:border-b-primary-950 active:bg-primary-600 active:border-primary-600',
        secondary:
            'bg-white text-slate-600 border-slate-200 border-b-slate-300 hover:bg-slate-50 hover:text-slate-700',
        dark: 'bg-slate-900 text-white border-slate-900 border-b-slate-950 hover:bg-slate-800 active:bg-slate-800',
        gradient:
            'bg-indigo-500 text-white border-indigo-500 border-b-indigo-700 hover:bg-indigo-600',
        glass: 'bg-white/10 backdrop-blur-sm text-white border-white/20 border-b-white/40 hover:bg-white/20',
        danger: 'bg-rose-500 text-white border-rose-500 border-b-rose-700 hover:bg-rose-600',
        success:
            'bg-emerald-500 text-white border-emerald-500 border-b-emerald-700 hover:bg-emerald-600',
        warning:
            'bg-amber-400 text-amber-950 border-amber-400 border-b-amber-600 hover:bg-amber-500',
        ghost: 'text-slate-500 hover:text-primary-500 hover:bg-slate-50 border-transparent border-b-transparent active:border-b-transparent active:translate-y-0',
        outline:
            'bg-white border-2 border-slate-800 text-slate-900 border-b-slate-900 hover:bg-slate-50 hover:border-slate-900 hover:border-b-slate-950 active:bg-slate-100',
    };

    const sizes: Record<ButtonSize, string> = {
        sm: 'px-4 py-2 text-xs border-b-4',
        md: 'px-6 py-3 text-sm border-b-6',
        lg: 'px-8 py-4 text-base border-b-6',
        xl: 'px-10 py-5 text-lg border-b-8',
    };

    const activeStates: Record<ButtonSize, string> = {
        sm: 'active:translate-y-[2px] active:border-b-2 shadow-none',
        md: 'active:translate-y-[4px] active:border-b-2 shadow-none',
        lg: 'active:translate-y-[4px] active:border-b-2 shadow-none',
        xl: 'active:translate-y-[6px] active:border-b-2 shadow-none',
    };

    const classes = $derived(
        `${baseClasses} ${activeStates[size] ?? activeStates.md} ${variants[variant] ?? variants.primary} ${sizes[size] ?? sizes.md} ${className}`
    );
    const hasChildren = $derived(children !== undefined);
</script>

{#if href}
    <Link {id} {href} aria-label={ariaLabel} class={classes} {...rest}>
        {#if icon && iconPosition === 'left'}
            {#if typeof icon === 'string'}
                <i
                    class="{icon} {hasChildren
                        ? 'mr-3'
                        : ''} transition-transform group-hover:-translate-x-1"
                ></i>
            {:else}
                {@const Icon = icon as any}
                <div
                    class="{hasChildren
                        ? 'mr-3'
                        : ''} text-lg text-current transition-transform group-hover:-translate-x-1"
                >
                    <Icon size={18} strokeWidth={2.5} />
                </div>
            {/if}
        {/if}
        {@render children?.()}
        {#if icon && iconPosition === 'right'}
            {#if typeof icon === 'string'}
                <i
                    class="{icon} {hasChildren
                        ? 'ml-3'
                        : ''} transition-transform group-hover:translate-x-1"
                ></i>
            {:else}
                {@const Icon = icon as any}
                <div
                    class="{hasChildren
                        ? 'ml-3'
                        : ''} text-lg text-current transition-transform group-hover:translate-x-1"
                >
                    <Icon size={18} strokeWidth={2.5} />
                </div>
            {/if}
        {/if}
    </Link>
{:else}
    <button {id} {type} class={classes} {disabled} aria-label={ariaLabel} {...rest}>
        {#if icon && iconPosition === 'left'}
            {#if typeof icon === 'string'}
                <i class="{icon} {hasChildren ? 'mr-3' : ''}"></i>
            {:else}
                {@const Icon = icon as any}
                <div class={hasChildren ? 'mr-3 text-lg text-current' : 'text-lg text-current'}>
                    <Icon size={18} strokeWidth={2.5} />
                </div>
            {/if}
        {/if}
        {@render children?.()}
        {#if icon && iconPosition === 'right'}
            {#if typeof icon === 'string'}
                <i
                    class="{icon} {hasChildren
                        ? 'ml-3'
                        : ''} transition-transform group-hover:translate-x-1"
                ></i>
            {:else}
                {@const Icon = icon as any}
                <div class={hasChildren ? 'ml-3 text-lg text-current' : 'text-lg text-current'}>
                    <Icon size={18} strokeWidth={2.5} />
                </div>
            {/if}
        {/if}
    </button>
{/if}
