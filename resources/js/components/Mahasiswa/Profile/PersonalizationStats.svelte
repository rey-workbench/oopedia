<script>
    import Card from "@/components/ui/Card.svelte";
    import { Eye, Brain, Zap, Trophy, Target, Flame } from "lucide-svelte";

    export let personalization = {};
</script>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Learning Style Card -->
    <Card
        padding="p-8"
        class="bg-purple-50 border-purple-100 shadow-lg shadow-purple-100/50"
    >
        <div class="space-y-4">
            <div
                class="w-14 h-14 bg-purple-500 text-white rounded-[1.5rem] flex items-center justify-center shadow-xl shadow-purple-200"
            >
                {#if personalization.learning_style === "visual"}
                    <Eye size={28} />
                {:else if personalization.learning_style === "auditory"}
                    <Brain size={28} />
                {:else}
                    <Zap size={28} />
                {/if}
            </div>
            <div>
                <p
                    class="text-[9px] font-bold text-purple-400 uppercase tracking-widest mb-1"
                >
                    Gaya Belajar
                </p>
                <p
                    class="text-2xl font-bold text-purple-900 tracking-widest uppercase"
                >
                    {personalization.learning_style || "Visual"}
                </p>
            </div>
        </div>
    </Card>

    <!-- Current Level Card -->
    <Card
        padding="p-8"
        class="bg-accent-50 border-accent-100 shadow-lg shadow-accent-100/50"
    >
        <div class="space-y-4">
            <div
                class="w-14 h-14 bg-primary-500 text-white rounded-[1.5rem] flex items-center justify-center shadow-xl shadow-primary-200"
            >
                <Trophy size={28} />
            </div>
            <div>
                <p
                    class="text-[9px] font-bold text-primary-400 uppercase tracking-widest mb-1"
                >
                    Level Saat Ini
                </p>
                <p
                    class="text-2xl font-bold text-primary-900 tracking-widest uppercase"
                >
                    {personalization.current_level || "Pemula"}
                </p>
                <p
                    class="text-[10px] font-bold text-primary-400 uppercase tracking-widest mt-2"
                >
                    {personalization.global_xp || 0} XP
                </p>
            </div>
        </div>
    </Card>

    <!-- Accuracy Card -->
    <Card
        padding="p-8"
        class="bg-emerald-50 border-emerald-100 shadow-lg shadow-emerald-100/50"
    >
        <div class="space-y-4">
            <div
                class="w-14 h-14 bg-emerald-500 text-white rounded-[1.5rem] flex items-center justify-center shadow-xl shadow-emerald-200"
            >
                <Target size={28} />
            </div>
            <div>
                <p
                    class="text-[9px] font-bold text-emerald-400 uppercase tracking-widest mb-1"
                >
                    Akurasi
                </p>
                <p class="text-2xl font-bold text-emerald-900 tracking-widest">
                    {personalization.accuracy || 0}%
                </p>
                <p
                    class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest mt-2"
                >
                    {personalization.correct_count ||
                        0}/{personalization.total_questions_answered || 0} Benar
                </p>
            </div>
        </div>
    </Card>

    <!-- Streak Card -->
    <Card
        padding="p-8"
        class="bg-orange-50 border-orange-100 shadow-lg shadow-orange-100/50"
    >
        <div class="space-y-4">
            <div
                class="w-14 h-14 bg-orange-500 text-white rounded-[1.5rem] flex items-center justify-center shadow-xl shadow-orange-200"
            >
                <Flame size={28} />
            </div>
            <div>
                <p
                    class="text-[9px] font-bold text-orange-400 uppercase tracking-widest mb-1"
                >
                    Streak
                </p>
                <p class="text-2xl font-bold text-orange-900 tracking-widest">
                    {personalization.current_streak || 0} 🔥
                </p>
                <p
                    class="text-[10px] font-bold text-orange-400 uppercase tracking-widest mt-2"
                >
                    Max: {personalization.max_streak || 0}
                </p>
            </div>
        </div>
    </Card>
</div>
