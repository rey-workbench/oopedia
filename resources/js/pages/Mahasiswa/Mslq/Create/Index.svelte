<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Alert from '@/components/ui/Alert.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import { ClipboardList, Send, Target } from '@lucide/svelte';
    import { untrack } from 'svelte';
    import { page } from '@inertiajs/svelte';
    import { MslqSurveyState } from '@/states/Mahasiswa/MslqSurveyState.svelte';
    import Input from '@/components/ui/Input.svelte';
    import Select from '@/components/ui/Select.svelte';
    import type { SharedProps } from '@/types';

    const { questions = [] } = $props();

    const state = untrack(() => {
        const user = (page.props as unknown as SharedProps).auth?.user;
        // Force type to 'post' and lock it as requested by the user
        return new MslqSurveyState(questions, 'post', user);
    });

    const motivationQuestions = $derived(
        state.questions.filter((q) => q.category === 'motivation')
    );
    const strategyQuestions = $derived(
        state.questions.filter((q) => q.category === 'learning_strategy')
    );

    const scaleLabels = [
        'Sangat Tidak Setuju',
        'Tidak Setuju',
        'Agak Tidak Setuju',
        'Netral',
        'Agak Setuju',
        'Setuju',
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
                        Mohon lengkapi seluruh pernyataan ({state.form.answers.length} butir) sebelum
                        menyimpan.
                    </Alert>
                {/if}

                <form
                    onsubmit={(e) => {
                        e.preventDefault();
                        state.submit();
                    }}
                    class="space-y-16"
                >
                    <div
                        id="mslq-identitas"
                        class="grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-4"
                    >
                        <div class="space-y-2.5">
                            <span
                                class="ml-4 block text-xs font-black tracking-widest text-slate-500 uppercase"
                            >
                                Identitas Mahasiswa
                            </span>
                            <div
                                class="flex rounded-3xl border-2 border-b-6 border-slate-200 bg-slate-50/50 px-6 py-4 text-sm font-bold tracking-widest text-slate-400 uppercase"
                            >
                                {state.user?.name || 'STUDENT SESSION'}
                            </div>
                        </div>

                        <Input
                            id="nim"
                            label="NIM *"
                            placeholder="Masukkan NIM Anda"
                            bind:value={state.form.nim}
                            class="rounded-3xl!"
                            required
                        />

                        <Input
                            id="class"
                            label="Kelas *"
                            placeholder="Masukkan Kelas Anda"
                            bind:value={state.form.class}
                            class="rounded-3xl!"
                            required
                        />

                        <Select
                            id="assessment_type"
                            label="Tipe Asesmen *"
                            bind:value={state.form.assessment_type}
                            disabled={true}
                            required
                            options={[
                                { label: 'PRE-TEST (AWAL)', value: 'pre' },
                                { label: 'POST-TEST (AKHIR)', value: 'post' },
                            ]}
                        />
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
                                    class={`group flex flex-col space-y-6 rounded-[2rem] border-2 p-8 transition-all
                                    ${state.form.errors[`answers.${answerIndex}.value`] ? 'border-rose-100 bg-rose-50/50 ring-4 ring-rose-50' : 'border-transparent bg-white hover:border-slate-100 hover:bg-slate-50'}`}
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
                                            <span class="ml-1 text-rose-500">*</span>
                                        </p>
                                    </div>

                                    <div
                                        class="flex flex-wrap items-center justify-between gap-4 px-2 md:px-16"
                                    >
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
                                                    class="peer sr-only"
                                                    required
                                                />
                                                <div
                                                    class="peer-checked:bg-primary-600 peer-checked:border-primary-600 peer-checked:shadow-primary-900/20 group-hover/item:border-primary-300 flex h-14 w-14 items-center justify-center rounded-2xl border-2 border-slate-100 bg-white text-sm font-bold text-slate-400 transition-all peer-checked:text-white peer-checked:shadow-xl"
                                                >
                                                    {val + 1}
                                                </div>
                                                <div class="flex h-10 items-center justify-center">
                                                    {#if scaleLabels[val]}
                                                        <span
                                                            class="max-w-[60px] text-center text-[8px] leading-tight font-bold tracking-tighter text-slate-400 uppercase opacity-0 transition-opacity group-hover/item:opacity-100 peer-checked:opacity-100"
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
                                    class={`group flex flex-col space-y-6 rounded-[2rem] border-2 p-8 transition-all
                                    ${state.form.errors[`answers.${answerIndex}.value`] ? 'border-rose-100 bg-rose-50/50 ring-4 ring-rose-50' : 'border-transparent bg-white hover:border-slate-100 hover:bg-slate-50'}`}
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
                                            <span class="ml-1 text-rose-500">*</span>
                                        </p>
                                    </div>

                                    <div
                                        class="flex flex-wrap items-center justify-between gap-4 px-2 md:px-16"
                                    >
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
                                                    class="peer sr-only"
                                                    required
                                                />
                                                <div
                                                    class="peer-checked:bg-primary-600 peer-checked:border-primary-600 peer-checked:shadow-primary-900/20 group-hover/item:border-primary-300 flex h-14 w-14 items-center justify-center rounded-2xl border-2 border-slate-100 bg-white text-sm font-bold text-slate-400 transition-all peer-checked:text-white peer-checked:shadow-xl"
                                                >
                                                    {val + 1}
                                                </div>
                                                <div class="flex h-10 items-center justify-center">
                                                    {#if scaleLabels[val]}
                                                        <span
                                                            class="max-w-[60px] text-center text-[8px] leading-tight font-bold tracking-tighter text-slate-400 uppercase opacity-0 transition-opacity group-hover/item:opacity-100 peer-checked:opacity-100"
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
                            class="shadow-primary-900/20 px-20 py-6 text-sm shadow-2xl"
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
