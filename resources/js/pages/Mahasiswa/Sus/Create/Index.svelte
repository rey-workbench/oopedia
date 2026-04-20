<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Alert from '@/components/ui/Alert.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import { CheckSquare, MessageSquare, Send } from 'lucide-svelte';
    import { untrack } from 'svelte';
    import { SusSurveyState } from '@/states/Mahasiswa/SusSurveyState.svelte';
    import Input from '@/components/ui/Input.svelte';

    const state = untrack(() => new SusSurveyState());

    const scaleLabels = [
        'Sangat Tidak Setuju',
        'Tidak Setuju',
        'Ragu-ragu',
        'Setuju',
        'Sangat Setuju',
    ];
</script>

<App title="SUS Survey">
    <div class="space-y-12 pb-20">
        <PageHeader
            id="page-header"
            title="System Usability Scale"
            subtitle="Bantu kami meningkatkan kualitas platform dengan memberikan penilaian sejujur mungkin terhadap kebergunaan sistem OOPEDIA."
        />

        <Card padding="p-0" class="overflow-hidden rounded-[3rem] border-slate-100 shadow-2xl">
            <div class="space-y-12 p-12">
                {#if state.form.errors && Object.keys(state.form.errors).length > 0}
                    <Alert variant="danger" dismissible={true}>
                        Ada beberapa field yang belum valid atau belum diisi. Silakan tinjau kembali
                        input Anda.
                    </Alert>
                {/if}

                <form
                    id="sus-survey-form"
                    onsubmit={(e) => {
                        e.preventDefault();
                        state.submit();
                    }}
                    class="space-y-16"
                >
                    <div id="sus-identitas" class="grid grid-cols-1 gap-10 md:grid-cols-3">
                        <div class="space-y-3">
                            <label
                                for="nim"
                                class="ml-4 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                            >
                                NIM Mahasiswa <span class="text-rose-500">*</span>
                            </label>
                            <Input
                                id="nim"
                                bind:value={state.form.nim}
                                placeholder="Contoh: 2141720000"
                                required
                                error={state.form.errors['nim']}
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
                                {state.user ? state.user.name : 'GUEST SESSION'}
                            </div>
                        </div>
                        <div class="space-y-3">
                            <label
                                for="class"
                                class="ml-4 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                            >
                                Segmentasi Kelas <span class="text-rose-500">*</span>
                            </label>
                            <Input
                                id="class"
                                bind:value={state.form.class}
                                placeholder="Contoh: TI-3A"
                                required
                                error={state.form.errors['class']}
                                class="rounded-[1.5rem] py-4"
                            />
                        </div>
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
                                Instrumen Penilaian SUS
                            </h4>
                        </div>

                        <div id="sus-questions" class="space-y-6">
                            {#each state.questions as question (question.id)}
                                <div
                                    class={`group flex flex-col space-y-6 rounded-[2rem] border-2 p-8 transition-all
                                    ${state.form.errors[`q${question.id}`] ? 'border-rose-100 bg-rose-50/50 ring-4 ring-rose-50' : 'border-transparent bg-white hover:border-slate-100 hover:bg-slate-50'}`}
                                >
                                    <div class="flex items-start gap-6">
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-900 text-xs font-bold text-white"
                                        >
                                            {question.id}
                                        </div>
                                        <p
                                            class="text-base leading-relaxed font-bold text-slate-700"
                                        >
                                            {question.text}
                                        </p>
                                    </div>

                                    <div
                                        class="flex flex-wrap items-center justify-between gap-4 px-2 md:px-16"
                                    >
                                        {#each Array(5) as _, i}
                                            <label
                                                class="group/item relative flex cursor-pointer flex-col items-center gap-3"
                                            >
                                                <input
                                                    type="radio"
                                                    name={`q${question.id}`}
                                                    value={i + 1}
                                                    bind:group={
                                                        (state.form as any)[`q${question.id}`]
                                                    }
                                                    class="peer hidden"
                                                    required
                                                />
                                                <div
                                                    class="peer-checked:bg-primary-600 peer-checked:border-primary-600 peer-checked:shadow-primary-900/20 group-hover/item:border-primary-300 flex h-14 w-14 items-center justify-center rounded-2xl border-2 border-slate-100 bg-white text-sm font-bold text-slate-400 transition-all peer-checked:text-white peer-checked:shadow-xl"
                                                >
                                                    {i + 1}
                                                </div>
                                                <span
                                                    class="text-[8px] font-bold tracking-tighter text-slate-400 uppercase opacity-0 transition-opacity group-hover/item:opacity-100 peer-checked:opacity-100"
                                                >
                                                    {scaleLabels[i]}
                                                </span>
                                            </label>
                                        {/each}
                                    </div>

                                    {#if state.form.errors[`q${question.id}`]}
                                        <p
                                            class="ml-16 text-[10px] font-bold tracking-widest text-rose-500 uppercase"
                                        >
                                            {state.form.errors[`q${question.id}`]}
                                        </p>
                                    {/if}
                                </div>
                            {/each}
                        </div>
                    </div>

                    <div id="sus-feedback" class="space-y-10 border-t border-slate-50 pt-16">
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
                                    Komentar Subjektif
                                </label>
                                <textarea
                                    id="comments"
                                    bind:value={state.form.comments}
                                    class="focus:ring-primary-50 focus:border-primary-500 min-h-[160px] w-full rounded-[2rem] border-2 border-slate-50 bg-slate-50 px-8 py-6 text-xs font-bold tracking-wider uppercase transition-all outline-none placeholder:text-slate-300 focus:ring-8"
                                    placeholder="Apa yang Anda rasakan selama menggunakan media pembelajaran ini?"
                                ></textarea>
                            </div>

                            <div class="space-y-3">
                                <label
                                    for="suggestions"
                                    class="ml-4 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                >
                                    Saran Optimasi
                                </label>
                                <textarea
                                    id="suggestions"
                                    bind:value={state.form.suggestions}
                                    class="focus:ring-primary-50 focus:border-primary-500 min-h-[160px] w-full rounded-[2rem] border-2 border-slate-50 bg-slate-50 px-8 py-6 text-xs font-bold tracking-wider uppercase transition-all outline-none placeholder:text-slate-300 focus:ring-8"
                                    placeholder="Ada saran fitur atau tampilan yang perlu diperbaiki?"
                                ></textarea>
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
