<script lang="ts">
    interface Props {
        value?: number;
        max?: number;
        color?: 'blue' | 'emerald' | 'amber' | 'rose' | 'gray';
        height?: string;
    }

    let { value = 0, max = 100, color = 'blue', height = 'h-4' }: Props = $props();

    const percentage = $derived(Math.min(100, Math.max(0, (value / max) * 100)));

    const colors = {
        blue: 'bg-primary-500',
        emerald: 'bg-emerald-500',
        amber: 'bg-amber-400',
        rose: 'bg-rose-500',
        gray: 'bg-slate-500',
    };

    const bgClass = $derived(colors[color] || colors.blue);
</script>

<div
    class={`w-full overflow-hidden rounded-full border-4 border-slate-300 bg-slate-200 ${height}`}
    role="progressbar"
    aria-valuemin={0}
    aria-valuemax={max}
    aria-valuenow={Math.round(percentage)}
>
    <div
        class={`relative h-full rounded-full border-b-4 border-black/10 shadow-sm transition-all duration-700 ease-out ${bgClass}`}
        style="width: {percentage}%"
    >
        <div
            class="absolute top-[10%] left-[1%] h-[30%] w-[98%] rounded-full bg-white/30 blur-[0.5px]"
        ></div>
    </div>
</div>
