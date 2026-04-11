<script lang="ts">
    import Card from '@/components/ui/Card.svelte';
    import { Check, Lock, Star, Trophy, Zap } from 'lucide-svelte';
    import type { LevelItem } from '@/types';

    interface Props {
        sortedLevels: LevelItem[];
        allCompleted: boolean;
        class?: string;
        onLevelClick?: (level: LevelItem) => void;
    }

    let {
        sortedLevels,
        allCompleted,
        class: className = '',
        onLevelClick = () => {},
    }: Props = $props();

    let mapW = $state(900);

    const START_Y = 80;
    const GAP_Y = 160;
    const TROPHY_GAP = 140;

    const X_CENTER = $derived(mapW / 2);
    const amplitude = $derived(Math.min(140, mapW * 0.28));
    const X_LEFT = $derived(X_CENTER - amplitude);
    const X_RIGHT = $derived(X_CENTER + amplitude);

    function nodeX(i: number): number {
        return [X_CENTER, X_LEFT, X_RIGHT][i % 3] as number;
    }
    function nodeY(i: number): number {
        return START_Y + i * GAP_Y;
    }

    const trophyY = $derived(
        START_Y +
            sortedLevels.length * GAP_Y +
            (sortedLevels.length > 0 ? TROPHY_GAP - GAP_Y + GAP_Y : 0)
    );
    const totalH = $derived(trophyY + 140);
    const tX = $derived(X_CENTER);

    function cubicBezier(t: number, p0: number, p1: number, p2: number, p3: number): number {
        const u = 1 - t;
        return u * u * u * p0 + 3 * u * u * t * p1 + 3 * u * t * t * p2 + t * t * t * p3;
    }

    interface Dot {
        x: number;
        y: number;
        completed: boolean;
    }

    function trailDots(x1: number, y1: number, x2: number, y2: number, count = 24): Dot[] {
        const dots: Dot[] = [];
        const cp1x = x1;
        const cp1y = (y1 + y2) / 2;
        const cp2x = x2;
        const cp2y = (y1 + y2) / 2;

        for (let i = 1; i < count; i++) {
            const t = i / count;
            dots.push({
                x: cubicBezier(t, x1, cp1x, cp2x, x2),
                y: cubicBezier(t, y1, cp1y, cp2y, y2),
                completed: false,
            });
        }
        return dots;
    }

    const mapDots = $derived.by(() => {
        if (mapW === 0 || sortedLevels.length === 0) return [];

        const result: Dot[] = [];
        for (let i = 0; i < sortedLevels.length; i++) {
            const x1 = nodeX(i);
            const y1 = nodeY(i);
            let x2: number, y2: number;
            if (i < sortedLevels.length - 1) {
                x2 = nodeX(i + 1);
                y2 = nodeY(i + 1);
            } else {
                x2 = X_CENTER;
                y2 = trophyY;
            }
            const completed = sortedLevels[i]?.status === 'completed';
            const segDots = trailDots(x1, y1, x2, y2, 22);
            segDots.forEach((d) => result.push({ ...d, completed }));
        }
        return result;
    });
</script>

