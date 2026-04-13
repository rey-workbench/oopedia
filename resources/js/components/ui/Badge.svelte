<script lang="ts">
    import type { Snippet } from 'svelte';

    type BadgeVariant =
        | 'primary'
        | 'secondary'
        | 'success'
        | 'danger'
        | 'warning'
        | 'info'
        | 'outline';
    type BadgeSize = 'xs' | 'sm' | 'md' | 'lg';

    interface Props {
        variant?: BadgeVariant;
        size?: BadgeSize;
        class?: string;
        children?: Snippet;
        [key: string]: any;
    }

    let {
        variant = 'primary',
        size = 'md',
        class: className = '',
        children,
        ...rest
    }: Props = $props();

    const variants: Record<BadgeVariant, string> = {
        primary: 'bg-primary-500 text-white border-2 border-primary-600 border-b-4',
        secondary: 'bg-slate-100 text-slate-700 border-2 border-slate-200 border-b-4',
        success: 'bg-emerald-500 text-white border-2 border-emerald-600 border-b-4',
        danger: 'bg-rose-500 text-white border-2 border-rose-600 border-b-4',
        warning: 'bg-amber-400 text-amber-950 border-2 border-amber-500 border-b-4',
        info: 'bg-sky-500 text-white border-2 border-sky-600 border-b-4',
        outline: 'bg-white border-2 border-slate-200 text-slate-600 border-b-4',
    };

    const sizes: Record<BadgeSize, string> = {
        xs: 'px-2 py-0.5 text-[9px]',
        sm: 'px-2.5 py-1 text-[10px]',
        md: 'px-3.5 py-1.5 text-xs',
        lg: 'px-4 py-2 text-sm',
    };

    const classes = $derived(
        `inline-flex items-center font-black uppercase tracking-widest rounded-full transition-all ${variants[variant] || variants.primary} ${sizes[size] || sizes.md} ${className}`
    );
</script>

<span class={classes} {...rest}>
    {@render children?.()}
</span>
