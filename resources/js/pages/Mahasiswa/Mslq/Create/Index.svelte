<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Alert from '@/components/ui/Alert.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import { ClipboardList, Send, Target } from 'lucide-svelte';
    import { untrack } from 'svelte';
    import { MslqSurveyState } from '@/states/Mahasiswa/MslqSurveyState.svelte';
    import Select from '@/components/ui/Select.svelte';

    const { questions = [], type = 'pre' } = $props();

    const state = untrack(() => new MslqSurveyState(questions, type));

    const motivationQuestions = $derived(
        state.questions.filter((q) => q.category === 'motivation')
    );
    const strategyQuestions = $derived(
        state.questions.filter((q) => q.category === 'learning_strategy')
    );

    const scaleLabels = [
        'Sangat Tidak Setuju',
        '',
        '',
        'Netral',
        '',
        '',
        'Sangat Setuju',
    ];
</script>

<App title="MSLQ Survey">
    <div class="space-y-12 pb-20">
        <PageHeader
            id="page-header"
            title="Motivated Strategies for Learning"
            subtitle="Kuesioner ini membantu kami memahami bagaimana motivasi dan strategi belajar Anda mempengaruhi keberhasilan akademik."
        />

        <Card padding="p-0" class="overflow-hidden rounded-[3rem] border-slate-100 shadow-2xl">
            <div class="space-y-12 p-8 sm:p-12">
                {#if state.form.errors && Object.keys(state.form.errors).length > 0}
                    <Alert variant="danger" dismissible={true}>
                        Mohon lengkapi seluruh pernyataan ({state.form.answers.length} butir) sebelum menyimpan.
                    </Alert>
                {/if}

                <form
                    onsubmit={(e) => {
                        e.preventDefault();
                        state.submit();
                    }}
                    class="space-y-16"
                >
                    <div id="mslq-identitas" class="grid grid-cols-1 gap-10 md:grid-cols-2">
                        <div class="space-y-3">
                            <label
                                for="assessment_type"
                                class="ml-4 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                >Tipe Asesmen</label
                            >
                            <Select
                                id="assessment_type"
                                bind:value={state.form.assessment_type}
                                options={[
                                    { label: 'PRE-TEST (AWAL)', value: 'pre' },
                                    { label: 'POST-TEST (AKHIR)', value: 'post' }
                                ]}
                                class="rounded-[1.5rem] py-4"
                            />
                        </div>
                        <div class="space-y-3">
                            <span
                                class="ml-4 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                >Identitas Akun</span
                            >
                            <div
                                class="w-full rounded-[1.5rem] border-2 border-slate-50 bg-slate-50 px-6 py-4 text-xs font-bold tracking-widest text-slate-400 uppercase"
                            >
                                {state.user?.name || 'STUDENT SESSION'}
                            </div>
                        </div>
                    </div>

                    <div id="mslq-bagian-a" class="space-y-8 border-t border-slate-50 pt-12">
                        <div class="mb-8 flex items-center gap-4">
                            <div
                                class="bg-primary-600 shadow-primary-900/20 flex h-10 w-10 items-center justify-center rounded-xl text-white shadow-lg"
                            >
                                <Target size={20} />
                            </div>
                            <h4
                                class="mb-0 text-xl font-bold tracking-widest text-slate-900 uppercase"
                            >
                                Bagian A: Motivasi Belajar
                            </h4>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            {#each motivationQuestions as question (question.id)}
                                {@const answerIndex = state.form.answers.findIndex(
                                    (a) => a.question_id === question.id
                                )}
                                <div
                                    class="group flex flex-col space-y-6 rounded-[2rem] border-2 border-transparent bg-white p-8 transition-all hover:border-slate-100 hover:bg-slate-50"
                                >
                                    <div class="flex items-start gap-6">
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-900 text-xs font-bold text-white"
                                        >
                                            {question.order}
                                        </div>
                                        <p
                                            class="text-base leading-relaxed font-bold text-slate-700"
                                        >
                                            {question.text}
                                        </p>
                                    </div>

                                    <div class="flex flex-wrap items-center justify-between gap-4 px-2 md:px-16">
                                        {#each Array(7) as _, val}
                                            <label
                                                class="group/item relative flex cursor-pointer flex-col items-center gap-3"
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
                                                    class="peer-checked:bg-primary-600 peer-checked:border-primary-600 peer-checked:shadow-primary-900/20 group-hover/item:border-primary-300 flex h-14 w-14 items-center justify-center rounded-2xl border-2 border-slate-100 bg-white text-sm font-bold text-slate-400 transition-all peer-checked:text-white peer-checked:shadow-xl"
                                                >
                                                    {val + 1}
                                                </div>
                                                <div class="h-4 flex items-center justify-center">
                                                    {#if scaleLabels[val]}
                                                        <span
                                                            class="text-[8px] font-bold tracking-tighter text-slate-400 uppercase opacity-0 transition-opacity group-hover/item:opacity-100 peer-checked:opacity-100 text-center leading-tight"
                                                        >
                                                            {scaleLabels[val]}
                                                        </span>
                                                    {/if}
                                                </div>
                                            </label>
                                        {/each}
                                    </div>
                                </div>
                            {/each}
                        </div>
                    </div>

                    <div id="mslq-bagian-b" class="space-y-8 border-t border-slate-50 pt-12">
                        <div class="mb-8 flex items-center gap-4">
                            <div
                                class="bg-primary-600 shadow-primary-900/20 flex h-10 w-10 items-center justify-center rounded-xl text-white shadow-lg"
                            >
                                <ClipboardList size={20} />
                            </div>
                            <h4
                                class="mb-0 text-xl font-bold tracking-widest text-slate-900 uppercase"
                            >
                                Bagian B: Strategi Belajar
                            </h4>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            {#each strategyQuestions as question (question.id)}
                                {@const answerIndex = state.form.answers.findIndex(
                                    (a) => a.question_id === question.id
                                )}
                                <div
                                    class="group flex flex-col space-y-6 rounded-[2rem] border-2 border-transparent bg-white p-8 transition-all hover:border-slate-100 hover:bg-slate-50"
                                >
                                    <div class="flex items-start gap-6">
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-900 text-xs font-bold text-white"
                                        >
                                            {question.order}
                                        </div>
                                        <p
                                            class="text-base leading-relaxed font-bold text-slate-700"
                                        >
                                            {question.text}
                                        </p>
                                    </div>

                                    <div class="flex flex-wrap items-center justify-between gap-4 px-2 md:px-16">
                                        {#each Array(7) as _, val}
                                            <label
                                                class="group/item relative flex cursor-pointer flex-col items-center gap-3"
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
                                                    class="peer-checked:bg-primary-600 peer-checked:border-primary-600 peer-checked:shadow-primary-900/20 group-hover/item:border-primary-300 flex h-14 w-14 items-center justify-center rounded-2xl border-2 border-slate-100 bg-white text-sm font-bold text-slate-400 transition-all peer-checked:text-white peer-checked:shadow-xl"
                                                >
                                                    {val + 1}
                                                </div>
                                                <div class="h-4 flex items-center justify-center">
                                                    {#if scaleLabels[val]}
                                                        <span
                                                            class="text-[8px] font-bold tracking-tighter text-slate-400 uppercase opacity-0 transition-opacity group-hover/item:opacity-100 peer-checked:opacity-100 text-center leading-tight"
                                                        >
                                                            {scaleLabels[val]}
                                                        </span>
                                                    {/if}
                                                </div>
                                            </label>
                                        {/each}
                                    </div>
                                </div>
                            {/each}
                        </div>
                    </div>

                    <div class="flex justify-center pt-10">
                        <Button
                            type="submit"
                            variant="primary"
                            class="px-20 py-6 text-sm shadow-2xl"
                            icon={Send}
                            disabled={state.form.processing}
                        >
                            {#if state.form.processing}MENYIMPAN...{:else}SIMPAN HASIL MSLQ{/if}
                        </Button>
                    </div>
                </form>
            </div>
        </Card>
    </div>
</App>

