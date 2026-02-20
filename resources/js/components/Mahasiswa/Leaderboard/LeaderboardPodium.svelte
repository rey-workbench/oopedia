<script>
    import { Crown } from "lucide-svelte";

    export let top3 = [];
</script>

<div class="bg-slate-50 pt-20 pb-12 px-8">
    <div
        class="flex justify-center items-end gap-4 md:gap-12 max-w-5xl mx-auto"
    >
        <!-- Rank 2 -->
        <div class="flex-1 flex flex-col items-center order-1">
            {#if top3[1]}
                <div class="text-center mb-8 group">
                    <div class="relative mb-6">
                        <div
                            class="w-20 h-20 md:w-24 md:h-24 bg-slate-200 rounded-[2rem] flex items-center justify-center text-4xl shadow-inner border-4 border-white mx-auto group-hover:scale-110 group-hover:-rotate-3 transition-all duration-500"
                        >
                            🥈
                        </div>
                        <div
                            class="absolute -bottom-2 -right-2 w-10 h-10 bg-white rounded-2xl flex items-center justify-center shadow-lg border-2 border-slate-100 text-[10px] font-bold"
                        >
                            2nd
                        </div>
                    </div>
                    <h5
                        class="font-bold text-slate-800 text-lg mb-1 truncate max-w-[140px] uppercase tracking-widest"
                    >
                        {top3[1].name}
                    </h5>
                    <div
                        class="text-primary-600 font-bold tracking-widest text-sm"
                    >
                        {top3[1].formatted_score} PTS
                    </div>
                </div>
                <div
                    class="w-full h-40 md:h-48 bg-slate-200 rounded-t-[2rem] shadow-inner flex items-center justify-center relative overflow-hidden"
                >
                    <div class="absolute inset-x-0 top-0 h-1 bg-white/20"></div>
                    <div
                        class="text-5xl md:text-7xl font-bold text-white/40 tracking-widest"
                    >
                        2
                    </div>
                </div>
            {/if}
        </div>

        <!-- Rank 1 -->
        <div class="flex-1 flex flex-col items-center order-2">
            {#if top3[0]}
                <div class="text-center mb-8 group relative z-10">
                    <div class="absolute -top-14 left-1/2 -translate-x-1/2">
                        <Crown
                            size={48}
                            class="text-amber-400 animate-bounce drop-shadow-[0_0_15px_rgba(251,191,36,0.5)]"
                        />
                    </div>
                    <div class="relative mb-6">
                        <div
                            class="w-24 h-24 md:w-32 md:h-32 bg-amber-400 rounded-[2.5rem] flex items-center justify-center text-6xl shadow-2xl border-4 border-white mx-auto group-hover:scale-110 transition-all duration-500 ring-[12px] ring-amber-400/20"
                        >
                            🥇
                        </div>
                        <div
                            class="absolute -bottom-2 -right-2 w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-xl border-2 border-amber-50 text-xs font-bold"
                        >
                            1st
                        </div>
                    </div>
                    <h5
                        class="font-bold text-slate-900 text-xl md:text-2xl mb-1 truncate max-w-[180px] uppercase tracking-widest"
                    >
                        {top3[0].name}
                    </h5>
                    <div
                        class="text-amber-600 font-bold tracking-widest text-xl"
                    >
                        {top3[0].formatted_score} PTS
                    </div>
                </div>
                <div
                    class="w-full h-56 md:h-64 bg-amber-400 rounded-t-[3rem] shadow-2xl shadow-amber-200/50 flex items-center justify-center relative overflow-hidden"
                >
                    <div
                        class="absolute inset-0 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-white/30 to-transparent"
                    ></div>
                    <div
                        class="absolute inset-x-0 top-0 h-1.5 bg-white/30"
                    ></div>
                    <div
                        class="text-8xl md:text-9xl font-bold text-white/40 tracking-widest"
                    >
                        1
                    </div>
                </div>
            {/if}
        </div>

        <!-- Rank 3 -->
        <div class="flex-1 flex flex-col items-center order-3">
            {#if top3[2]}
                <div class="text-center mb-8 group">
                    <div class="relative mb-6">
                        <div
                            class="w-20 h-20 md:w-24 md:h-24 bg-rose-200 rounded-[2rem] flex items-center justify-center text-4xl shadow-inner border-4 border-white mx-auto group-hover:scale-110 group-hover:rotate-3 transition-all duration-500"
                        >
                            🥉
                        </div>
                        <div
                            class="absolute -bottom-2 -right-2 w-10 h-10 bg-white rounded-2xl flex items-center justify-center shadow-lg border-2 border-rose-50 text-[10px] font-bold"
                        >
                            3rd
                        </div>
                    </div>
                    <h5
                        class="font-bold text-slate-800 text-lg mb-1 truncate max-w-[140px] uppercase tracking-widest"
                    >
                        {top3[2].name}
                    </h5>
                    <div
                        class="text-primary-600 font-bold tracking-widest text-sm"
                    >
                        {top3[2].formatted_score} PTS
                    </div>
                </div>
                <div
                    class="w-full h-32 md:h-40 bg-rose-300 rounded-t-[2rem] shadow-inner flex items-center justify-center relative overflow-hidden"
                >
                    <div class="absolute inset-x-0 top-0 h-1 bg-white/20"></div>
                    <div
                        class="text-4xl md:text-6xl font-bold text-white/40 tracking-widest"
                    >
                        3
                    </div>
                </div>
            {/if}
        </div>
    </div>
</div>
