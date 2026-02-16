<script>
    export let value = 0;
    export let max = 100;
    export let color = "blue"; // 'blue', 'emerald', 'amber', 'rose'
    export let height = "h-2";

    $: percentage = Math.min(100, Math.max(0, (value / max) * 100));

    const colors = {
        blue: "bg-primary-600",
        emerald: "bg-emerald-600",
        amber: "bg-amber-500",
        rose: "bg-rose-500",
        gray: "bg-slate-500",
    };

    $: bgClass = colors[color] || colors.blue;
</script>

<div class={`w-full bg-slate-200 rounded-full overflow-hidden ${height}`}>
    <div
        class={`h-full transition-all duration-500 ease-out ${bgClass}`}
        style={`width: ${percentage}%`}
    ></div>
</div>
