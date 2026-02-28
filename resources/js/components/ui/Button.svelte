<script lang="ts">
    import { Link } from "@inertiajs/svelte";
    import type { Component, Snippet } from "svelte";

    type ButtonVariant = "primary" | "secondary" | "gradient" | "glass" | "danger" | "success" | "warning" | "ghost" | "outline";
    type ButtonSize = "sm" | "md" | "lg" | "xl";

    interface Props {
        variant?: ButtonVariant;
        size?: ButtonSize;
        type?: "button" | "submit" | "reset";
        disabled?: boolean;
        icon?: Component<{ size?: number; strokeWidth?: number; class?: string }> | string | null;
        iconPosition?: "left" | "right";
        href?: string | null;
        class?: string;
        children?: Snippet;
        [key: string]: unknown;
    }

    let {
        variant = "primary",
        size = "md",
        type = "button",
        disabled = false,
        icon = null,
        iconPosition = "left",
        href = null,
        class: className = "",
        children,
        ...rest
    }: Props = $props();

    const baseClasses =
        "inline-flex items-center justify-center font-bold tracking-tight transition-all duration-300 active:scale-95 disabled:opacity-50 disabled:pointer-events-none rounded-xl";

    const variants: Record<ButtonVariant, string> = {
        primary:
            "bg-primary-600 text-white shadow-lg shadow-accent-950/20 hover:scale-[1.02] hover:shadow-accent-600/30",
        secondary:
            "bg-white text-slate-900 border-2 border-slate-100 hover:border-accent-500 hover:text-accent-600 shadow-sm",
        gradient:
            "bg-accent-600 text-white shadow-lg shadow-accent-500/25 hover:shadow-accent-500/40 hover:-translate-y-0.5",
        glass: "glass text-primary-700 hover:bg-white/50 shadow-sm hover:shadow-md",
        danger: "bg-rose-500 text-white shadow-lg shadow-rose-500/20 hover:bg-rose-600",
        success:
            "bg-emerald-500 text-white shadow-lg shadow-emerald-500/20 hover:bg-emerald-600",
        warning:
            "bg-amber-400 text-amber-950 shadow-lg shadow-amber-400/20 hover:bg-amber-500",
        ghost: "text-slate-500 hover:text-accent-600 hover:bg-accent-50",
        outline:
            "bg-transparent border-2 border-slate-900 text-slate-900 hover:bg-slate-900 hover:text-white",
    };

    const sizes: Record<ButtonSize, string> = {
        sm: "px-4 py-2 text-[10px]",
        md: "px-6 py-2.5 text-xs",
        lg: "px-8 py-3.5 text-sm",
        xl: "px-10 py-4 text-base",
    };

    const classes = $derived(
        `${baseClasses} ${variants[variant] ?? variants.primary} ${sizes[size] ?? sizes.md} ${className}`
    );
    const hasChildren = $derived(children !== undefined);
</script>

{#if href}
    <Link {href} class={classes} {...rest}>
        {#if icon && iconPosition === "left"}
            {#if typeof icon === "string"}
                <i class="{icon} {hasChildren ? 'mr-3' : ''} transition-transform group-hover:-translate-x-1"></i>
            {:else}
                <div class="{hasChildren ? 'mr-3' : ''} transition-transform group-hover:-translate-x-1 text-lg">
                    <svelte:component this={icon} size={18} strokeWidth={2.5} />
                </div>
            {/if}
        {/if}
        {@render children?.()}
        {#if icon && iconPosition === "right"}
            {#if typeof icon === "string"}
                <i class="{icon} {hasChildren ? 'ml-3' : ''} transition-transform group-hover:translate-x-1"></i>
            {:else}
                <div class="{hasChildren ? 'ml-3' : ''} transition-transform group-hover:translate-x-1 text-lg">
                    <svelte:component this={icon} size={18} strokeWidth={2.5} />
                </div>
            {/if}
        {/if}
    </Link>
{:else}
    <button {type} {disabled} class={classes} {...rest}>
        {#if icon && iconPosition === "left"}
            {#if typeof icon === "string"}
                <i class="{icon} {hasChildren ? 'mr-3' : ''}"></i>
            {:else}
                <div class={hasChildren ? "mr-3 text-lg" : "text-lg"}>
                    <svelte:component this={icon} size={18} strokeWidth={2.5} />
                </div>
            {/if}
        {/if}
        {@render children?.()}
        {#if icon && iconPosition === "right"}
            {#if typeof icon === "string"}
                <i class="{icon} {hasChildren ? 'ml-3' : ''}"></i>
            {:else}
                <div class={hasChildren ? "ml-3 text-lg" : "text-lg"}>
                    <svelte:component this={icon} size={18} strokeWidth={2.5} />
                </div>
            {/if}
        {/if}
    </button>
{/if}
