<script>
    import Badge from "@/components/ui/Badge.svelte";
    import { HelpCircle, List, Check, X, Lightbulb } from "lucide-svelte";

    export let questions = [];
</script>

<div class="space-y-6">
    {#each questions as question, index (question.id)}
        <div
            class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow"
        >
            <div class="flex justify-between items-start mb-6">
                <div class="flex flex-col gap-1">
                    <span
                        class="inline-flex items-center gap-2 font-bold text-slate-700"
                    >
                        <div
                            class="w-8 h-8 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center text-sm"
                        >
                            {index + 1}
                        </div>
                        Soal dari {questions.length}
                    </span>
                    {#if question.user_attempt}
                        <div class="flex items-center gap-2 ml-10 mt-1">
                            <Badge
                                variant={question.user_attempt.is_correct
                                    ? "success"
                                    : "danger"}
                                size="sm"
                            >
                                {question.user_attempt.is_correct
                                    ? "Benar"
                                    : "Salah"}
                            </Badge>
                            <span
                                class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                            >
                                Percobaan #{question.user_attempt
                                    .attempt_number} • Skor:
                                {question.user_attempt.score}
                            </span>
                        </div>
                    {/if}
                </div>
                <Badge
                    variant={question.difficulty === "beginner"
                        ? "success"
                        : question.difficulty === "medium"
                          ? "warning"
                          : "danger"}
                >
                    {question.difficulty === "hard"
                        ? "Hard"
                        : question.difficulty.charAt(0).toUpperCase() +
                          question.difficulty.slice(1)}
                </Badge>
            </div>

            <div class="space-y-6">
                <!-- Question Text -->
                <div>
                    <h5
                        class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2"
                    >
                        <HelpCircle size={16} class="text-primary-600" />
                        Pertanyaan
                    </h5>
                    <div
                        class="p-5 bg-slate-50 rounded-xl text-slate-800 leading-relaxed border border-slate-100"
                    >
                        {@html question.question_text}
                    </div>
                </div>

                <!-- Answers -->
                <div>
                    <h5
                        class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2"
                    >
                        <List size={16} class="text-primary-600" />
                        Pilihan Jawaban
                    </h5>
                    <div class="grid grid-cols-1 gap-3">
                        {#each question.answers as answer}
                            <div
                                class={`p-4 rounded-xl flex items-start gap-4 transition-all ${
                                    answer.is_correct
                                        ? "bg-emerald-50 border-2 border-emerald-200 shadow-sm"
                                        : question.user_attempt?.answer_id ===
                                            answer.id
                                          ? "bg-rose-50 border-2 border-rose-200 shadow-sm text-rose-700"
                                          : "bg-white border-2 border-slate-50 text-slate-500"
                                }`}
                            >
                                {#if answer.is_correct}
                                    <div
                                        class="w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center shrink-0 mt-0.5"
                                    >
                                        <Check size={14} class="text-white" />
                                    </div>
                                {:else if question.user_attempt?.answer_id === answer.id}
                                    <div
                                        class="w-6 h-6 rounded-full bg-rose-500 flex items-center justify-center shrink-0 mt-0.5"
                                    >
                                        <X size={14} class="text-white" />
                                    </div>
                                {:else}
                                    <div
                                        class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center shrink-0 mt-0.5"
                                    >
                                        <X size={14} class="text-slate-300" />
                                    </div>
                                {/if}
                                <div class="flex-1 font-medium">
                                    {answer.answer_text}
                                    {#if question.user_attempt?.answer_id === answer.id}
                                        <span
                                            class="ml-2 text-[10px] font-bold uppercase tracking-tight opacity-70"
                                        >
                                            (Pilihan Anda)
                                        </span>
                                    {/if}
                                </div>
                            </div>
                            {#if answer.is_correct && answer.explanation}
                                <div
                                    class="mt-2 p-5 bg-primary-50 border-l-4 border-primary-600 rounded-r-xl"
                                >
                                    <div
                                        class="flex items-center gap-2 font-bold text-primary-900 mb-1"
                                    >
                                        <Lightbulb
                                            size={16}
                                            class="text-primary-600"
                                        />
                                        Penjelasan:
                                    </div>
                                    <div
                                        class="text-primary-800 text-sm leading-relaxed"
                                    >
                                        {@html answer.explanation}
                                    </div>
                                </div>
                            {/if}
                        {/each}
                    </div>
                </div>
            </div>
        </div>
    {/each}
</div>
