<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import type { Snippet } from 'svelte';

    type ButtonVariant =
        | 'primary'
        | 'secondary'
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
        children,
        ...rest
    }: Props = $props();

    const baseClasses =
        'group inline-flex items-center justify-center font-black tracking-widest transition-all duration-150 active:translate-y-[4px] active:border-b-2 disabled:opacity-50 disabled:pointer-events-none rounded-2xl border-2 border-b-6 uppercase';

    const variants: Record<ButtonVariant, string> = {
        primary:
            'bg-primary-500 text-white border-primary-500 border-b-slate-700 hover:bg-primary-600',
        secondary:
            'bg-white text-slate-500 border-slate-300 border-b-slate-400 hover:bg-slate-50 hover:text-slate-600',
        gradient: 'bg-accent-500 text-white border-accent-800 hover:bg-accent-600',
        glass: 'bg-white/10 backdrop-blur-sm text-white border-white/20 border-b-white/30 hover:bg-white/20',
        danger: 'bg-rose-500 text-white border-rose-800 hover:bg-rose-600',
        success: 'bg-emerald-500 text-white border-emerald-800 hover:bg-emerald-600',
        warning: 'bg-amber-400 text-amber-950 border-amber-600 hover:bg-amber-500',
        ghost: 'text-slate-500 hover:text-primary-500 hover:bg-slate-50 border-transparent border-b-transparent active:translate-y-0',
        outline:
            'bg-white border-2 border-slate-800 text-slate-900 hover:bg-slate-50 border-b-slate-900',
    };

    const sizes: Record<ButtonSize, string> = {
        sm: 'px-4 py-2 text-[10px] border-b-4 active:translate-y-[2px]',
        md: 'px-6 py-3 text-xs border-b-6 active:translate-y-[4px]',
        lg: 'px-8 py-4 text-sm border-b-6 active:translate-y-[4px]',
        xl: 'px-10 py-5 text-base border-b-8 active:translate-y-[6px]',
    };

    const classes = $derived(
        `${baseClasses} ${variants[variant] ?? variants.primary} ${sizes[size] ?? sizes.md} ${className}`
    );
    const hasChildren = $derived(children !== undefined);
</script>

{#if href}
    <Link {id} {href} class={classes} {...rest}>
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
    <button {id} {type} class={classes} {disabled} {...rest}>
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
