<script lang="ts">
    import Card from '@/components/ui/Card.svelte';
    import { Link } from '@inertiajs/svelte';
    import { Check, Lock, Star, Trophy, Zap } from 'lucide-svelte';
    import type { Material, LevelItem } from '@/types';

    interface Props {
        material: Material;
        sortedLevels: LevelItem[];
        allCompleted: boolean;
        class?: string;
    }

    let { material, sortedLevels, allCompleted, class: className = '' }: Props = $props();

    // ============================================
    //   MAP COORDINATE SYSTEM
    // ============================================
    const MAP_W = 900;
    const START_Y = 100;
    const GAP_Y = 200;
    const TROPHY_GAP = 160;

    const X_CENTER = MAP_W / 2;
    const X_LEFT = 180;
    const X_RIGHT = MAP_W - 180;

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
    const totalH = $derived(trophyY + 120);
    const tPctX = $derived((X_CENTER / MAP_W) * 100);
    const tPctY = $derived((trophyY / totalH) * 100);

    // ============================================
    //   BEZIER DOT TRAIL CALCULATION
    // ============================================
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
            const segDots = trailDots(x1, y1, x2, y2, 26);
            segDots.forEach((d) => result.push({ ...d, completed }));
        }
        return result;
    });
</script>

<Card padding="p-0" class={`overflow-hidden ${className}`}>
    <!-- Start Badge -->
    <div class="pt-10 pb-3 text-center">
        <span
            class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-6 py-2 text-[10px] font-black tracking-[0.25em] text-white uppercase shadow-lg"
        >
            <Zap size={12} />
            Start
        </span>
        <div class="mx-auto mt-2 h-5 w-px bg-slate-200"></div>
    </div>

    <!-- Map Area -->
    <div class="relative w-full px-6 pb-10" style="height: {totalH}px;">
        <!-- SVG DOT TRAIL LAYER -->
        <svg
            class="pointer-events-none absolute inset-0 w-full"
            viewBox="0 0 {MAP_W} {totalH}"
            preserveAspectRatio="xMidYMid meet"
            style="height: {totalH}px;"
        >
            {#each mapDots as dot, i (i)}
                <circle
                    cx={dot.x}
                    cy={dot.y}
                    r="4"
                    fill={dot.completed ? '#6ee7b7' : '#e2e8f0'}
                    opacity={dot.completed ? 0.9 : 0.6}
                ></circle>
            {/each}
        </svg>

        <!-- HTML NODE LAYER -->
        {#each sortedLevels as level, i (level.level)}
            {@const cx = nodeX(i)}
            {@const cy = nodeY(i)}
            {@const pctX = (cx / MAP_W) * 100}
            {@const pctY = (cy / totalH) * 100}

            <div
                class="absolute flex flex-col items-center"
                style="left: {pctX}%; top: {pctY}%; transform: translate(-50%, -50%);"
            >
                {#if level.status === 'locked'}
                    <!-- LOCKED NODE -->
                    <div
                        class="relative flex h-[5.5rem] w-[5.5rem] items-center justify-center rounded-full border-[3px] border-slate-200 bg-slate-50 shadow-sm"
                    >
                        <span class="text-3xl font-black text-slate-300">{level.level}</span>
                        <div
                            class="absolute -right-1 -bottom-1 flex h-7 w-7 items-center justify-center rounded-full border-2 border-slate-200 bg-white shadow-sm"
                        >
                            <Lock size={12} class="text-slate-300" />
                        </div>
                    </div>
                {:else if level.status === 'completed'}
                    <!-- COMPLETED NODE -->
                    <Link
                        href={`/mahasiswa/materials/${material.id}/questions?question=${level.question_id}`}
                        class="group flex flex-col items-center"
                    >
                        <div
                            class="relative flex h-[5.5rem] w-[5.5rem] items-center justify-center rounded-full border-4 border-white bg-emerald-500 shadow-lg shadow-emerald-200 transition-all duration-300 group-hover:scale-110 group-hover:shadow-xl group-hover:shadow-emerald-300"
                        >
                            <span class="text-3xl font-black text-white drop-shadow-sm"
                                >{level.level}</span
                            >
                            <div
                                class="absolute -right-1 -bottom-1.5 flex h-8 w-8 items-center justify-center rounded-full border-2 border-emerald-100 bg-white shadow-md"
                            >
                                <Check size={16} class="text-emerald-500" strokeWidth={3} />
                            </div>
                        </div>
                        <div class="mt-2.5 flex gap-1">
                            {#each Array(3) as _, j (j)}
                                <Star size={13} class="fill-amber-400 text-amber-400" />
                            {/each}
                        </div>
                    </Link>
                {:else}
                    <!-- ACTIVE / PLAYABLE NODE -->
                    <Link
                        href={`/mahasiswa/materials/${material.id}/questions?question=${level.question_id}`}
                        class="group relative flex flex-col items-center"
                    >
                        <!-- Pulse ring -->
                        <div class="absolute inset-0 flex items-start justify-center">
                            <div
                                class="bg-primary-400/25 h-28 w-28 animate-ping rounded-full"
                                style="animation-duration: 2s;"
                            ></div>
                        </div>
                        <div
                            class="bg-primary-600 ring-primary-100 shadow-primary-900/20 group-hover:bg-primary-700 relative z-10 flex h-[5.5rem] w-[5.5rem] items-center justify-center rounded-full border-4 border-white shadow-xl ring-4 transition-all duration-300 group-hover:scale-110"
                        >
                            <span class="text-3xl font-black text-white drop-shadow-sm"
                                >{level.level}</span
                            >
                        </div>
                        <span
                            class="z-10 mt-3 rounded-full bg-slate-900 px-5 py-1.5 text-[10px] font-black tracking-wider text-white uppercase shadow-lg"
                            >Play</span
                        >
                    </Link>
                {/if}
            </div>
        {/each}

        <!-- TROPHY NODE -->
        <div
            class="absolute flex flex-col items-center"
            style="left: {tPctX}%; top: {tPctY}%; transform: translate(-50%, -50%);"
        >
            <div
                class={`flex h-28 w-28 items-center justify-center rounded-full border-4 border-white shadow-xl transition-all duration-700 ${allCompleted ? 'scale-110 bg-amber-400 shadow-amber-200' : 'bg-slate-50 shadow-slate-200'}`}
            >
                <Trophy
                    size={48}
                    class={allCompleted ? 'text-white drop-shadow-md' : 'text-slate-300'}
                />
            </div>
            {#if allCompleted}
                <div class="mt-3 flex gap-1">
                    {#each Array(5) as _, j (j)}
                        <Star size={14} class="fill-amber-400 text-amber-400" />
                    {/each}
                </div>
                <span
                    class="mt-1.5 animate-bounce rounded-full bg-amber-500 px-5 py-1.5 text-[10px] font-black tracking-wider text-white uppercase shadow-lg"
                >
                    🎉 Completed!
                </span>
            {:else}
                <span class="mt-3 text-[10px] font-bold tracking-widest text-slate-300 uppercase">
                    Finish
                </span>
            {/if}
        </div>
    </div>
</Card>
