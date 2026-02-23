<script>
    export let name = "";
    export let size = "md"; // 'sm' | 'md' | 'lg'
    export let dark = false;
    let className = "";
    export { className as class };

    const sizes = {
        sm: "w-8 h-8 text-[10px]",
        md: "w-10 h-10 text-xs",
        lg: "w-20 h-20 text-2xl",
    };

    const radius = {
        sm: "rounded-lg",
        md: "rounded-xl",
        lg: "rounded-[1.5rem]",
    };

    $: initial = name ? name.charAt(0).toUpperCase() : "?";
    $: sizeClass = sizes[size] || sizes.md;
    $: radiusClass = radius[size] || radius.md;
    $: bgClass = dark ? "bg-white/10 text-white" : "bg-slate-900 text-white";
</script>

<div
    class={`${sizeClass} ${radiusClass} ${bgClass} flex items-center justify-center font-bold shadow-lg shadow-slate-200 shrink-0 ${className}`}
>
    {initial}
</div>
