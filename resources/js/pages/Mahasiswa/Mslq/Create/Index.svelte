<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Alert from '@/components/ui/Alert.svelte';
    import ProgressBar from '@/components/ui/ProgressBar.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import { ClipboardList, Send, Target, Info } from 'lucide-svelte';
    import { untrack } from 'svelte';
    import { MslqSurveyState } from '@/states/Mahasiswa/MslqSurveyState.svelte';
    import Input from '@/components/ui/Input.svelte';

    const { questions = [] } = $props();

    const state = untrack(() => new MslqSurveyState(questions));

    const motivationQuestions = $derived(
        state.questions.filter((q) => q.category === 'motivation')
    );
    const strategyQuestions = $derived(
        state.questions.filter((q) => q.category === 'learning_strategy')
    );
</script>

<style>
    .glass {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }
</style>

<App title="MSLQ Survey">
    <div class="space-y-12 pb-20">
        <PageHeader
            id="page-header"
            title="Motivated Strategies for Learning"
            subtitle="Kuesioner ini membantu kami memahami bagaimana motivasi dan strategi belajar Anda mempengaruhi keberhasilan akademik."
            centered
        />

        <div id="mslq-progress" class="sticky top-20 z-20">
            <div
                class="glass flex items-center justify-between rounded-3xl border-2 border-slate-100 bg-white/80 p-6 shadow-xl backdrop-blur-xl"
            >
                <div class="flex flex-1 items-center gap-6">
                    <div class="hidden sm:block">
                        <div
                            class="text-[10px] font-black tracking-widest text-slate-400 uppercase"
                        >
                            Progress Pengisian
                        </div>
                        <div class="text-primary-500 text-2xl font-black">
                            {Math.round(state.progress)}%
                        </div>
                    </div>
                    <div class="flex-1 px-4">
                        <ProgressBar value={state.progress} color="accent" height="h-3" />
                    </div>
                </div>
                <div class="flex items-center gap-4 border-l-2 border-slate-100 pl-6">
                    <div class="hidden text-right sm:block">
                        <div
                            class="text-[10px] font-black tracking-widest text-slate-400 uppercase"
                        >
                            Item Terisi
                        </div>
                        <div class="text-primary-500 text-lg font-black">
                            {state.form.answers.filter((a) => a.value !== null).length} / 81
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Card
            padding="p-0"
            class="border-duo overflow-hidden rounded-[3rem] border-slate-100 shadow-2xl"
        >
            <div class="space-y-12 p-8 sm:p-12">
                {#if state.form.errors && Object.keys(state.form.errors).length > 0}
                    <Alert variant="danger" dismissible={true}>
                        Mohon lengkapi seluruh pernyataan (81 butir) dan pastikan identitas sudah
                        benar.
                    </Alert>
                {/if}

                <form
                    onsubmit={(e) => {
                        e.preventDefault();
                        state.submit();
                    }}
                    class="space-y-20"
                >
                    <div id="mslq-identitas" class="grid grid-cols-1 gap-10 md:grid-cols-3">
                        <div class="space-y-3">
                            <label
                                for="nim"
                                class="ml-4 block text-[10px] font-black tracking-widest text-slate-400 uppercase"
                            >
                                NIM Mahasiswa <span class="text-accent-500">*</span>
                            </label>
                            <Input
                                id="nim"
                                bind:value={state.form.nim}
                                placeholder="Contoh: 2141720000"
                                required
                                error={state.form.errors['nim']}
                            />
                        </div>
                        <div class="space-y-3">
                            <label
                                for="class"
                                class="ml-4 block text-[10px] font-black tracking-widest text-slate-400 uppercase"
                            >
                                Segmentasi Kelas <span class="text-accent-500">*</span>
                            </label>
                            <Input
                                id="class"
                                bind:value={state.form.class}
                                placeholder="Contoh: TI-3A"
                                required
                                error={state.form.errors['class']}
                            />
                        </div>
                        <div class="space-y-3">
                            <span
                                class="ml-4 block text-[10px] font-black tracking-widest text-slate-400 uppercase"
                                >Skala Penilaian</span
                            >
                            <div
                                class="border-accent-50 bg-accent-50/50 text-accent-600 w-full rounded-[1.5rem] border-4 px-6 py-4 text-[11px] leading-relaxed font-black uppercase"
                            >
                                1 (STS) &harr; 7 (SS)
                            </div>
                        </div>
                    </div>

                    <div id="mslq-bagian-a" class="space-y-10">
                        <div class="flex items-center gap-4">
                            <div
                                class="bg-accent-500 shadow-accent-200 border-accent-700 flex h-10 w-10 items-center justify-center rounded-xl border-b-4 text-white shadow-lg"
                            >
                                <Target size={20} />
                            </div>
                            <h4
                                class="text-primary-500 font-display mb-0 text-xl font-black tracking-widest uppercase"
                            >
                                Bagian A: Motivasi Belajar
                            </h4>
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            {#each motivationQuestions as question}
                                {@const answerIndex = state.form.answers.findIndex(
                                    (a) => a.question_id === question.id
                                )}
                                <div
                                    class="group relative flex flex-col gap-6 rounded-3xl border-2 border-transparent bg-white p-8 transition-all hover:border-slate-100 hover:bg-slate-50/50 hover:shadow-xl"
                                >
                                    <div class="flex gap-6">
                                        <div
                                            class="group-hover:bg-accent-100 group-hover:text-accent-600 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-xs font-black text-slate-400 transition-all group-hover:scale-110"
                                        >
                                            {question.order}
                                        </div>
                                        <p
                                            class="group-hover:text-primary-500 pt-1.5 text-sm leading-relaxed font-bold text-slate-600 transition-colors"
                                        >
                                            {question.text}
                                        </p>
                                    </div>

                                    <div class="flex flex-wrap justify-between gap-2 px-4 sm:px-12">
                                        {#each Array(7) as _, val}
                                            <label
                                                class="group/item press-active relative cursor-pointer"
                                            >
                                                <input
                                                    type="radio"
                                                    name={`q-${question.id}`}
                                                    value={val + 1}
                                                    bind:group={
                                                        state.form.answers[answerIndex]!.value
                                                    }
                                                    class="peer hidden"
                                                    required
                                                />
                                                <div
                                                    class="peer-checked:bg-accent-500 peer-checked:border-accent-700 peer-checked:shadow-accent-900/20 group-hover/item:border-accent-300 flex h-10 w-10 items-center justify-center rounded-xl border-2 border-b-6 border-slate-100 bg-white text-[10px] font-black text-slate-400 transition-all peer-checked:translate-y-[2px] peer-checked:text-white sm:h-12 sm:w-12 sm:text-xs"
                                                >
                                                    {val + 1}
                                                </div>
                                            </label>
                                        {/each}
                                    </div>
                                </div>
                            {/each}
                        </div>
                    </div>

                    <div id="mslq-bagian-b" class="space-y-10">
                        <div class="flex items-center gap-4">
                            <div
                                class="bg-primary-500 flex h-10 w-10 items-center justify-center rounded-xl border-b-4 border-black text-white shadow-lg shadow-slate-200"
                            >
                                <ClipboardList size={20} />
                            </div>
                            <h4
                                class="text-primary-500 font-display mb-0 text-xl font-black tracking-widest uppercase"
                            >
                                Bagian B: Strategi Belajar
                            </h4>
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            {#each strategyQuestions as question}
                                {@const answerIndex = state.form.answers.findIndex(
                                    (a) => a.question_id === question.id
                                )}
                                <div
                                    class="group relative flex flex-col gap-6 rounded-3xl border-2 border-transparent bg-white p-8 transition-all hover:border-slate-100 hover:bg-slate-50/50 hover:shadow-xl"
                                >
                                    <div class="flex gap-6">
                                        <div
                                            class="group-hover:bg-primary-100 group-hover:text-primary-500 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-xs font-black text-slate-400 transition-all group-hover:scale-110"
                                        >
                                            {question.order}
                                        </div>
                                        <p
                                            class="group-hover:text-primary-500 pt-1.5 text-sm leading-relaxed font-bold text-slate-600 transition-colors"
                                        >
                                            {question.text}
                                        </p>
                                    </div>

                                    <div class="flex flex-wrap justify-between gap-2 px-4 sm:px-12">
                                        {#each Array(7) as _, val}
                                            <label
                                                class="group/item press-active relative cursor-pointer"
                                            >
                                                <input
                                                    type="radio"
                                                    name={`q-${question.id}`}
                                                    value={val + 1}
                                                    bind:group={
                                                        state.form.answers[answerIndex]!.value
                                                    }
                                                    class="peer hidden"
                                                    required
                                                />
                                                <div
                                                    class="peer-checked:bg-primary-500 peer-checked:shadow-primary-900/20 group-hover/item:border-primary-300 flex h-10 w-10 items-center justify-center rounded-xl border-2 border-b-6 border-slate-100 bg-white text-[10px] font-black text-slate-400 transition-all peer-checked:translate-y-[2px] peer-checked:border-black peer-checked:text-white sm:h-12 sm:w-12 sm:text-xs"
                                                >
                                                    {val + 1}
                                                </div>
                                            </label>
                                        {/each}
                                    </div>
                                </div>
                            {/each}
                        </div>
                    </div>

                    <div class="flex flex-col items-center gap-6 border-t border-slate-50 pt-16">
                        <div
                            class="flex items-center gap-2 text-[10px] font-bold tracking-[0.2em] text-slate-400 uppercase"
                        >
                            <Info size={14} />
                            Pastikan anda telah mengisi seluruh butir pertanyaan
                        </div>
                        <Button
                            type="submit"
                            variant="primary"
                            size="xl"
                            class="group border-duo-lg h-20 px-16 transition-all hover:scale-105 active:scale-95"
                            icon={Send}
                            disabled={state.form.processing || state.progress < 100}
                        >
                            {#if state.form.processing}MEMPROSES...{:else}SIMPAN HASIL SURVEY MSLQ{/if}
                        </Button>
                    </div>
                </form>
            </div>
        </Card>
    </div>
</App>
