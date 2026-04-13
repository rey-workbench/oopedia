<script lang="ts">
    import App from '@/layouts/App.svelte';
    import DifficultyFilterBar from '@/components/layout/DifficultyFilterBar.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Badge from '@/components/ui/Badge.svelte';
    import { Link } from '@inertiajs/svelte';
    import { ROUTES } from '@/utils/route';
    import { HelpCircle, List, Check, X, Lightbulb, Book, FileText } from 'lucide-svelte';
    import { untrack } from 'svelte';
    import { ReviewState } from '@/states/Mahasiswa/QuizState.svelte';
    import type { Material, QuestionWithAttempt, DifficultyLevel } from '@/types';

    const {
        material,
        materials = [],
        questions = [],
        difficulty = 'all',
    }: {
        material: Material;
        materials: Material[];
        questions: QuestionWithAttempt[];
        difficulty: DifficultyLevel | 'all';
    } = $props();

    const state = untrack(() => new ReviewState(material, materials, questions, difficulty));

    $effect(() => {
        state.material = material;
        state.materials = materials;
        state.questions = questions;
        state.difficulty = difficulty;
    });
</script>

<App title={`Review Soal - ${state.material.title}`}>
    <div class="container-fluid mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
            <div class="lg:col-span-1">
                <Card class="sticky top-24" padding="p-2">
                    {#snippet header()}
                        <h5
                            class="flex items-center gap-3 text-xs font-black tracking-widest text-slate-900 uppercase"
                        >
                            <div
                                class="bg-primary-600 shadow-primary-900/20 flex h-8 w-8 items-center justify-center rounded-xl text-white shadow-lg"
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
                                    class={`group flex items-center gap-3 rounded-xl p-3 text-xs font-bold tracking-tight uppercase transition-all
                                        ${m.id === state.material.id ? 'bg-primary-600 shadow-primary-900/20 text-white shadow-xl' : 'hover:text-primary-600 text-slate-500 hover:bg-slate-50'}`}
                                >
                                    <div
                                        class={`flex h-8 w-8 items-center justify-center rounded-lg transition-colors
                                        ${m.id === state.material.id ? 'bg-white/20' : 'group-hover:bg-primary-100 bg-slate-100'}`}
                                    >
                                        <FileText size={16} />
                                    </div>
                                    <span class="flex-1 truncate">{m.title}</span>
                                </Link>
                            </li>
                        {/each}
                    </ul>
                </Card>
            </div>

            <div id="review-results" class="space-y-8 lg:col-span-3">
                <DifficultyFilterBar
                    difficulty={state.difficulty}
                    onfilter={(d) => state.filterDifficulty(d)}
                />

                {#each state.questions as question, index (question.id)}
                    <Card padding="p-8">
                        <div class="mb-8 flex items-start justify-between">
                            <div class="flex flex-col gap-2">
                                <span
                                    class="inline-flex items-center gap-3 font-bold text-slate-800"
                                >
                                    <div
                                        class="bg-primary-50 text-primary-600 flex h-10 w-10 items-center justify-center rounded-xl text-sm shadow-inner"
                                    >
                                        {index + 1}
                                    </div>
                                    <span class="text-xs tracking-widest text-slate-400 uppercase"
                                        >Soal dari {state.questions.length}</span
                                    >
                                </span>
                                {#if question.user_attempt}
                                    <div class="ml-12 flex items-center gap-3">
                                        <Badge
                                            variant={question.user_attempt.is_correct
                                                ? 'success'
                                                : 'danger'}
                                            size="sm"
                                            class="shadow-sm"
                                        >
                                            {question.user_attempt.is_correct
                                                ? 'TERJAWAB BENAR'
                                                : 'TERJAWAB SALAH'}
                                        </Badge>
                                        <span
                                            class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                        >
                                            Percobaan #{question.user_attempt.attempt_number} • Skor:
                                            {question.user_attempt.score}
                                        </span>
                                    </div>
                                {/if}
                            </div>
                            <Badge
                                variant={question.difficulty === 'beginner'
                                    ? 'success'
                                    : question.difficulty === 'medium'
                                      ? 'warning'
                                      : 'danger'}
                                class="shadow-sm"
                            >
                                {question.difficulty === 'hard'
                                    ? 'HARD LEVEL'
                                    : question.difficulty.toUpperCase()}
                            </Badge>
                        </div>

                        <div class="space-y-10">
                            <div class="space-y-4">
                                <h5
                                    class="mb-0 flex items-center gap-2 text-[10px] font-black tracking-[0.2em] text-slate-400 uppercase"
                                >
                                    <HelpCircle size={16} class="text-primary-600" /> Deskripsi Pertanyaan
                                </h5>
                                <div
                                    class="rounded-[1.5rem] border border-slate-100 bg-slate-50 p-6 leading-relaxed font-medium text-slate-800"
                                >
                                    {@html question.question_text}
                                </div>
                            </div>

                            <div class="space-y-6">
                                <h5
                                    class="mb-0 flex items-center gap-2 text-[10px] font-black tracking-[0.2em] text-slate-400 uppercase"
                                >
                                    <List size={16} class="text-primary-600" /> Pilihan Jawaban
                                </h5>
                                <div class="grid grid-cols-1 gap-4">
                                    {#each question.answers as answer}
                                        <div
                                            class={`flex items-start gap-4 rounded-2xl border-2 p-5 transition-all duration-300
                                            ${
                                                answer.is_correct
                                                    ? 'border-emerald-100 bg-emerald-50 shadow-sm ring-4 ring-emerald-50/50'
                                                    : question.user_attempt?.answer_id === answer.id
                                                      ? 'border-rose-100 bg-rose-50 text-rose-700 shadow-sm ring-4 ring-rose-50/50'
                                                      : 'border-transparent bg-white font-medium text-slate-500 transition-all hover:bg-slate-50'
                                            }`}
                                        >
                                            {#if answer.is_correct}
                                                <div
                                                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-emerald-500 shadow-lg shadow-emerald-200"
                                                >
                                                    <Check
                                                        size={16}
                                                        class="text-white"
                                                        strokeWidth={3}
                                                    />
                                                </div>
                                            {:else if question.user_attempt?.answer_id === answer.id}
                                                <div
                                                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-rose-500 shadow-lg shadow-rose-200"
                                                >
                                                    <X
                                                        size={16}
                                                        class="text-white"
                                                        strokeWidth={3}
                                                    />
                                                </div>
                                            {:else}
                                                <div
                                                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-slate-100"
                                                >
                                                    <div
                                                        class="h-2 w-2 rounded-full bg-slate-300"
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
                                                class="bg-primary-50 border-primary-100 group/exp relative mt-2 overflow-hidden rounded-[1.5rem] border p-6"
                                            >
                                                <div
                                                    class="absolute top-0 right-0 p-4 opacity-10 transition-transform duration-700 group-hover/exp:scale-110"
                                                >
                                                    <Lightbulb size={64} class="text-primary-600" />
                                                </div>
                                                <div class="relative z-10">
                                                    <div
                                                        class="text-primary-600 mb-3 flex items-center gap-2 text-[10px] font-black tracking-widest uppercase"
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
