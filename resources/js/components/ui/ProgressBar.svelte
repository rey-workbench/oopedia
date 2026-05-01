<script lang="ts">
    interface Props {
        value?: number;
        max?: number;
        color?: 'blue' | 'emerald' | 'amber' | 'rose' | 'gray' | 'accent';
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
        accent: 'bg-accent-500',
    };

    const bgClass = $derived(colors[color] || colors.blue);
</script>

<div
    class={`w-full overflow-hidden rounded-full border-2 border-slate-200 bg-slate-100 p-1 shadow-inner ${height}`}
    role="progressbar"
    aria-valuemin={0}
    aria-valuemax={max}
    aria-valuenow={Math.round(percentage)}
>
    <div
        class={`relative h-full rounded-full border-b-6 border-black/15 shadow-sm transition-all duration-1000 cubic-bezier(0.34, 1.56, 0.64, 1) ${bgClass}`}
        style="width: {percentage}%"
    >
        <!-- Duo Gloss Effect -->
        <div
            class="absolute top-[10%] left-[1.5%] h-[25%] w-[97%] rounded-full bg-white/40 blur-[0.3px]"
        ></div>
        
        <!-- Animated Pulse at the tip -->
        {#if percentage > 0 && percentage < 100}
            <div class="absolute right-0 top-0 h-full w-2 bg-white/30 blur-sm animate-pulse"></div>
        {/if}
    </div>
</div>
