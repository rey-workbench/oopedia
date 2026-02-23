<script>
    import App from "@/layouts/App.svelte";
    import Card from "@/components/ui/Card.svelte";
    import Button from "@/components/ui/Button.svelte";
    import Alert from "@/components/ui/Alert.svelte";
    import { useForm, page } from "@inertiajs/svelte";
    import {
        ClipboardList,
        CheckSquare,
        MessageSquare,
        Send,
    } from "lucide-svelte";
    import { UeqSurveyState } from "@/states/Mahasiswa/UeqSurveyState.svelte";
    import Input from "@/components/ui/Input.svelte";

    export let aspects = [];

    const state = new UeqSurveyState(aspects);

    const form = useForm({
        nim: "",
        class: "",
        comments: "",
        suggestions: "",
        ...Object.fromEntries(aspects.map((a) => [a.name, null])),
    });

    $: authUser = $page.props.auth.user;
    $: missingFields = $page.props.flash.missingFields || [];

    function handleSubmit() {
        $form.post("/mahasiswa/ueq-survey", {
            scrollToError: true,
            onSuccess: () => {
                // Inertia will redirect
            },
        });
    }
</script>

<App title="UEQ Survey">
    <div class="space-y-12 pb-20">
        <div class="text-center space-y-6">
            <div
                class="inline-flex items-center justify-center w-20 h-20 bg-primary-50 text-primary-600 rounded-[2rem] shadow-inner"
            >
                <ClipboardList size={32} />
            </div>
            <h1
                class="text-4xl font-bold text-slate-900 tracking-[0.2em] uppercase"
            >
                User Experience <span class="text-primary-600"
                    >Questionnaire</span
                >
            </h1>
            <p class="text-slate-500 text-lg max-w-2xl mx-auto font-medium">
                Kami mengundang Anda untuk memberikan evaluasi objektif terhadap
                pengalaman interaksi Anda dengan platform OOPEDIA.
            </p>
        </div>

        <Card
            padding="p-0"
            class="overflow-hidden border-slate-100 shadow-2xl rounded-[3rem]"
        >
            <div class="p-12 space-y-12">
                {#if $form.errors && Object.keys($form.errors).length > 0}
                    <Alert variant="danger" dismissible={true}>
                        Ada {Object.keys($form.errors).length} aspek yang belum Anda
                        evaluasi atau tidak valid. Silakan tinjau kembali input Anda.
                    </Alert>
                {/if}

                <form
                    on:submit|preventDefault={handleSubmit}
                    class="space-y-16"
                >
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                        <div class="space-y-3">
                            <label
                                for="nim"
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-4"
                            >
                                NIM Mahasiswa <span class="text-rose-500"
                                    >*</span
                                >
                            </label>
                            <Input
                                id="nim"
                                bind:value={$form.nim}
                                placeholder="Contoh: 2141720000"
                                required
                                error={$form.errors.nim}
                                class="rounded-[1.5rem] py-4"
                            />
                        </div>
                        <div class="space-y-3">
                            <span
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-4"
                                >Identitas Akun</span
                            >
                            <div
                                class="w-full px-6 py-4 border-2 border-slate-50 rounded-[1.5rem] bg-slate-50 font-bold text-slate-400 uppercase tracking-widest text-xs"
                            >
                                {authUser ? authUser.name : "GUEST SESSION"}
                            </div>
                        </div>
                        <div class="space-y-3">
                            <label
                                for="class"
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-4"
                            >
                                Segmentasi Kelas <span class="text-rose-500"
                                    >*</span
                                >
                            </label>
                            <Input
                                id="class"
                                bind:value={$form.class}
                                placeholder="Contoh: TI-3A"
                                required
                                error={$form.errors.class}
                                class="rounded-[1.5rem] py-4"
                            />
                        </div>
                    </div>

                    <div class="space-y-8 pt-12 border-t border-slate-50">
                        <div class="flex items-center gap-4 mb-8">
                            <div
                                class="w-10 h-10 bg-primary-600 text-white rounded-xl flex items-center justify-center shadow-lg shadow-primary-900/20"
                            >
                                <CheckSquare size={20} />
                            </div>
                            <h4
                                class="text-xl font-bold text-slate-900 tracking-widest uppercase mb-0"
                            >
                                Matriks Evaluasi UEQ
                            </h4>
                        </div>

                        <div class="overflow-x-auto -mx-12 px-12">
                            <div class="min-w-[900px]">
                                <div
                                    class="flex items-center text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em] mb-8 px-6"
                                >
                                    <div class="w-1/4">Pole Negatif</div>
                                    <div
                                        class="w-2/4 flex justify-between px-16"
                                    >
                                        {#each Array(7) as _, i}
                                            <div class="w-10 text-center">
                                                {i + 1}
                                            </div>
                                        {/each}
                                    </div>
                                    <div class="w-1/4 text-right">
                                        Pole Positif
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    {#each state.questionnaireAspects as aspect}
                                        <div
                                            class={`flex items-center p-6 rounded-[2rem] transition-all group border-2
                                            ${$form.errors[aspect.name] ? "bg-rose-50/50 border-rose-100 ring-4 ring-rose-50" : "bg-white border-transparent hover:bg-slate-50 hover:border-slate-100"}`}
                                        >
                                            <div
                                                class="w-1/4 text-xs font-bold text-slate-500 group-hover:text-slate-900 transition-colors"
                                            >
                                                {aspect.left}
                                            </div>
                                            <div
                                                class="w-2/4 flex justify-between px-14"
                                            >
                                                {#each Array(7) as _, i}
                                                    <label
                                                        class="cursor-pointer group/item relative"
                                                    >
                                                        <input
                                                            type="radio"
                                                            name={aspect.name}
                                                            value={i + 1}
                                                            bind:group={
                                                                $form[
                                                                    aspect.name
                                                                ]
                                                            }
                                                            class="peer hidden"
                                                            required
                                                        />
                                                        <div
                                                            class="w-10 h-10 rounded-xl border-2 border-slate-100 bg-white peer-checked:bg-primary-600 peer-checked:border-primary-600 peer-checked:shadow-xl peer-checked:shadow-primary-900/20 transition-all flex items-center justify-center text-[10px] font-bold text-transparent peer-checked:text-white group-hover/item:border-primary-300"
                                                        >
                                                            {i + 1}
                                                        </div>
                                                    </label>
                                                {/each}
                                            </div>
                                            <div
                                                class="w-1/4 text-xs font-bold text-slate-500 text-right group-hover:text-slate-900 transition-colors uppercase"
                                            >
                                                {aspect.right}
                                            </div>
                                        </div>
                                    {/each}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-16 border-t border-slate-50 space-y-10">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 bg-emerald-600 text-white rounded-xl flex items-center justify-center shadow-lg shadow-emerald-200"
                            >
                                <MessageSquare size={20} />
                            </div>
                            <h4
                                class="text-xl font-bold text-slate-900 tracking-widest uppercase mb-0"
                            >
                                Feedback Kualitatif
                            </h4>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <div class="space-y-3">
                                <label
                                    for="comments"
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-4"
                                >
                                    Komentar Subjektif <span
                                        class="text-rose-500">*</span
                                    >
                                </label>
                                <textarea
                                    id="comments"
                                    bind:value={$form.comments}
                                    class="w-full px-8 py-6 border-2 border-slate-50 rounded-[2rem] bg-slate-50 font-bold focus:ring-8 focus:ring-primary-50 focus:border-primary-500 outline-none transition-all placeholder:text-slate-300 text-xs min-h-[160px] uppercase tracking-wider"
                                    placeholder="Bagaimana perasaan Anda saat belajar menggunakan OOPEDIA?"
                                    required
                                ></textarea>
                                {#if $form.errors.comments}
                                    <p
                                        class="text-[10px] font-bold text-rose-500 ml-4 uppercase tracking-widest"
                                    >
                                        {$form.errors.comments}
                                    </p>
                                {/if}
                            </div>

                            <div class="space-y-3">
                                <label
                                    for="suggestions"
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-4"
                                >
                                    Saran Optimasi <span class="text-rose-500"
                                        >*</span
                                    >
                                </label>
                                <textarea
                                    id="suggestions"
                                    bind:value={$form.suggestions}
                                    class="w-full px-8 py-6 border-2 border-slate-50 rounded-[2rem] bg-slate-50 font-bold focus:ring-8 focus:ring-primary-50 focus:border-primary-500 outline-none transition-all placeholder:text-slate-300 text-xs min-h-[160px] uppercase tracking-wider"
                                    placeholder="Apa satu hal yang paling ingin Anda tingkatkan dari sistem ini?"
                                    required
                                ></textarea>
                                {#if $form.errors.suggestions}
                                    <p
                                        class="text-[10px] font-bold text-rose-500 ml-4 uppercase tracking-widest"
                                    >
                                        {$form.errors.suggestions}
                                    </p>
                                {/if}
                            </div>
                        </div>
                    </div>

                    <div class="pt-10 flex justify-center">
                        <Button
                            type="submit"
                            variant="primary"
                            class="px-20 py-6 text-sm shadow-2xl shadow-primary-900/20"
                            icon={Send}
                            disabled={$form.processing}
                        >
                            {#if $form.processing}MENGIRIMKAN...{:else}KIRIM
                                DATA SURVEI{/if}
                        </Button>
                    </div>
                </form>
            </div>
        </Card>
    </div>
</App>
