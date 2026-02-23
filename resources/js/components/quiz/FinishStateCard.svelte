<script>
    import { Trophy, Check, Target, Star, Flame, BarChart3, Home } from "lucide-svelte";
    import Button from "@/components/ui/Button.svelte";
    import { ROUTES } from "@/utils/route";

    export let state;
    export let material = {};
    export let answeredCount = 0;
</script>

<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100">
        <div class="bg-emerald-600 p-12 text-center text-white relative">
            <div class="absolute top-0 right-0 p-8 opacity-10">
                <Trophy size={96} class="text-white" />
            </div>
            <div class="relative z-10">
                <div
                    class="w-24 h-24 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl border-4 border-white/30"
                >
                    <Check size={48} class="text-white" />
                </div>
                <h2 class="text-4xl font-bold mb-3 tracking-widest">HEBAT!</h2>
                <p class="text-emerald-50 text-xl font-medium">
                    Kamu sudah menjawab semua soal di materi ini dengan baik.
                </p>
            </div>
        </div>

        {#if !state.isGuest}
            <div class="grid grid-cols-3 gap-0 border-b border-slate-100">
                <div class="p-6 text-center border-r border-slate-100">
                    <div
                        class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center mx-auto mb-2"
                    >
                        <Target size={20} class="text-primary-600" />
                    </div>
                    <div class="text-2xl font-bold text-slate-800">{answeredCount}</div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        Soal Dijawab
                    </div>
                </div>
                <div class="p-6 text-center border-r border-slate-100">
                    <div
                        class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center mx-auto mb-2"
                    >
                        <Star size={20} class="text-amber-500" />
                    </div>
                    <div class="text-2xl font-bold text-slate-800">{state.xp}</div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        Total XP
                    </div>
                </div>
                <div class="p-6 text-center">
                    <div
                        class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center mx-auto mb-2"
                    >
                        <Flame size={20} class="text-orange-500" />
                    </div>
                    <div class="text-2xl font-bold text-slate-800">{state.streak}</div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        Streak
                    </div>
                </div>
            </div>
        {/if}

        <div class="p-10 bg-white text-center space-y-4">
            <Button
                href={ROUTES.MAHASISWA.MATERIALS.QUESTIONS.REVIEW(material.id)}
                variant="secondary"
                icon={BarChart3}
                class="mr-3"
            >
                Review Jawaban
            </Button>
            <Button
                href={ROUTES.MAHASISWA.MATERIALS.SHOW(material.id)}
                variant="primary"
                icon={Home}
                iconPosition="right"
            >
                Kembali ke Materi
            </Button>
        </div>
    </div>
</div>