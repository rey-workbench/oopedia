<script>
    export let variant = "primary";
    export let size = "md";
    let className = "";
    export { className as class };

    const variants = {
        primary: "bg-primary-600 text-white shadow-sm",
        secondary: "bg-slate-100 text-slate-600",
        success: "bg-emerald-100 text-emerald-700",
        danger: "bg-rose-100 text-rose-700",
        warning: "bg-amber-100 text-amber-700",
        info: "bg-primary-50 text-primary-700",
        outline: "bg-transparent border border-slate-200 text-slate-600",
    };

    const sizes = {
        xs: "px-2 py-0.5 text-[8px]",
        sm: "px-3 py-1 text-[10px]",
        md: "px-4 py-1.5 text-xs",
        lg: "px-5 py-2 text-sm",
    };

    $: classes = `inline-flex items-center font-bold uppercase tracking-widest rounded-xl transition-all ${variants[variant] || variants.primary} ${sizes[size] || sizes.md} ${className}`;
</script>

<span class={classes} {...$$restProps}>
    <slot />
</span>
