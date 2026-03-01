<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/shared/PageHeader.svelte";
        import Button from "@/components/ui/Button.svelte";
    import QuillEditor from "@/components/ui/QuillEditor.svelte";
    import { ArrowLeft, Edit2, RefreshCw, Plus, X } from "lucide-svelte";
    import { QuestionEditState } from "@/states/Admin/QuestionState.svelte";
    import Input from "@/components/ui/Input.svelte";

    export let materials = [];
    export let material = null;
    export let subMaterials = [];
    export let question;

    // Initialize State
    const state = new QuestionEditState(question);

    // Initialize submaterials
    state.setSubMaterials(subMaterials);

    // Proxy for easy access in template
    const form = state.form;
</script>

<App title={`Edit Soal #${question.id}`}>
    <div class="space-y-12 pb-20">
        <PageHeader
            title="Rekonfigurasi Soal"
            subtitle={`Memodifikasi instrumen penilaian #${question.id} untuk optimasi validitas.`}
        >
            <div slot="actions">
                <Button
                    href={material
                        ? `/admin/materials/${material.id}/questions`
                        : "/admin/questions"}
                    variant="ghost"
                    icon={ArrowLeft}>BATALKAN</Button
                >
            </div>
        </PageHeader>

        
<form onsubmit={(e) => { e.preventDefault(); () => state.submit(question.id)(e); }} class="space-y-12">
    <div class="bg-white rounded-3xl p-6 shadow-2xl border border-slate-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
        <div class="mb-6">
            <h3 class="text-lg font-bold text-slate-800">
                Update Konten & Logika
            </h3>
        </div>

        <div class="space-y-10 p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                <div class="lg:col-span-2 space-y-8">
                    <div class="space-y-2">
                        <span
                            class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                            >Teks Pertanyaan (Rich Text)</span
                        >
                        <QuillEditor
                            bind:value={$form.question_text}
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
                            <h3
                                class="text-sm font-bold text-slate-900 uppercase tracking-widest"
                            >
                                Opsi Jawaban Terdaftar
                            </h3>
                            <Button
                                type="button"
                                onclick={() => state.addAnswer()}
                                variant="ghost"
                                size="sm"
                                icon={Plus}
                            >
                                Tambah Opsi
                            </Button>
                        </div>

                        {#each state.answers as answer, i}
                            <div
                                class="flex items-start gap-4 p-4 bg-slate-50 rounded-2xl border-2 border-slate-100 group"
                            >
                                <div class="flex-1 space-y-3">
                                    <textarea
                                        bind:value={answer.answer_text}
                                        placeholder="Teks jawaban..."
                                        class="w-full px-4 py-3 border-2 border-slate-100 rounded-xl bg-white text-sm font-bold focus:ring-4 focus:ring-primary-50 focus:border-primary-500 outline-none transition-all resize-none"
                                        rows="2"
                                    ></textarea>
                                    <div class="flex items-center gap-3">
                                        <label
                                            class="flex items-center gap-2 cursor-pointer"
                                        >
                                            <input
                                                type="checkbox"
                                                bind:checked={answer.is_correct}
                                                class="w-4 h-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500 transition-colors"
                                            />
                                            <span
                                                class="text-[10px] font-bold uppercase tracking-widest text-slate-500"
                                                >Jawaban Benar</span
                                            >
                                        </label>
                                    </div>
                                    <textarea
                                        bind:value={answer.explanation}
                                        placeholder="Penjelasan jawaban (opsional)..."
                                        class="w-full px-4 py-3 border-2 border-slate-100 rounded-xl bg-white text-sm focus:ring-4 focus:ring-primary-50 focus:border-primary-500 outline-none transition-all resize-none"
                                        rows="2"
                                    ></textarea>
                                </div>
                                <Button
                                    type="button"
                                    onclick={() => state.removeAnswer(i)}
                                    variant="ghost"
                                    size="sm"
                                    icon={X}
                                    class="text-slate-300 hover:text-rose-500 hover:bg-rose-50 p-2"
                                />
                            </div>
                        {/each}
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="space-y-8">
                        <h3
                            class="text-sm font-bold text-slate-900 uppercase tracking-widest"
                        >
                            Parametrik
                        </h3>

                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-bold uppercase text-slate-400"
                                >Modul Materi</label
                            >
                            <select
                                bind:value={$form.material_id}
                                onchange={(e) =>
                                    state.handleMaterialChange(e.target.value)}
                                class="w-full px-4 py-3 border-2 border-slate-100 rounded-2xl bg-white text-sm font-bold focus:ring-4 focus:ring-primary-50 focus:border-primary-500 outline-none transition-all appearance-none"
                            >
                                <option value="">Pilih Materi</option>
                                {#each materials as mat}
                                    <option value={mat.id}>{mat.title}</option>
                                {/each}
                            </select>
                        </div>

                        {#if state.availableSubMaterials.length > 0}
                            <div class="space-y-2">
                                <label
                                    class="text-[10px] font-bold uppercase text-slate-400"
                                    >Sub-Materi</label
                                >
                                <select
                                    bind:value={$form.sub_material_id}
                                    class="w-full px-4 py-3 border-2 border-slate-100 rounded-2xl bg-white text-sm font-bold focus:ring-4 focus:ring-primary-50 focus:border-primary-500 outline-none transition-all appearance-none"
                                >
                                    <option value="">Pilih Sub-Materi</option>
                                    {#each state.availableSubMaterials as sub}
                                        <option value={sub.id}
                                            >{sub.title}</option
                                        >
                                    {/each}
                                </select>
                            </div>
                        {/if}

                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-bold uppercase text-slate-400"
                                >Algoritma Tipe</label
                            >
                            <div class="grid grid-cols-2 gap-3">
                                {#each ["multiple_choice", "true_false"] as type}
                                    <button
                                        type="button"
                                        onclick={() =>
                                            state.setQuestionType(type)}
                                        class={`py-3 px-4 rounded-2xl border-2 font-bold uppercase tracking-widest text-[10px] transition-all
                                        ${$form.question_type === type ? "border-primary-600 bg-primary-50 text-primary-600" : "border-slate-100 bg-slate-50 text-slate-400"}`}
                                    >
                                        {type === "multiple_choice"
                                            ? "Multiple Choice"
                                            : "True/False"}
                                    </button>
                                {/each}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-bold uppercase text-slate-400"
                                >Tingkat Kesulitan</label
                            >
                            <div class="space-y-2">
                                {#each [{ value: "easy", label: "Mudah", color: "emerald" }, { value: "medium", label: "Sedang", color: "amber" }, { value: "hard", label: "Sulit", color: "rose" }] as diff}
                                    <button
                                        type="button"
                                        onclick={() =>
                                            state.setDifficulty(diff.value)}
                                        class={`w-full py-3 px-4 rounded-2xl border-2 font-bold uppercase tracking-widest text-[10px] transition-all text-left
                                        ${$form.difficulty === diff.value ? `border-${diff.color}-600 bg-${diff.color}-50 text-${diff.color}-600` : "border-slate-100 bg-slate-50 text-slate-400"}`}
                                    >
                                        {diff.label}
                                    </button>
                                {/each}
                            </div>
                            {#if $form.errors.difficulty}
                                <p
                                    class="text-[10px] font-bold text-rose-500 uppercase tracking-widest"
                                >
                                    {$form.errors.difficulty}
                                </p>
                            {/if}
                        </div>

                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-bold uppercase text-slate-400"
                                >Skor Soal</label
                            >
                            <Input
                                type="number"
                                bind:value={$form.score}
                                placeholder="10"
                                error={$form.errors.score}
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    
                </div>

                <div class="flex gap-4">
                    
                    <Button
                        type="submit"
                        variant="primary"
                        size="lg"
                        class="shadow-xl shadow-primary-900/20"
                        icon={RefreshCw}
                        disabled={$form.processing}
                    >
                        {#if $form.processing}
                            Memproses...
                        {:else}
                            PERBARUI INSTRUMEN
                        {/if}
                    </Button>
                </div>
            </div>
        </div>
    </div>
</form>
    </div>
</App>