<Card padding="p-0" class={`overflow-hidden ${className}`}>
    <div class="pt-8 pb-2 text-center">
        <span
            class="inline-flex items-center gap-2 rounded-full bg-slate-800 px-6 py-2 text-[10px] font-black tracking-[0.25em] text-white uppercase shadow-md"
        >
            <Zap size={14} class="fill-amber-400 text-amber-400" />
            Start
        </span>
    </div>

    <div id="level-map" bind:clientWidth={mapW} class="relative w-full pb-10" style="height: {totalH}px;">
        <svg
            class="pointer-events-none absolute inset-0 w-full"
            style="height: {totalH}px;"
            aria-hidden="true"
            role="presentation"
        >
            {#each mapDots as dot, i (i)}
                <circle cx={dot.x} cy={dot.y} r="4.5" fill={dot.completed ? '#10b981' : '#cbd5e1'}
                ></circle>
            {/each}
        </svg>

        {#each sortedLevels as level, i (level.level)}
            <div
                id="level-node-{level.level}"
                class="absolute flex flex-col items-center"
                style="left: {nodeX(i)}px; top: {nodeY(i)}px; transform: translate(-50%, -50%);"
            >
                {#if level.status === 'locked'}
                    <button
                        type="button"
                        onclick={() => onLevelClick(level)}
                        class="relative flex h-20 w-20 items-center justify-center rounded-full border-2 border-b-[6px] border-slate-300 bg-slate-200 shadow-sm transition-transform active:translate-y-1 sm:h-24 sm:w-24"
                    >
                        <span class="text-2xl font-black text-slate-400 sm:text-3xl"
                            >{level.level}</span
                        >
                        <div
                            class="absolute -right-1 bottom-1 flex h-7 w-7 items-center justify-center rounded-full border-2 border-slate-300 bg-slate-100 shadow-sm sm:h-8 sm:w-8"
                        >
                            <Lock size={14} class="text-slate-400" strokeWidth={3} />
                        </div>
                    </button>
                {:else if level.status === 'completed'}
                    <div class="group flex flex-col items-center">
                        <button
                            type="button"
                            onclick={() => onLevelClick(level)}
                            class="relative flex h-20 w-20 items-center justify-center rounded-full border-2 border-b-[6px] border-emerald-600 bg-emerald-500 shadow-lg shadow-emerald-200 transition-transform hover:scale-105 active:translate-y-1 sm:h-24 sm:w-24"
                        >
                            <span class="text-2xl font-black text-white drop-shadow-sm sm:text-3xl"
                                >{level.level}</span
                            >
                            <div
                                class="absolute -right-1 bottom-0 flex h-8 w-8 items-center justify-center rounded-full border-2 border-emerald-200 bg-white shadow-md sm:h-9 sm:w-9"
                            >
                                <Check size={18} class="text-emerald-500" strokeWidth={4} />
                            </div>
                        </button>
                        <div class="mt-2.5 flex gap-1">
                            {#each Array(3) as _, j (j)}
                                <Star size={13} class="fill-amber-400 text-amber-400" />
                            {/each}
                        </div>
                    </div>
                {:else}
                    <div class="group relative flex flex-col items-center">
                        <button
                            type="button"
                            onclick={() => onLevelClick(level)}
                            class="border-primary-600 bg-primary-500 hover:shadow-primary-200 relative z-10 flex h-20 w-20 items-center justify-center rounded-full border-2 border-b-[6px] shadow-xl transition-all hover:scale-110 active:translate-y-1 sm:h-24 sm:w-24"
                        >
                            <div
                                class="absolute inset-0 rounded-full border-[3px] border-white/20"
                            ></div>
                            <span class="text-2xl font-black text-white drop-shadow-sm sm:text-3xl"
                                >{level.level}</span
                            >
                        </button>
                        <span
                            class="z-10 mt-3 rounded-2xl border-2 border-b-4 border-slate-900 bg-slate-800 px-5 py-2 text-[10px] font-black tracking-widest text-white uppercase shadow-md"
                        >
                            Play
                        </span>
                    </div>
                {/if}
            </div>
        {/each}

        <div
            id="level-trophy"
            class="absolute flex flex-col items-center"
            style="left: {tX}px; top: {trophyY}px; transform: translate(-50%, -50%);"
        >
            <div
                class={`relative flex h-28 w-28 items-center justify-center rounded-full border-2 border-b-6 transition-all duration-700 sm:h-32 sm:w-32 ${allCompleted ? 'scale-110 border-amber-600 bg-amber-400 shadow-xl shadow-amber-200' : 'border-slate-300 bg-slate-200 shadow-sm'}`}
            >
                {#if allCompleted}
                    <div class="absolute inset-0 rounded-full border-4 border-white/30"></div>
                {/if}
                <Trophy
                    size={56}
                    class={allCompleted ? 'z-10 text-white drop-shadow-md' : 'text-slate-400'}
                    strokeWidth={2.5}
                />
            </div>
            {#if allCompleted}
                <div class="mt-4 flex gap-1">
                    {#each Array(5) as _, j (j)}
                        <Star size={16} class="fill-amber-400 text-amber-400 drop-shadow-sm" />
                    {/each}
                </div>
                <span
                    class="mt-2.5 animate-bounce rounded-full border-2 border-b-4 border-amber-600 bg-amber-500 px-5 py-2 text-[11px] font-black tracking-wider text-white uppercase shadow-lg"
                >
                    🎉 Completed!
                </span>
            {:else}
                <span class="mt-4 text-[11px] font-black tracking-widest text-slate-400 uppercase">
                    Finish
                </span>
            {/if}
        </div>
    </div>
</Card>
