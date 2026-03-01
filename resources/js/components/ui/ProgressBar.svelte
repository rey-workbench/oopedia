<script lang="ts">
    interface Props {
        value?: number;
        max?: number;
        color?: "blue" | "emerald" | "amber" | "rose" | "gray";
        height?: string;
    }

    let {
        value = 0,
        max = 100,
        color = "blue",
        height = "h-2",
    }: Props = $props();

    const percentage = $derived(
        Math.min(100, Math.max(0, (value / max) * 100)),
    );

    const colors = {
        blue: "bg-primary-600",
        emerald: "bg-emerald-600",
        amber: "bg-amber-500",
        rose: "bg-rose-500",
        gray: "bg-slate-500",
    };

    const bgClass = $derived(colors[color] || colors.blue);
</script>

<div class="w-full bg-slate-200 rounded-full overflow-hidden {height}">
    <div
        class="h-full transition-all duration-500 ease-out {bgClass}"
        style="width: {percentage}%"
    ></div>
</div>
