<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Alert from '@/components/ui/Alert.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import { CheckSquare, MessageSquare, Send } from 'lucide-svelte';
    import { untrack } from 'svelte';
    import { page } from '@inertiajs/svelte';
    import { UeqSurveyState } from '@/states/Mahasiswa/UeqSurveyState.svelte';
    import Input from '@/components/ui/Input.svelte';
    import type { SharedProps } from '@/types';

    const { aspects = [] }: { aspects: { name: string }[] } = $props();

    const state = untrack(() => {
        const user = (page.props as unknown as SharedProps).auth?.user;
        // UEQ is always post-usage evaluation, so hardcode to 'post'
        return new UeqSurveyState(aspects, 'post', user);
    });
</script>

<App title="UEQ Survey">
    <div class="space-y-12 pb-20">
        <PageHeader
            id="page-header"
            title="User Experience Questionnaire"
            subtitle="Kami mengundang Anda untuk memberikan evaluasi objektif terhadap pengalaman interaksi Anda dengan platform OOPEDIA."
        />

        <Card padding="p-0" class="overflow-hidden rounded-[3rem] border-slate-100 shadow-2xl">
            <div class="space-y-12 p-8 sm:p-12">
                {#if state.form.errors && Object.keys(state.form.errors).length > 0}
                    <Alert variant="danger" dismissible={true}>
                        Ada {Object.keys(state.form.errors).length} aspek yang belum Anda evaluasi atau
                        tidak valid. Silakan tinjau kembali input Anda.
                    </Alert>
                {/if}

                <form
                    id="ueq-survey-form"
                    onsubmit={(e) => {
                        e.preventDefault();
                        state.submit();
                    }}
                    class="space-y-16"
                >
                    <div
                        id="ueq-identitas"
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
                    </div>

                    <div class="space-y-8 border-t border-slate-50 pt-12">
                        <div class="mb-8 flex items-center gap-4">
                            <div
                                class="bg-primary-600 shadow-primary-900/20 flex h-10 w-10 items-center justify-center rounded-xl text-white shadow-lg"
                            >
                                <CheckSquare size={20} />
                            </div>
                            <h4
                                class="mb-0 text-xl font-bold tracking-widest text-slate-900 uppercase"
                            >
                                Matriks Evaluasi UEQ
                            </h4>
                        </div>

                        <div id="ueq-matriks" class="-mx-12 overflow-x-auto px-12">
                            <div class="min-w-[900px]">
                                <div
                                    class="mb-8 flex items-center px-6 text-[10px] font-bold tracking-[0.3em] text-slate-400 uppercase"
                                >
                                    <div class="w-1/4">Pole Negatif</div>
                                    <div class="flex w-2/4 justify-between px-16">
                                        {#each Array(7) as _, i}
                                            <div class="w-10 text-center">
                                                {i + 1}
                                            </div>
                                        {/each}
                                    </div>
                                    <div class="w-1/4 text-right">Pole Positif</div>
                                </div>

                                <div class="space-y-4">
                                    {#each state.questionnaireAspects as aspect}
                                        {@const answerIndex = state.form.answers.findIndex(
                                            (a) => a.question_id === aspect.name
                                        )}
                                        <div
                                            class={`group flex items-center rounded-[2rem] border-2 p-6 transition-all
                                            ${state.form.errors[`answers.${answerIndex}.value`] ? 'border-rose-100 bg-rose-50/50 ring-4 ring-rose-50' : 'border-transparent bg-white hover:border-slate-100 hover:bg-slate-50'}`}
                                        >
                                            <div
                                                class="w-1/4 text-xs font-bold text-slate-500 transition-colors group-hover:text-slate-900"
                                            >
                                                {aspect.left}
                                            </div>
                                            <div class="flex w-2/4 justify-between px-14">
                                                {#each Array(7) as _, i}
                                                    <label
                                                        class="group/item relative cursor-pointer"
                                                    >
                                                        {#if answerIndex !== -1}
                                                            <input
                                                                type="radio"
                                                                name={aspect.name}
                                                                value={i + 1}
                                                                bind:group={
                                                                    state.form.answers[answerIndex]!
                                                                        .value
                                                                }
                                                                class="peer sr-only"
                                                                required
                                                            />
                                                        {/if}
                                                        <div
                                                            class="peer-checked:bg-primary-600 peer-checked:border-primary-600 peer-checked:shadow-primary-900/20 group-hover/item:border-primary-300 flex h-10 w-10 items-center justify-center rounded-xl border-2 border-slate-100 bg-white text-[10px] font-bold text-transparent transition-all peer-checked:text-white peer-checked:shadow-xl"
                                                        >
                                                            {i + 1}
                                                        </div>
                                                    </label>
                                                {/each}
                                            </div>
                                            <div
                                                class="w-1/4 text-right text-xs font-bold text-slate-500 uppercase transition-colors group-hover:text-slate-900"
                                            >
                                                {aspect.right}
                                            </div>
                                        </div>
                                    {/each}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="ueq-feedback" class="space-y-10 border-t border-slate-50 pt-16">
                        <div class="flex items-center gap-4">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-lg shadow-emerald-200"
                            >
                                <MessageSquare size={20} />
                            </div>
                            <h4
                                class="mb-0 text-xl font-bold tracking-widest text-slate-900 uppercase"
                            >
                                Feedback Kualitatif
                            </h4>
                        </div>

                        <div class="grid grid-cols-1 gap-10 md:grid-cols-2">
                            <div class="space-y-3">
                                <label
                                    for="comments"
                                    class="ml-4 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                >
                                    Komentar Subjektif <span class="text-rose-500">*</span>
                                </label>
                                <textarea
                                    id="comments"
                                    bind:value={state.form['comments']}
                                    class="focus:ring-primary-50 focus:border-primary-500 min-h-[160px] w-full rounded-[2rem] border-2 border-slate-50 bg-slate-50 px-8 py-6 text-xs font-bold tracking-wider uppercase transition-all outline-none placeholder:text-slate-300 focus:ring-8"
                                    placeholder="Bagaimana perasaan Anda saat belajar menggunakan OOPEDIA?"
                                    required
                                ></textarea>
                                {#if state.form.errors['comments']}
                                    <p
                                        class="ml-4 text-[10px] font-bold tracking-widest text-rose-500 uppercase"
                                    >
                                        {state.form.errors['comments']}
                                    </p>
                                {/if}
                            </div>

                            <div class="space-y-3">
                                <label
                                    for="suggestions"
                                    class="ml-4 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                >
                                    Saran Optimasi <span class="text-rose-500">*</span>
                                </label>
                                <textarea
                                    id="suggestions"
                                    bind:value={state.form['suggestions']}
                                    class="focus:ring-primary-50 focus:border-primary-500 min-h-[160px] w-full rounded-[2rem] border-2 border-slate-50 bg-slate-50 px-8 py-6 text-xs font-bold tracking-wider uppercase transition-all outline-none placeholder:text-slate-300 focus:ring-8"
                                    placeholder="Apa satu hal yang paling ingin Anda tingkatkan dari sistem ini?"
                                    required
                                ></textarea>
                                {#if state.form.errors['suggestions']}
                                    <p
                                        class="ml-4 text-[10px] font-bold tracking-widest text-rose-500 uppercase"
                                    >
                                        {state.form.errors['suggestions']}
                                    </p>
                                {/if}
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center pt-10">
                        <Button
                            id="btn-submit-survey"
                            type="submit"
                            variant="primary"
                            class="shadow-primary-900/20 px-20 py-6 text-sm shadow-2xl"
                            icon={Send}
                            disabled={state.form.processing}
                        >
                            {#if state.form.processing}MENGIRIMKAN...{:else}KIRIM DATA SURVEI{/if}
                        </Button>
                    </div>
                </form>
            </div>
        </Card>
    </div>
</App>
