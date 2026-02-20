<script>
    import Card from "@/components/ui/Card.svelte";
    import { CheckCircle, XCircle, Lightbulb, Zap } from "lucide-svelte";

    export let personalization = {};
</script>

<Card padding="p-8 md:p-12" class="bg-slate-50 border-slate-100">
    <div slot="header">
        <h4 class="text-sm font-bold text-slate-900 uppercase tracking-widest">
            Statistik Pembelajaran Detail
        </h4>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Questions Answered -->
        <div class="space-y-4">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-primary-100 text-primary-600 rounded-xl flex items-center justify-center"
                >
                    <CheckCircle size={24} />
                </div>
                <div>
                    <p
                        class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"
                    >
                        Total Soal Dijawab
                    </p>
                    <p
                        class="text-3xl font-bold text-slate-900 tracking-widest"
                    >
                        {personalization.total_questions_answered || 0}
                    </p>
                </div>
            </div>
        </div>

        <!-- Correct Answers -->
        <div class="space-y-4">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center"
                >
                    <CheckCircle size={24} />
                </div>
                <div>
                    <p
                        class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"
                    >
                        Jawaban Benar
                    </p>
                    <p
                        class="text-3xl font-bold text-emerald-600 tracking-widest"
                    >
                        {personalization.correct_count || 0}
                    </p>
                </div>
            </div>
        </div>

        <!-- Wrong Answers -->
        <div class="space-y-4">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center"
                >
                    <XCircle size={24} />
                </div>
                <div>
                    <p
                        class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"
                    >
                        Jawaban Salah
                    </p>
                    <p class="text-3xl font-bold text-rose-600 tracking-widest">
                        {personalization.wrong_count || 0}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div
        class="mt-8 pt-8 border-t border-slate-100 grid grid-cols-1 md:grid-cols-2 gap-8"
    >
        <!-- Hints Usage -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div
                    class="w-10 h-10 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center"
                >
                    <Lightbulb size={20} />
                </div>
                <div>
                    <p
                        class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"
                    >
                        Hints Digunakan
                    </p>
                    <p class="text-xl font-bold text-slate-900 tracking-widest">
                        {personalization.hints_used_count || 0}
                    </p>
                </div>
            </div>
            <div
                class="px-4 py-2 bg-amber-50 text-amber-600 rounded-xl border border-amber-100"
            >
                <p class="text-[10px] font-bold uppercase tracking-widest">
                    {personalization.hints_available || 3} Tersisa
                </p>
            </div>
        </div>

        <!-- Fast Track Status -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div
                    class="w-10 h-10 {personalization.fast_track_active
                        ? 'bg-purple-100 text-purple-600'
                        : 'bg-slate-100 text-slate-400'} rounded-xl flex items-center justify-center"
                >
                    <Zap size={20} />
                </div>
                <div>
                    <p
                        class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"
                    >
                        Status Fast Track
                    </p>
                    <p
                        class="text-xl font-bold {personalization.fast_track_active
                            ? 'text-purple-600'
                            : 'text-slate-400'} tracking-widest uppercase"
                    >
                        {personalization.fast_track_active
                            ? "Aktif"
                            : "Tidak Aktif"}
                    </p>
                </div>
            </div>
        </div>
    </div>
</Card>
