<script lang="ts">
    import UserAvatar from '@/components/ui/UserAvatar.svelte';
    import { Crown } from 'lucide-svelte';
    import type { LeaderboardEntry } from '@/types';

    let { top3 = [] }: { top3: LeaderboardEntry[] } = $props();

    // We want to reorder them so it's 2, 1, 3 for a visual podium
    let podiumItems = $derived([
        top3.length > 1 ? top3[1] : null, // 2nd
        top3.length > 0 ? top3[0] : null, // 1st
        top3.length > 2 ? top3[2] : null, // 3rd
    ]);
</script>

<style>
    @keyframes fade-in-up {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in-up {
        animation: fade-in-up 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0;
    }
</style>

<div
    class="relative flex flex-col items-center overflow-hidden bg-gradient-to-b from-slate-900 to-slate-800 p-8 text-white md:p-12"
>
    <!-- Background accents -->
    <div
        class="pointer-events-none absolute inset-0 bg-[url('/img/grid-pattern.svg')] opacity-10"
    ></div>
    <div
        class="bg-primary-500/10 pointer-events-none absolute -top-40 -right-40 h-96 w-96 rounded-full blur-[100px]"
    ></div>
    <div
        class="bg-accent-500/10 pointer-events-none absolute -bottom-40 -left-40 h-96 w-96 rounded-full blur-[100px]"
    ></div>

    <div
        class="relative z-10 mx-auto flex w-full max-w-4xl items-end justify-center gap-3 pt-12 md:gap-6"
    >
        {#each podiumItems as item, index}
            {#if index === 0}
                <!-- 2nd Place -->
                <div
                    class="animate-fade-in-up z-10 flex flex-1 flex-col items-center transition-transform duration-300 hover:-translate-y-2"
                    style="animation-delay: 200ms;"
                >
                    {#if item}
                        <div class="relative mb-3">
                            <UserAvatar
                                name={item.name}
                                size="lg"
                                class="border-4 border-slate-300 !bg-slate-200 !text-slate-800 shadow-[0_0_20px_rgba(203,213,225,0.3)]"
                            />
                            <div
                                class="absolute -top-3 -right-3 flex h-8 w-8 items-center justify-center rounded-full border-2 border-slate-800 bg-slate-200 font-black text-slate-800 shadow-lg"
                            >
                                2
                            </div>
                        </div>
                        <div class="mb-5 w-full px-2 text-center">
                            <h3
                                class="line-clamp-2 w-full text-base font-bold text-slate-100 md:h-14 md:text-lg"
                            >
                                {item.name}
                            </h3>
                            <div
                                class="mt-1 inline-block rounded-full border border-slate-700/50 bg-slate-800/50 px-2 py-0.5 text-[10px] font-bold tracking-widest text-slate-400 uppercase md:text-xs"
                            >
                                {item.formatted_score} pts
                            </div>
                        </div>
                        <div
                            class="relative flex h-28 w-full flex-col items-center justify-start overflow-hidden rounded-t-2xl border-x border-t border-slate-600/50 bg-gradient-to-t from-slate-800 to-slate-700/80 pt-4 shadow-[0_-5px_15px_rgba(0,0,0,0.3)] backdrop-blur-sm md:h-36"
                        >
                            <div
                                class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-slate-300 to-transparent opacity-50"
                            ></div>
                            <div class="text-4xl font-black tracking-tighter text-slate-600/30">
                                2
                            </div>
                        </div>
                    {:else}
                        <div
                            class="mt-auto h-28 w-full rounded-t-2xl border-x border-t border-dashed border-slate-700/30 bg-slate-800/30 backdrop-blur-sm md:h-36"
                        ></div>
                    {/if}
                </div>
            {:else if index === 1}
                <!-- 1st Place -->
                <div
                    class="animate-fade-in-up z-20 -mb-2 flex flex-1 flex-col items-center transition-transform duration-300 hover:-translate-y-2 md:-mb-4"
                    style="animation-delay: 400ms;"
                >
                    {#if item}
                        <div class="group relative mb-5">
                            <div
                                class="absolute -top-12 left-1/2 -translate-x-1/2 animate-pulse text-amber-400 drop-shadow-[0_0_10px_rgba(251,191,36,0.6)]"
                            >
                                <Crown size={36} strokeWidth={2.5} />
                            </div>
                            <div
                                class="absolute inset-0 rounded-full bg-amber-400 opacity-30 blur-xl transition-opacity duration-300 group-hover:opacity-50"
                            ></div>
                            <UserAvatar
                                name={item.name}
                                size="lg"
                                class="relative scale-110 border-[5px] border-amber-400 !bg-gradient-to-br !from-amber-200 !to-amber-500 !text-amber-950 shadow-[0_0_30px_rgba(251,191,36,0.4)]"
                            />
                            <div
                                class="absolute -top-3 -right-3 flex h-9 w-9 items-center justify-center rounded-full border-2 border-slate-900 bg-gradient-to-br from-amber-300 to-amber-500 text-base font-black text-amber-950 shadow-[0_0_15px_rgba(251,191,36,0.5)] md:-top-4 md:-right-4 md:h-10 md:w-10 md:text-lg"
                            >
                                1
                            </div>
                        </div>
                        <div class="mb-5 w-full px-2 text-center">
                            <h3
                                class="line-clamp-2 w-full text-lg font-black text-amber-50 drop-shadow-md md:h-14 md:text-xl"
                            >
                                {item.name}
                            </h3>
                            <div
                                class="mt-1.5 inline-block rounded-full border border-amber-500/30 bg-amber-950/40 px-3 py-1 text-xs font-bold tracking-widest text-amber-200 uppercase shadow-inner"
                            >
                                {item.formatted_score} pts
                            </div>
                        </div>
                        <div
                            class="relative flex h-40 w-full flex-col items-center justify-start overflow-hidden rounded-t-2xl border-x border-t-2 border-amber-500/50 bg-gradient-to-t from-amber-900/80 to-amber-600/40 pt-4 shadow-[0_-10px_30px_rgba(245,158,11,0.2)] backdrop-blur-md md:h-48"
                        >
                            <div
                                class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-amber-300 to-transparent opacity-80"
                            ></div>
                            <div class="text-5xl font-black tracking-tighter text-amber-500/20">
                                1
                            </div>
                        </div>
                    {:else}
                        <div
                            class="mt-auto h-40 w-full rounded-t-2xl border-x border-t border-dashed border-slate-700/30 bg-slate-800/30 backdrop-blur-sm md:h-48"
                        ></div>
                    {/if}
                </div>
            {:else}
                <!-- 3rd Place -->
                <div
                    class="animate-fade-in-up z-10 flex flex-1 flex-col items-center transition-transform duration-300 hover:-translate-y-2"
                    style="animation-delay: 0ms;"
                >
                    {#if item}
                        <div class="relative mb-3">
                            <UserAvatar
                                name={item.name}
                                size="lg"
                                class="border-4 border-rose-400 !bg-rose-300 !text-rose-950 shadow-[0_0_20px_rgba(251,113,133,0.3)]"
                            />
                            <div
                                class="absolute -top-3 -right-3 flex h-8 w-8 items-center justify-center rounded-full border-2 border-slate-800 bg-rose-300 font-black text-rose-950 shadow-lg"
                            >
                                3
                            </div>
                        </div>
                        <div class="mb-5 w-full px-2 text-center">
                            <h3
                                class="line-clamp-2 w-full text-base font-bold text-slate-100 md:h-14 md:text-lg"
                            >
                                {item.name}
                            </h3>
                            <div
                                class="mt-1 inline-block rounded-full border border-slate-700/50 bg-slate-800/50 px-2 py-0.5 text-[10px] font-bold tracking-widest text-slate-400 uppercase md:text-xs"
                            >
                                {item.formatted_score} pts
                            </div>
                        </div>
                        <div
                            class="relative flex h-24 w-full flex-col items-center justify-start overflow-hidden rounded-t-2xl border-x border-t border-rose-900/40 bg-gradient-to-t from-slate-800 to-slate-700/60 pt-4 shadow-[0_-5px_15px_rgba(0,0,0,0.2)] backdrop-blur-sm md:h-32"
                        >
                            <div
                                class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-rose-400 to-transparent opacity-40"
                            ></div>
                            <div class="text-4xl font-black tracking-tighter text-slate-600/30">
                                3
                            </div>
                        </div>
                    {:else}
                        <div
                            class="mt-auto h-24 w-full rounded-t-2xl border-x border-t border-dashed border-slate-700/30 bg-slate-800/30 backdrop-blur-sm md:h-32"
                        ></div>
                    {/if}
                </div>
            {/if}
        {/each}
    </div>
</div>
