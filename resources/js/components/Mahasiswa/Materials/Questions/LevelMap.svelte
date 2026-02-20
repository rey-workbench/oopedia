<script>
    import Card from "@/components/ui/Card.svelte";
    import { Link } from "@inertiajs/svelte";
    import { Lock, Check, Star, Trophy, Zap } from "lucide-svelte";

    export let material;
    export let sortedLevels = [];
    export let allCompleted = false;

    // ============================================
    //   MAP COORDINATE SYSTEM
    // ============================================
    const MAP_W = 900;
    const NODE_R = 44;
    const START_Y = 100;
    const GAP_Y = 200;
    const TROPHY_GAP = 160;

    const X_CENTER = MAP_W / 2;
    const X_LEFT = 180;
    const X_RIGHT = MAP_W - 180;

    function nodeX(i) {
        return [X_CENTER, X_LEFT, X_RIGHT][i % 3];
    }
    function nodeY(i) {
        return START_Y + i * GAP_Y;
    }

    $: trophyY =
        START_Y +
        sortedLevels.length * GAP_Y +
        (sortedLevels.length > 0 ? TROPHY_GAP - GAP_Y + GAP_Y : 0);
    $: totalH = trophyY + 120;
    $: tPctX = (X_CENTER / MAP_W) * 100;
    $: tPctY = (trophyY / totalH) * 100;

    // ============================================
    //   BEZIER DOT TRAIL CALCULATION
    // ============================================
    function cubicBezier(t, p0, p1, p2, p3) {
        const u = 1 - t;
        return (
            u * u * u * p0 +
            3 * u * u * t * p1 +
            3 * u * t * t * p2 +
            t * t * t * p3
        );
    }

    function trailDots(x1, y1, x2, y2, count = 24) {
        const dots = [];
        const cp1x = x1;
        const cp1y = (y1 + y2) / 2;
        const cp2x = x2;
        const cp2y = (y1 + y2) / 2;

        for (let i = 1; i < count; i++) {
            const t = i / count;
            dots.push({
                x: cubicBezier(t, x1, cp1x, cp2x, x2),
                y: cubicBezier(t, y1, cp1y, cp2y, y2),
            });
        }
        return dots;
    }

    $: mapDots = (() => {
        const result = [];
        for (let i = 0; i < sortedLevels.length; i++) {
            const x1 = nodeX(i);
            const y1 = nodeY(i);
            let x2, y2;
            if (i < sortedLevels.length - 1) {
                x2 = nodeX(i + 1);
                y2 = nodeY(i + 1);
            } else {
                x2 = X_CENTER;
                y2 = trophyY;
            }
            const completed = sortedLevels[i].status === "completed";
            const segDots = trailDots(x1, y1, x2, y2, 26);
            segDots.forEach((d) => result.push({ ...d, completed }));
        }
        return result;
    })();

    function diffStyle(d) {
        if (d === "beginner")
            return { color: "#10b981", label: "Beginner", bg: "emerald" };
        if (d === "medium")
            return { color: "#f59e0b", label: "Medium", bg: "amber" };
        if (d === "hard")
            return { color: "#ef4444", label: "Hard", bg: "rose" };
        return { color: "#004e98", label: "Level", bg: "primary" };
    }
</script>

