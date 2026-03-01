<script lang="ts">
    import App from "@/layouts/App.svelte";
    import DifficultyFilterBar from "@/components/shared/DifficultyFilterBar.svelte";
    import Card from "@/components/ui/Card.svelte";
    import Badge from "@/components/ui/Badge.svelte";
    import { Link } from "@inertiajs/svelte";
    import { ROUTES } from "@/utils/route";
    import {
        HelpCircle,
        List,
        Check,
        X,
        Lightbulb,
        Book,
        FileText,
    } from "lucide-svelte";
    import { ReviewState } from "@/states/Mahasiswa/QuizState.svelte";
    import type { Material } from "@/types";

    const {
        material,
        materials = [],
        questions = [],
        difficulty = "all",
    }: { material: Material; materials: Material[]; questions: any[]; difficulty: string } = $props();

    const state = new ReviewState(material, materials, questions, difficulty);

    $effect(() => {
        state.material = material;
        state.materials = materials;
        state.questions = questions;
        state.difficulty = difficulty;
    });
</script>

<App title={`Review Soal - ${state.material.title}`}>
    <div class="container-fluid py-4 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div class="lg:col-span-1">
                <Card class="sticky top-4" padding="p-2">
                    {#snippet header()}
                        <h5
                            class="font-black text-slate-900 uppercase tracking-widest text-xs flex items-center gap-3"
                        >
                            <div
                                class="w-8 h-8 rounded-xl bg-primary-600 text-white flex items-center justify-center shadow-lg shadow-primary-900/20"
                            >
                                <Book size={16} />
                            </div>
                            Daftar Materi
                        </h5>
                    {/snippet}

                    <ul class="space-y-1 p-2">
                        {#each state.materials as m (m.id)}
                            <li>
                                <Link
                                    href={ROUTES.MAHASISWA.MATERIALS.SHOW(m.id)}
                                    class={`group flex items-center gap-3 p-3 rounded-xl transition-all font-bold tracking-tight text-xs uppercase
                                        ${m.id === state.material.id ? "bg-primary-600 text-white shadow-xl shadow-primary-900/20" : "text-slate-500 hover:bg-slate-50 hover:text-primary-600"}`}
                                >
                                    <div
                                        class={`w-8 h-8 rounded-lg flex items-center justify-center transition-colors
                                        ${m.id === state.material.id ? "bg-white/20" : "bg-slate-100 group-hover:bg-primary-100"}`}
                                    >
                                        <FileText size={16} />
                                    </div>
                                    <span class="flex-1 truncate"
                                        >{m.title}</span
                                    >
                                </Link>
                            </li>
                        {/each}
                    </ul>
                </Card>
            </div>

            <div class="lg:col-span-3 space-y-8">
                <DifficultyFilterBar
                    difficulty={state.difficulty}
                    onFilter={(d) => state.filterDifficulty(d)}
                />

                {#each state.questions as question, index (question.id)}
                    <Card padding="p-8">
                        <div class="flex justify-between items-start mb-8">
                            <div class="flex flex-col gap-2">
                                <span
                                    class="inline-flex items-center gap-3 font-bold text-slate-800"
                                >
                                    <div
                                        class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center text-sm shadow-inner"
                                    >
                                        {index + 1}
                                    </div>
                                    <span
                                        class="text-xs uppercase tracking-widest text-slate-400"
                                        >Soal dari {state.questions
                                            .length}</span
                                    >
                                </span>
                                {#if question.user_attempt}
                                    <div class="flex items-center gap-3 ml-12">
                                        <Badge
                                            variant={question.user_attempt
                                                .is_correct
                                                ? "success"
                                                : "danger"}
                                            size="sm"
                                            class="shadow-sm"
                                        >
                                            {question.user_attempt.is_correct
                                                ? "TERJAWAB BENAR"
                                                : "TERJAWAB SALAH"}
                                        </Badge>
                                        <span
                                            class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                                        >
                                            Percobaan #{question.user_attempt
                                                .attempt_number} • Skor: {question
                                                .user_attempt.score}
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
                                class="shadow-sm"
                            >
                                {question.difficulty === "hard"
                                    ? "HARD LEVEL"
                                    : question.difficulty.toUpperCase()}
                            </Badge>
                        </div>

                        <div class="space-y-10">
                            <div class="space-y-4">
                                <h5
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] flex items-center gap-2 mb-0"
                                >
                                    <HelpCircle
                                        size={16}
                                        class="text-primary-600"
                                    /> Deskripsi Pertanyaan
                                </h5>
                                <div
                                    class="p-6 bg-slate-50 rounded-[1.5rem] text-slate-800 leading-relaxed border border-slate-100 font-medium"
                                >
                                    {@html question.question_text}
                                </div>
                            </div>

                            <div class="space-y-6">
                                <h5
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] flex items-center gap-2 mb-0"
                                >
                                    <List size={16} class="text-primary-600" /> Pilihan
                                    Jawaban
                                </h5>
                                <div class="grid grid-cols-1 gap-4">
                                    {#each question.answers as answer}
                                        <div
                                            class={`p-5 rounded-2xl flex items-start gap-4 transition-all duration-300 border-2
                                            ${
                                                answer.is_correct
                                                    ? "bg-emerald-50 border-emerald-100 ring-4 ring-emerald-50/50 shadow-sm"
                                                    : question.user_attempt
                                                            ?.answer_id ===
                                                        answer.id
                                                      ? "bg-rose-50 border-rose-100 ring-4 ring-rose-50/50 shadow-sm text-rose-700"
                                                      : "bg-white border-transparent text-slate-500 hover:bg-slate-50 transition-all font-medium"
                                            }`}
                                        >
                                            {#if answer.is_correct}
                                                <div
                                                    class="w-7 h-7 rounded-lg bg-emerald-500 flex items-center justify-center shrink-0 shadow-lg shadow-emerald-200"
                                                >
                                                    <Check
                                                        size={16}
                                                        class="text-white"
                                                        strokeWidth={3}
                                                    />
                                                </div>
                                            {:else if question.user_attempt?.answer_id === answer.id}
                                                <div
                                                    class="w-7 h-7 rounded-lg bg-rose-500 flex items-center justify-center shrink-0 shadow-lg shadow-rose-200"
                                                >
                                                    <X
                                                        size={16}
                                                        class="text-white"
                                                        strokeWidth={3}
                                                    />
                                                </div>
                                            {:else}
                                                <div
                                                    class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center shrink-0 border border-slate-200"
                                                >
                                                    <div
                                                        class="w-2 h-2 rounded-full bg-slate-300"
                                                    ></div>
                                                </div>
                                            {/if}
                                            <div class="flex-1 font-bold">
                                                {answer.answer_text}
                                                {#if question.user_attempt?.answer_id === answer.id}
                                                    <Badge
                                                        variant="outline"
                                                        size="xs"
                                                        class="ml-2 border-current opacity-70"
                                                        >Pilihan Anda</Badge
                                                    >
                                                {/if}
                                            </div>
                                        </div>
                                        {#if answer.is_correct && answer.explanation}
                                            <div
                                                class="mt-2 p-6 bg-primary-50 border border-primary-100 rounded-[1.5rem] relative overflow-hidden group/exp"
                                            >
                                                <div
                                                    class="absolute top-0 right-0 p-4 opacity-10 group-hover/exp:scale-110 transition-transform duration-700"
                                                >
                                                    <Lightbulb
                                                        size={64}
                                                        class="text-primary-600"
                                                    />
                                                </div>
                                                <div class="relative z-10">
                                                    <div
                                                        class="flex items-center gap-2 text-[10px] font-black text-primary-600 uppercase tracking-widest mb-3"
                                                    >
                                                        <Lightbulb
                                                            size={14}
                                                            class="text-primary-600"
                                                        /> Analisis Jawaban:
                                                    </div>
                                                    <div
                                                        class="text-primary-900 text-sm leading-relaxed font-bold"
                                                    >
                                                        {@html answer.explanation}
                                                    </div>
                                                </div>
                                            </div>
                                        {/if}
                                    {/each}
                                </div>
                            </div>
                        </div>
                    </Card>
                {/each}
            </div>
        </div>
    </div>
</App>
