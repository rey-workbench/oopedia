<script>
    import App from "../../../layouts/App.svelte";
    import PageHeader from "../../../components/ui/PageHeader.svelte";
    import Card from "../../../components/ui/Card.svelte";
    import Button from "../../../components/ui/Button.svelte";
    import Input from "../../../components/ui/Input.svelte";
    import QuillEditor from "../../../components/ui/QuillEditor.svelte";
    import { useForm, router } from "@inertiajs/svelte";
    import { onMount } from "svelte";

    export let materials = [];
    export let material = null;
    export let subMaterials = [];

    const form = useForm({
        question_text: "",
        question_type: "radio_button",
        difficulty: "beginner",
        material_id: material ? material.id : "",
        sub_material_id: "",
        answers: [
            { answer_text: "", is_correct: 0, explanation: "" },
            { answer_text: "", is_correct: 0, explanation: "" },
        ],
        correct_answer: null,
    });

    let availableSubMaterials = subMaterials;

    async function handleMaterialChange() {
        if (!$form.material_id) {
            availableSubMaterials = [];
            return;
        }
        const response = await fetch(
            `/admin/materials/${$form.material_id}/submaterials/json`,
        );
        availableSubMaterials = await response.json();
        $form.sub_material_id = "";
    }

    function addAnswer() {
        $form.answers = [
            ...$form.answers,
            { answer_text: "", is_correct: 0, explanation: "" },
        ];
    }

    function removeAnswer(index) {
        $form.answers = $form.answers.filter((_, i) => i !== index);
    }

    function handleSubmit() {
        // Process is_correct based on correct_answer index for radio_button/fill_in_the_blank
        if (
            ["radio_button", "fill_in_the_blank"].includes($form.question_type)
        ) {
            $form.answers.forEach((ans, i) => {
                ans.is_correct = i == $form.correct_answer ? 1 : 0;
            });
        }
        $form.post("/admin/questions");
    }
</script>

