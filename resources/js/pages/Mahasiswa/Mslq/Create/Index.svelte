<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Alert from '@/components/ui/Alert.svelte';
    import ProgressBar from '@/components/ui/ProgressBar.svelte';
    import { ClipboardList, Send, Brain, Target, Info } from 'lucide-svelte';
    import { untrack } from 'svelte';
    import { MslqState } from '@/states/Mahasiswa/MslqState.svelte';
    import Input from '@/components/ui/Input.svelte';

    const { questions = [] } = $props();

    const state = untrack(() => new MslqState(questions));

    // Group questions by category
    const motivationQuestions = $derived(state.questions.filter(q => q.category === 'motivation'));
    const strategyQuestions = $derived(state.questions.filter(q => q.category === 'learning_strategy'));
</script>

<App title="MSLQ Survey">
    <div class="space-y-12 pb-20">
        <!-- Header -->
        <div id="mslq-instructions" class="space-y-6 text-center">
            <div class="bg-indigo-50 text-indigo-600 inline-flex h-20 w-20 items-center justify-center rounded-[2rem] shadow-inner">
                <Brain size={32} />
            </div>
            <h1 class="text-4xl font-bold tracking-[0.2em] text-slate-900 uppercase">
                Motivated Strategies <span class="text-indigo-600">for Learning</span>
            </h1>
            <p class="mx-auto max-w-2xl text-lg font-medium text-slate-500">
                Kuesioner ini membantu kami memahami bagaimana motivasi dan strategi belajar Anda mempengaruhi keberhasilan akademik.
            </p>
        </div>

        <!-- Progress Sticky -->
        <div class="sticky top-20 z-20">
            <div class="glass flex items-center justify-between rounded-3xl border border-slate-100 bg-white/80 p-6 shadow-xl backdrop-blur-xl">
                <div class="flex flex-1 items-center gap-6">
                    <div class="hidden sm:block">
                        <div class="text-[10px] font-bold tracking-widest text-slate-400 uppercase">Progress Pengisian</div>
                        <div class="text-xl font-black text-slate-900">{Math.round(state.progress)}%</div>
                    </div>
                    <div class="flex-1 px-4">
                        <ProgressBar value={state.progress} color="blue" height="h-3" />
                    </div>
                </div>
                <div class="flex items-center gap-4 pl-6 border-l border-slate-100">
                    <div class="text-right hidden sm:block">
                        <div class="text-[10px] font-bold tracking-widest text-slate-400 uppercase">Item Terisi</div>
                        <div class="text-lg font-bold text-slate-900">{state.form.answers.filter(a => a.value !== null).length} / 81</div>
                    </div>
                </div>
            </div>
        </div>

        <Card padding="p-0" class="overflow-hidden rounded-[3rem] border-slate-100 shadow-2xl">
            <div class="space-y-12 p-12">
                {#if state.form.errors && Object.keys(state.form.errors).length > 0}
                    <Alert variant="danger" dismissible={true}>
                        Mohon lengkapi seluruh pernyataan (81 butir) dan pastikan identitas sudah benar.
                    </Alert>
                {/if}

                <form
                    onsubmit={(e) => {
                        e.preventDefault();
                        state.submit();
                    }}
                    class="space-y-20"
                >
                    <!-- Identity Section -->
                    <div id="mslq-identitas" class="grid grid-cols-1 gap-10 md:grid-cols-3">
                        <div class="space-y-3">
                            <label for="nim" class="ml-4 block text-[10px] font-bold tracking-widest text-slate-400 uppercase">
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
                            <label for="class" class="ml-4 block text-[10px] font-bold tracking-widest text-slate-400 uppercase">
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
                        <div class="space-y-3">
                            <span class="ml-4 block text-[10px] font-bold tracking-widest text-slate-400 uppercase">Skala Penilaian</span>
                            <div class="w-full rounded-[1.5rem] border-2 border-indigo-50 bg-indigo-50/50 px-6 py-4 text-[10px] font-bold leading-relaxed text-indigo-600 uppercase">
                                1 (Sangat Tidak Setuju) &harr; 7 (Sangat Setuju)
                            </div>
                        </div>
                    </div>

                    <!-- Motivation Section -->
                    <div class="space-y-10">
                        <div class="flex items-center gap-4">
                            <div class="bg-indigo-600 flex h-10 w-10 items-center justify-center rounded-xl text-white shadow-lg shadow-indigo-200">
                                <Target size={20} />
                            </div>
                            <h4 class="mb-0 text-xl font-bold tracking-widest text-slate-900 uppercase">Bagian A: Motivasi Belajar</h4>
                        </div>
                        
                        <div class="space-y-2">
                            {#each motivationQuestions as question}
                                {@const answerIndex = state.form.answers.findIndex(a => a.question_id === question.id)}
                                <div class="group relative flex flex-col gap-6 rounded-3xl border-2 border-transparent bg-white p-8 transition-all hover:border-slate-100 hover:bg-slate-50/50 hover:shadow-xl">
                                    <div class="flex gap-6">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-xs font-black text-slate-400 transition-colors group-hover:bg-indigo-100 group-hover:text-indigo-600">
                                            {question.order}
                                        </div>
                                        <p class="pt-1.5 text-sm font-bold leading-relaxed text-slate-600 transition-colors group-hover:text-slate-900">
                                            {question.text}
                                        </p>
                                    </div>
                                    
                                    <div class="flex flex-wrap justify-between gap-2 px-4 sm:px-12">
                                        {#each Array(7) as _, val}
                                            <label class="group/item relative cursor-pointer">
                                                <input
                                                    type="radio"
                                                    name={`q-${question.id}`}
                                                    value={val + 1}
                                                    bind:group={state.form.answers[answerIndex]!.value}
                                                    class="peer hidden"
                                                    required
                                                />
                                                <div class="peer-checked:bg-indigo-600 peer-checked:border-indigo-600 peer-checked:shadow-indigo-900/20 group-hover/item:border-indigo-300 flex h-10 w-10 items-center justify-center rounded-xl border-2 border-slate-100 bg-white text-[10px] font-bold text-slate-400 transition-all peer-checked:text-white peer-checked:shadow-xl sm:h-12 sm:w-12 sm:text-xs">
                                                    {val + 1}
                                                </div>
                                            </label>
                                        {/each}
                                    </div>
                                </div>
                            {/each}
                        </div>
                    </div>

                    <!-- Strategy Section -->
                    <div class="space-y-10">
                        <div class="flex items-center gap-4">
                            <div class="bg-emerald-600 flex h-10 w-10 items-center justify-center rounded-xl text-white shadow-lg shadow-emerald-200">
                                <ClipboardList size={20} />
                            </div>
                            <h4 class="mb-0 text-xl font-bold tracking-widest text-slate-900 uppercase">Bagian B: Strategi Belajar</h4>
                        </div>
                        
                        <div class="space-y-2">
                            {#each strategyQuestions as question}
                                {@const answerIndex = state.form.answers.findIndex(a => a.question_id === question.id)}
                                <div class="group relative flex flex-col gap-6 rounded-3xl border-2 border-transparent bg-white p-8 transition-all hover:border-slate-100 hover:bg-slate-50/50 hover:shadow-xl">
                                    <div class="flex gap-6">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-xs font-black text-slate-400 transition-colors group-hover:bg-emerald-100 group-hover:text-emerald-600">
                                            {question.order}
                                        </div>
                                        <p class="pt-1.5 text-sm font-bold leading-relaxed text-slate-600 transition-colors group-hover:text-slate-900">
                                            {question.text}
                                        </p>
                                    </div>
                                    
                                    <div class="flex flex-wrap justify-between gap-2 px-4 sm:px-12">
                                        {#each Array(7) as _, val}
                                            <label class="group/item relative cursor-pointer">
                                                <input
                                                    type="radio"
                                                    name={`q-${question.id}`}
                                                    value={val + 1}
                                                    bind:group={state.form.answers[answerIndex]!.value}
                                                    class="peer hidden"
                                                    required
                                                />
                                                <div class="peer-checked:bg-emerald-600 peer-checked:border-emerald-600 peer-checked:shadow-emerald-900/20 group-hover/item:border-emerald-300 flex h-10 w-10 items-center justify-center rounded-xl border-2 border-slate-100 bg-white text-[10px] font-bold text-slate-400 transition-all peer-checked:text-white peer-checked:shadow-xl sm:h-12 sm:w-12 sm:text-xs">
                                                    {val + 1}
                                                </div>
                                            </label>
                                        {/each}
                                    </div>
                                </div>
                            {/each}
                        </div>
                    </div>

                    <!-- Footer / Submit -->
                    <div class="flex flex-col items-center gap-6 border-t border-slate-50 pt-16">
                        <div class="flex items-center gap-2 text-[10px] font-bold tracking-[0.2em] text-slate-400 uppercase">
                            <Info size={14} />
                            Pastikan anda telah mengisi seluruh butir pertanyaan
                        </div>
                        <Button
                            type="submit"
                            variant="primary"
                            class="shadow-indigo-900/20 group h-16 px-16 text-sm shadow-2xl transition-all hover:scale-105 active:scale-95"
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

<style>
    .glass {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }
</style>