<Card padding="p-0" class="overflow-hidden">
    <!-- Start Badge -->
    <div class="text-center pt-10 pb-3">
        <span
            class="inline-flex items-center gap-2 px-6 py-2 bg-slate-900 text-white rounded-full text-[10px] font-black uppercase tracking-[0.25em] shadow-lg"
        >
            <Zap size={12} />
            Start
        </span>
        <div class="w-px h-5 bg-slate-200 mx-auto mt-2"></div>
    </div>

    <!-- Map Area -->
    <div class="relative w-full px-6 pb-10" style="height: {totalH}px;">
        <!-- SVG DOT TRAIL LAYER -->
        <svg
            class="absolute inset-0 w-full pointer-events-none"
            viewBox="0 0 {MAP_W} {totalH}"
            preserveAspectRatio="xMidYMid meet"
            style="height: {totalH}px;"
        >
            {#each mapDots as dot}
                <circle
                    cx={dot.x}
                    cy={dot.y}
                    r="4"
                    fill={dot.completed ? "#6ee7b7" : "#e2e8f0"}
                    opacity={dot.completed ? 0.9 : 0.6}
                />
            {/each}
        </svg>

        <!-- HTML NODE LAYER -->
        {#each sortedLevels as level, i}
            {@const cx = nodeX(i)}
            {@const cy = nodeY(i)}
            {@const pctX = (cx / MAP_W) * 100}
            {@const pctY = (cy / totalH) * 100}
            {@const ds = diffStyle(level.difficulty)}

            <div
                class="absolute flex flex-col items-center"
                style="left: {pctX}%; top: {pctY}%; transform: translate(-50%, -50%);"
            >
                {#if level.status === "locked"}
                    <!-- LOCKED NODE -->
                    <div
                        class="w-[5.5rem] h-[5.5rem] rounded-full bg-slate-50 border-[3px] border-slate-200 flex items-center justify-center relative shadow-sm"
                    >
                        <span class="text-3xl font-black text-slate-300"
                            >{level.level}</span
                        >
                        <div
                            class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full bg-white border-2 border-slate-200 flex items-center justify-center shadow-sm"
                        >
                            <Lock size={12} class="text-slate-300" />
                        </div>
                    </div>
                    <span
                        class="mt-3 text-[10px] font-bold text-slate-300 uppercase tracking-widest"
                        >{ds.label}</span
                    >
                {:else if level.status === "completed"}
                    <!-- COMPLETED NODE -->
                    <Link
                        href={`/mahasiswa/materials/${material.id}/questions?question=${level.question_id}`}
                        class="group flex flex-col items-center"
                    >
                        <div
                            class="w-[5.5rem] h-[5.5rem] rounded-full bg-emerald-500 border-4 border-white flex items-center justify-center relative shadow-lg shadow-emerald-200 group-hover:scale-110 group-hover:shadow-xl group-hover:shadow-emerald-300 transition-all duration-300"
                        >
                            <span
                                class="text-3xl font-black text-white drop-shadow-sm"
                                >{level.level}</span
                            >
                            <div
                                class="absolute -bottom-1.5 -right-1 w-8 h-8 rounded-full bg-white border-2 border-emerald-100 flex items-center justify-center shadow-md"
                            >
                                <Check
                                    size={16}
                                    class="text-emerald-500"
                                    strokeWidth={3}
                                />
                            </div>
                        </div>
                        <div class="flex gap-1 mt-2.5">
                            {#each Array(3) as _}
                                <Star
                                    size={13}
                                    class="text-amber-400 fill-amber-400"
                                />
                            {/each}
                        </div>
                        <span
                            class="mt-1 text-[10px] font-bold text-emerald-500 uppercase tracking-widest"
                            >{ds.label}</span
                        >
                    </Link>
                {:else}
                    <!-- ACTIVE / PLAYABLE NODE -->
                    <Link
                        href={`/mahasiswa/materials/${material.id}/questions?question=${level.question_id}`}
                        class="group flex flex-col items-center relative"
                    >
                        <!-- Pulse ring -->
                        <div
                            class="absolute inset-0 flex items-start justify-center"
                        >
                            <div
                                class="w-28 h-28 rounded-full bg-primary-400/25 animate-ping"
                                style="animation-duration: 2s;"
                            ></div>
                        </div>
                        <div
                            class="w-[5.5rem] h-[5.5rem] rounded-full bg-primary-600 border-4 border-white ring-4 ring-primary-100 flex items-center justify-center relative shadow-xl shadow-primary-900/20 group-hover:scale-110 group-hover:bg-primary-700 transition-all duration-300 z-10"
                        >
                            <span
                                class="text-3xl font-black text-white drop-shadow-sm"
                                >{level.level}</span
                            >
                        </div>
                        <span
                            class="mt-3 px-5 py-1.5 bg-slate-900 text-white rounded-full text-[10px] font-black uppercase tracking-wider shadow-lg z-10"
                            >Play</span
                        >
                        <span
                            class="mt-1.5 text-[10px] font-bold text-primary-600 uppercase tracking-widest z-10"
                            >{ds.label}</span
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
                class={`w-28 h-28 rounded-full border-4 border-white flex items-center justify-center shadow-xl transition-all duration-700 ${allCompleted ? "bg-amber-400 shadow-amber-200 scale-110" : "bg-slate-50 shadow-slate-200"}`}
            >
                <Trophy
                    size={48}
                    class={allCompleted
                        ? "text-white drop-shadow-md"
                        : "text-slate-300"}
                />
            </div>
            {#if allCompleted}
                <div class="mt-3 flex gap-1">
                    {#each Array(5) as _}
                        <Star size={14} class="text-amber-400 fill-amber-400" />
                    {/each}
                </div>
                <span
                    class="mt-1.5 px-5 py-1.5 bg-amber-500 text-white rounded-full text-[10px] font-black uppercase tracking-wider shadow-lg animate-bounce"
                >
                    🎉 Completed!
                </span>
            {:else}
                <span
                    class="mt-3 text-[10px] font-bold text-slate-300 uppercase tracking-widest"
                    >Finish</span
                >
            {/if}
        </div>
    </div>
</Card>
