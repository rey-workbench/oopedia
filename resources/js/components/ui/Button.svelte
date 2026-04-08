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
        'inline-flex items-center justify-center font-bold tracking-tight transition-all duration-200 active:scale-95 disabled:opacity-50 disabled:pointer-events-none rounded-xl';

    const variants: Record<ButtonVariant, string> = {
        primary: 'bg-primary-500 text-white hover:bg-primary-600',
        secondary:
            'bg-white text-cosmos-text border border-cosmos-border hover:bg-primary-50 hover:text-primary-500',
        gradient: 'bg-accent-500 text-white hover:bg-accent-600',
        glass: 'bg-white/10 backdrop-blur-sm text-cosmos-text border border-white/20 hover:bg-white/20',
        danger: 'bg-rose-500 text-white hover:bg-rose-600',
        success: 'bg-emerald-500 text-white hover:bg-emerald-600',
        warning: 'bg-amber-400 text-amber-950 hover:bg-amber-500',
        ghost: 'text-cosmos-muted hover:text-primary-500 hover:bg-primary-50',
        outline:
            'bg-transparent border border-cosmos-border text-cosmos-text hover:bg-cosmos-text hover:text-white',
    };

    const sizes: Record<ButtonSize, string> = {
        sm: 'px-4 py-2 text-[10px]',
        md: 'px-6 py-2.5 text-xs',
        lg: 'px-8 py-3.5 text-sm',
        xl: 'px-10 py-4 text-base',
    };

    const classes = $derived(
        `${baseClasses} ${variants[variant] ?? variants.primary} ${sizes[size] ?? sizes.md} ${className}`
    );
    const hasChildren = $derived(children !== undefined);
</script>

{#if href}
    <Link {href} class={classes} {...rest}>
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
                        : ''} text-lg transition-transform group-hover:-translate-x-1"
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
                        : ''} text-lg transition-transform group-hover:translate-x-1"
                >
                    <Icon size={18} strokeWidth={2.5} />
                </div>
            {/if}
        {/if}
    </Link>
{:else}
    <button {type} {disabled} class={classes} {...rest}>
        {#if icon && iconPosition === 'left'}
            {#if typeof icon === 'string'}
                <i class="{icon} {hasChildren ? 'mr-3' : ''}"></i>
            {:else}
                {@const Icon = icon as any}
                <div class={hasChildren ? 'mr-3 text-lg' : 'text-lg'}>
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
                <div class={hasChildren ? 'ml-3 text-lg' : 'text-lg'}>
                    <Icon size={18} strokeWidth={2.5} />
                </div>
            {/if}
        {/if}
    </button>
{/if}