<App title="Buat Instrumen Baru">
    <div class="space-y-12 pb-20">
        <PageHeader
            title="Engineering Evaluasi"
            subtitle="Membangun instrumen penilaian baru dengan parameter algoritma yang presisi."
        >
            <div slot="actions">
                <Button
                    href={material
                        ? `/admin/materials/${material.id}/questions`
                        : "/admin/questions"}
                    variant="ghost"
                    icon="fas fa-arrow-left">BATALKAN</Button
                >
            </div>
        </PageHeader>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="lg:col-span-2 space-y-10">
                <Card
                    padding="p-0"
                    class="overflow-hidden border-slate-100 shadow-2xl"
                >
                    <div
                        slot="header"
                        class="bg-blue-600 px-8 py-6 text-white flex items-center gap-3"
                    >
                        <i class="fas fa-edit"></i>
                        <h6
                            class="text-sm font-bold tracking-widest uppercase mb-0 text-white"
                        >
                            Konten & Logika Pertanyaan
                        </h6>
                    </div>

                    <div class="p-8 space-y-8">
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                                >Teks Pertanyaan (Rich Text)</label
                            >
                            <QuillEditor
                                bind:content={$form.question_text}
                                placeholder="Deskripsikan problematik pemrograman di sini..."
                            />
                            {#if $form.errors.question_text}
                                <p
                                    class="text-[10px] font-bold text-rose-500 uppercase tracking-widest"
                                >
                                    {$form.errors.question_text}
                                </p>
                            {/if}
                        </div>

                        <div class="space-y-6">
                            <div class="flex items-center justify-between">
                                <label
                                    class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                                    >Konfigurasi Opsi Jawaban</label
                                >
                                <Button
                                    type="button"
                                    on:click={addAnswer}
                                    variant="ghost"
                                    size="xs"
                                    icon="fas fa-plus">TAMBAH OPSI</Button
                                >
                            </div>

                            <div class="space-y-4">
                                {#each $form.answers as answer, i}
                                    <div
                                        class="p-6 bg-slate-50 rounded-3xl border border-slate-100 space-y-4 relative group"
                                    >
                                        <div class="flex items-start gap-4">
                                            <div class="pt-2">
                                                <input
                                                    type="radio"
                                                    name="correct_answer"
                                                    value={i}
                                                    bind:group={
                                                        $form.correct_answer
                                                    }
                                                    class="w-5 h-5 text-blue-600 focus:ring-blue-500 border-slate-300 transition-all cursor-pointer"
                                                />
                                            </div>
                                            <div class="flex-1 space-y-2">
                                                <input
                                                    type="text"
                                                    bind:value={
                                                        answer.answer_text
                                                    }
                                                    placeholder={`Opsi Jawaban #${i + 1}`}
                                                    class="w-full bg-white border-2 border-slate-100 rounded-2xl px-6 py-3 text-xs font-bold uppercase tracking-widest text-slate-900 focus:border-blue-600 focus:outline-none transition-all"
                                                />
                                                <input
                                                    type="text"
                                                    bind:value={
                                                        answer.explanation
                                                    }
                                                    placeholder="Penjelasan/Feedback (Opsional)"
                                                    class="w-full bg-white/50 border border-slate-100 rounded-xl px-4 py-2 text-[10px] font-bold text-slate-500 focus:border-blue-400 focus:outline-none transition-all"
                                                />
                                            </div>
                                            {#if $form.answers.length > 1}
                                                <button
                                                    type="button"
                                                    on:click={() =>
                                                        removeAnswer(i)}
                                                    class="p-2 text-slate-300 hover:text-rose-500 transition-colors"
                                                >
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            {/if}
                                        </div>
                                    </div>
                                {/each}
                            </div>
                        </div>
                    </div>
                </Card>
            </div>

            <div class="space-y-10">
                <Card
                    padding="p-8"
                    class="border-slate-100 shadow-2xl space-y-8"
                >
                    <h6
                        class="text-xs font-bold tracking-widest uppercase text-slate-900 border-b border-slate-50 pb-4 mb-0"
                    >
                        Atribut Metadata
                    </h6>

                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label
                                for="material"
                                class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                                >Modul Utama</label
                            >
                            <select
                                id="material"
                                bind:value={$form.material_id}
                                on:change={handleMaterialChange}
                                class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-900 focus:border-blue-600 focus:outline-none transition-all cursor-pointer"
                            >
                                <option value="">PILIH MODUL</option>
                                {#each materials as m}
                                    <option value={m.id}>{m.title}</option>
                                {/each}
                            </select>
                            {#if $form.errors.material_id}
                                <p
                                    class="text-[10px] font-bold text-rose-500 uppercase tracking-widest"
                                >
                                    {$form.errors.material_id}
                                </p>
                            {/if}
                        </div>

                        <div class="space-y-2">
                            <label
                                for="sub_material"
                                class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                                >Unit Spesifik (Opsional)</label
                            >
                            <select
                                id="sub_material"
                                bind:value={$form.sub_material_id}
                                class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-900 focus:border-blue-600 focus:outline-none transition-all cursor-pointer disabled:opacity-50"
                                disabled={!$form.material_id}
                            >
                                <option value="">TAG UNIT TERKAIT</option>
                                {#each availableSubMaterials as sm}
                                    <option value={sm.id}>{sm.title}</option>
                                {/each}
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                                >Tipe Algoritma</label
                            >
                            <div class="grid grid-cols-1 gap-2">
                                {#each ["radio_button", "fill_in_the_blank", "drag_and_drop"] as type}
                                    <button
                                        type="button"
                                        on:click={() =>
                                            ($form.question_type = type)}
                                        class={`py-4 px-6 rounded-2xl border-2 font-bold uppercase tracking-widest text-[10px] text-left transition-all flex items-center justify-between
                        ${$form.question_type === type ? "border-blue-600 bg-blue-50 text-blue-600" : "border-slate-50 bg-slate-50 text-slate-400"}`}
                                    >
                                        {type.replace(/_/g, " ")}
                                        {#if $form.question_type === type}<i
                                                class="fas fa-check-circle"
                                            ></i>{/if}
                                    </button>
                                {/each}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                                >Level Kesulitan</label
                            >
                            <div class="flex gap-2">
                                {#each ["beginner", "medium", "hard"] as diff}
                                    <button
                                        type="button"
                                        on:click={() =>
                                            ($form.difficulty = diff)}
                                        class={`flex-1 py-3 px-2 rounded-xl border-2 font-bold uppercase tracking-widest text-[9px] transition-all
                        ${$form.difficulty === diff ? "border-indigo-600 bg-indigo-50 text-indigo-600" : "border-slate-50 bg-slate-50 text-slate-400"}`}
                                    >
                                        {diff}
                                    </button>
                                {/each}
                            </div>
                        </div>
                    </div>

                    <div class="pt-6">
                        <Button
                            on:click={handleSubmit}
                            variant="primary"
                            class="w-full py-4 shadow-xl shadow-blue-500/20"
                            icon="fas fa-save"
                            disabled={$form.processing}
                        >
                            {#if $form.processing}SINGKRONISASI...{:else}SIMPAN
                                INSTRUMEN{/if}
                        </Button>
                    </div>
                </Card>
            </div>
        </div>
    </div>
</App>
