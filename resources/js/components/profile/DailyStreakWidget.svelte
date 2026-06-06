<script lang="ts">
    import { Flame, Check, Lock, Clock } from '@lucide/svelte';
    import Card from '@/components/ui/Card.svelte';
    import { useStreakState } from '@/states/Mahasiswa/StreakState.svelte';

    const {
        currentStreak = 0,
        maxStreak = 0,
    }: {
        currentStreak: number;
        maxStreak: number;
    } = $props();

    const state = useStreakState(
        () => currentStreak,
        () => maxStreak
    );
</script>

<!-- Outer wrapper exactly like other sections in Profile/Index.svelte -->
<div id="profile-streak-widget" class="space-y-4">
    <!-- Section heading matches the other section headings in Profile/Index.svelte -->
    <h3 class="text-xl font-bold tracking-widest text-slate-900 uppercase">
        Daily Streak
    </h3>

    <!-- White Card — same style as all other cards on this page -->
    <Card hover={false} overflowHidden={false} padding="p-0" class="group">
        <!-- Header bar inside the card -->
        <div class="relative overflow-hidden rounded-t-[calc(2rem-2px)] bg-white px-8 py-6 text-slate-900 border-b-2 border-slate-100">
            <!-- Decorative overlapping circles -->
            <div class="pointer-events-none absolute -top-10 -right-10 h-40 w-40 rounded-full bg-slate-50"></div>
            <div class="pointer-events-none absolute -bottom-6 -left-4 h-24 w-24 rounded-full bg-slate-50"></div>

            <div class="relative z-10 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl border-2 border-amber-100 bg-amber-50 shadow-inner backdrop-blur-sm text-amber-500">
                        <Flame size={28} strokeWidth={2.5} class="fill-amber-500/20" />
                    </div>
                    <div>
                        <h4 class="font-display text-2xl font-black tracking-tight">Daily Login Streak</h4>
                        <p class="text-sm font-semibold text-slate-500">
                            Login & kuis tiap hari agar streak tidak reset!
                        </p>
                    </div>
                </div>
                <!-- Big streak number, like the value in StatCard -->
                <div class="text-right">
                    <div class="font-display text-5xl font-black tracking-tight drop-shadow-sm text-slate-900">
                        {currentStreak}
                    </div>
                    <div class="flex items-center gap-1.5 justify-end mt-1">
                        <div class="h-1.5 w-1.5 rounded-full bg-amber-500"></div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-500">
                            Hari Aktif · Max {maxStreak}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Day cards section — white bg, same padding as card body -->
        <div class="p-6 md:p-8">
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-5">
                {#each state.daysToShow as day}
                    <div class="
                        relative flex flex-col items-center gap-3 rounded-[1.5rem] border-2 border-b-8 p-4 transition-all duration-200 select-none
                        {day.isCompleted
                            ? 'border-amber-200 border-b-amber-300 bg-amber-50 hover:shadow-md hover:shadow-amber-100'
                            : ''}
                        {day.isCurrentTarget
                            ? 'border-slate-200 border-b-slate-300 bg-white shadow-lg hover:shadow-xl'
                            : ''}
                        {day.isLocked
                            ? 'border-slate-100 border-b-slate-200 bg-slate-50 opacity-60'
                            : ''}
                    ">
                        <!-- Label -->
                        <span class="text-[10px] font-black uppercase tracking-widest
                            {day.isCompleted ? 'text-amber-600' : 'text-slate-400'}">
                            Hari {day.dayNum}
                        </span>

                        <!-- Icon Box — exactly same pattern as StatCard icon box -->
                        <div class="
                            flex h-14 w-14 items-center justify-center rounded-2xl border-2
                            {day.isCompleted
                                ? 'border-amber-100 bg-amber-100 text-amber-600'
                                : ''}
                            {day.isCurrentTarget
                                ? 'border-amber-200 bg-amber-50 text-amber-500'
                                : ''}
                            {day.isLocked
                                ? 'border-slate-100 bg-slate-100 text-slate-400'
                                : ''}
                        ">
                            {#if day.isCompleted}
                                <Check size={24} strokeWidth={3} />
                            {:else if day.isCurrentTarget}
                                <Flame size={24} strokeWidth={2.5} class="animate-pulse fill-amber-500/20" />
                            {:else}
                                <Lock size={20} strokeWidth={2.5} />
                            {/if}
                        </div>

                        <!-- Footer label — same dot + text pattern from StatCard -->
                        {#if day.isCompleted}
                            <div class="flex items-center gap-1.5">
                                <div class="h-1.5 w-1.5 rounded-full bg-amber-500"></div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-amber-600">Selesai</p>
                            </div>
                        {:else if day.isCurrentTarget}
                            <div class="flex items-center gap-1.5">
                                <Clock size={10} class="text-rose-400 animate-pulse" />
                                <p class="font-mono text-[10px] font-black tracking-wide text-slate-700">{state.timeLeftStr}</p>
                            </div>
                        {:else}
                            <div class="flex items-center gap-1.5">
                                <div class="h-1.5 w-1.5 rounded-full bg-slate-300"></div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Terkunci</p>
                            </div>
                        {/if}
                    </div>
                {/each}
            </div>
        </div>

        <!-- Footer — same pattern as card footer in other profile cards -->
        <div class="border-t-2 border-slate-100 bg-slate-50/50 px-8 py-4">
            <div class="flex flex-col gap-2 sm:flex-row sm:gap-8">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <div class="h-1.5 w-1.5 rounded-full bg-amber-500"></div>
                    <span>Selesaikan minimal 1 soal kuis per hari untuk mempertahankan streak</span>
                </div>
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <div class="h-1.5 w-1.5 rounded-full bg-emerald-500"></div>
                    <span>Bonus <strong class="text-slate-700">+50 XP</strong> otomatis saat <code class="text-[10px] bg-slate-200 px-1 py-0.5 rounded text-slate-600">STREAK_BONUS</code> aktif</span>
                </div>
            </div>
        </div>
    </Card>
</div>
