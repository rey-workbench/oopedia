<script>
    import UserAvatar from "@/components/ui/UserAvatar.svelte";
    import Badge from "@/components/ui/Badge.svelte";
    import { Crown } from "lucide-svelte";

    let { top3 = [] } = $props();

    // We want to reorder them so it's 2, 1, 3 for a visual podium
    let podiumItems = $derived([
        top3.length > 1 ? top3[1] : null, // 2nd
        top3.length > 0 ? top3[0] : null, // 1st
        top3.length > 2 ? top3[2] : null, // 3rd
    ]);
</script>

<div
    class="bg-gradient-to-b from-slate-900 to-slate-800 p-8 md:p-12 text-white relative flex flex-col items-center overflow-hidden"
>
    <!-- Background accents -->
    <div
        class="absolute inset-0 bg-[url('/img/grid-pattern.svg')] opacity-10 pointer-events-none"
    ></div>
    <div
        class="absolute -top-40 -right-40 w-96 h-96 bg-primary-500/10 blur-[100px] rounded-full pointer-events-none"
    ></div>
    <div
        class="absolute -bottom-40 -left-40 w-96 h-96 bg-accent-500/10 blur-[100px] rounded-full pointer-events-none"
    ></div>

    <div
        class="flex items-end justify-center w-full max-w-4xl mx-auto gap-3 md:gap-6 pt-12 relative z-10"
    >
        {#each podiumItems as item, index}
            {#if index === 0}
                <!-- 2nd Place -->
                <div
                    class="flex flex-col items-center flex-1 z-10 animate-fade-in-up transition-transform hover:-translate-y-2 duration-300"
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
                                class="absolute -top-3 -right-3 w-8 h-8 bg-slate-200 rounded-full flex items-center justify-center font-black text-slate-800 shadow-lg border-2 border-slate-800"
                            >
                                2
                            </div>
                        </div>
                        <div class="text-center mb-5 w-full px-2">
                            <h3
                                class="font-bold text-base md:text-lg text-slate-100 w-full line-clamp-2 md:h-14"
                            >
                                {item.name}
                            </h3>
                            <div
                                class="text-[10px] md:text-xs text-slate-400 font-bold tracking-widest uppercase mt-1 px-2 py-0.5 bg-slate-800/50 rounded-full border border-slate-700/50 inline-block"
                            >
                                {item.formatted_score} pts
                            </div>
                        </div>
                        <div
                            class="w-full bg-gradient-to-t from-slate-800 to-slate-700/80 rounded-t-2xl h-28 md:h-36 border-t border-x border-slate-600/50 relative overflow-hidden backdrop-blur-sm shadow-[0_-5px_15px_rgba(0,0,0,0.3)] flex flex-col items-center justify-start pt-4"
                        >
                            <div
                                class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-slate-300 to-transparent opacity-50"
                            ></div>
                            <div
                                class="text-4xl text-slate-600/30 font-black tracking-tighter"
                            >
                                2
                            </div>
                        </div>
                    {:else}
                        <div
                            class="w-full bg-slate-800/30 rounded-t-2xl h-28 md:h-36 border-t border-x border-slate-700/30 mt-auto backdrop-blur-sm border-dashed"
                        ></div>
                    {/if}
                </div>
            {:else if index === 1}
                <!-- 1st Place -->
                <div
                    class="flex flex-col items-center flex-1 z-20 -mb-2 md:-mb-4 animate-fade-in-up transition-transform hover:-translate-y-2 duration-300"
                    style="animation-delay: 400ms;"
                >
                    {#if item}
                        <div class="relative mb-5 group">
                            <div
                                class="absolute -top-12 left-1/2 -translate-x-1/2 text-amber-400 drop-shadow-[0_0_10px_rgba(251,191,36,0.6)] animate-pulse"
                            >
                                <Crown size={36} strokeWidth={2.5} />
                            </div>
                            <div
                                class="absolute inset-0 bg-amber-400 blur-xl opacity-30 rounded-full group-hover:opacity-50 transition-opacity duration-300"
                            ></div>
                            <UserAvatar
                                name={item.name}
                                size="lg"
                                class="border-[5px] border-amber-400 !bg-gradient-to-br !from-amber-200 !to-amber-500 !text-amber-950 relative scale-110 shadow-[0_0_30px_rgba(251,191,36,0.4)]"
                            />
                            <div
                                class="absolute -top-3 -right-3 md:-right-4 md:-top-4 w-9 h-9 md:w-10 md:h-10 bg-gradient-to-br from-amber-300 to-amber-500 rounded-full flex items-center justify-center font-black text-amber-950 shadow-[0_0_15px_rgba(251,191,36,0.5)] border-2 border-slate-900 text-base md:text-lg"
                            >
                                1
                            </div>
                        </div>
                        <div class="text-center mb-5 w-full px-2">
                            <h3
                                class="font-black text-lg md:text-xl text-amber-50 w-full drop-shadow-md line-clamp-2 md:h-14"
                            >
                                {item.name}
                            </h3>
                            <div
                                class="text-xs text-amber-200 font-bold tracking-widest uppercase mt-1.5 px-3 py-1 bg-amber-950/40 rounded-full border border-amber-500/30 inline-block shadow-inner"
                            >
                                {item.formatted_score} pts
                            </div>
                        </div>
                        <div
                            class="w-full bg-gradient-to-t from-amber-900/80 to-amber-600/40 rounded-t-2xl h-40 md:h-48 border-t-2 border-x border-amber-500/50 relative overflow-hidden backdrop-blur-md shadow-[0_-10px_30px_rgba(245,158,11,0.2)] flex flex-col items-center justify-start pt-4"
                        >
                            <div
                                class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-amber-300 to-transparent opacity-80"
                            ></div>
                            <div
                                class="text-5xl text-amber-500/20 font-black tracking-tighter"
                            >
                                1
                            </div>
                        </div>
                    {:else}
                        <div
                            class="w-full bg-slate-800/30 rounded-t-2xl h-40 md:h-48 border-t border-x border-slate-700/30 mt-auto backdrop-blur-sm border-dashed"
                        ></div>
                    {/if}
                </div>
            {:else}
                <!-- 3rd Place -->
                <div
                    class="flex flex-col items-center flex-1 z-10 animate-fade-in-up transition-transform hover:-translate-y-2 duration-300"
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
                                class="absolute -top-3 -right-3 w-8 h-8 bg-rose-300 rounded-full flex items-center justify-center font-black text-rose-950 shadow-lg border-2 border-slate-800"
                            >
                                3
                            </div>
                        </div>
                        <div class="text-center mb-5 w-full px-2">
                            <h3
                                class="font-bold text-base md:text-lg text-slate-100 w-full line-clamp-2 md:h-14"
                            >
                                {item.name}
                            </h3>
                            <div
                                class="text-[10px] md:text-xs text-slate-400 font-bold tracking-widest uppercase mt-1 px-2 py-0.5 bg-slate-800/50 rounded-full border border-slate-700/50 inline-block"
                            >
                                {item.formatted_score} pts
                            </div>
                        </div>
                        <div
                            class="w-full bg-gradient-to-t from-slate-800 to-slate-700/60 rounded-t-2xl h-24 md:h-32 border-t border-x border-rose-900/40 relative overflow-hidden backdrop-blur-sm shadow-[0_-5px_15px_rgba(0,0,0,0.2)] flex flex-col items-center justify-start pt-4"
                        >
                            <div
                                class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-rose-400 to-transparent opacity-40"
                            ></div>
                            <div
                                class="text-4xl text-slate-600/30 font-black tracking-tighter"
                            >
                                3
                            </div>
                        </div>
                    {:else}
                        <div
                            class="w-full bg-slate-800/30 rounded-t-2xl h-24 md:h-32 border-t border-x border-slate-700/30 mt-auto backdrop-blur-sm border-dashed"
                        ></div>
                    {/if}
                </div>
            {/if}
        {/each}
    </div>
</div>

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
