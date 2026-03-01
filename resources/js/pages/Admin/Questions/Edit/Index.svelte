<script lang="ts">
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/shared/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import QuillEditor from "@/components/ui/QuillEditor.svelte";
    import { ArrowLeft, RefreshCw, Plus, X } from "lucide-svelte";
    import { QuestionEditState } from "@/states/Admin/QuestionState.svelte";

    let { materials = [], material = null, subMaterials = [], question } = $props();

    const state = new QuestionEditState(question, subMaterials);
    const form = state.form;
</script>

<App title={`Edit Soal #${question.id}`}>
    <div class="space-y-12 pb-20">
        <PageHeader
            title="Rekonfigurasi Soal"
            subtitle={`Memodifikasi instrumen penilaian #${question.id} untuk optimasi validitas.`}
        >
            {#snippet actions()}
                <Button
                    href={material
                        ? `/admin/materials/${material.id}/questions`
                        : "/admin/questions"}
                    variant="ghost"
                    icon={ArrowLeft}>BATALKAN</Button
                >
            {/snippet}
        </PageHeader>

        <form
            onsubmit={(e) => {
                e.preventDefault();
                state.submit();
            }}
            class="space-y-12"
        >
            <div
                class="bg-white rounded-3xl p-6 shadow-2xl border border-slate-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300"
            >
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
                                    bind:value={form.question_text}
                                    placeholder="Deskripsikan problematik pemrograman di sini..."
                                />
                                {#if form.errors && form.errors['question_text']}
                                    <p
                                        class="text-[10px] font-bold text-rose-500 uppercase tracking-widest"
                                    >
                                        {form.errors['question_text']}
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

                                {#each form.answers || [] as answer, i}
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
                                            <div
                                                class="flex items-center gap-3"
                                            >
                                                <label
                                                    class="flex items-center gap-2 cursor-pointer"
                                                >
                                                    <input
                                                        type="checkbox"
                                                        checked={answer.is_correct === 1}
                                                        onchange={(e) => { answer.is_correct = (e.target as HTMLInputElement).checked ? 1 : 0; }}
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
                                            onclick={() =>
                                                state.removeAnswer(i)}
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
                                        for="material_id"
                                        class="text-[10px] font-bold uppercase text-slate-400"
                                        >Modul Materi</label
                                    >
                                    <select
                                        id="material_id"
                                        bind:value={form.material_id}
                                        onchange={() =>
                                            state.handleMaterialChange()}
                                        class="w-full px-4 py-3 border-2 border-slate-100 rounded-2xl bg-white text-sm font-bold focus:ring-4 focus:ring-primary-50 focus:border-primary-500 outline-none transition-all appearance-none"
                                    >
                                        <option value="">Pilih Materi</option>
                                        {#each materials as mat}
                                            <option value={mat.id}
                                                >{mat.title}</option
                                            >
                                        {/each}
                                    </select>
                                </div>

                                {#if state.availableSubMaterials.length > 0}
                                    <div class="space-y-2">
                                        <label
                                            for="sub_material_id"
                                            class="text-[10px] font-bold uppercase text-slate-400"
                                            >Sub-Materi</label
                                        >
                                        <select
                                            id="sub_material_id"
                                            bind:value={form.sub_material_id}
                                            class="w-full px-4 py-3 border-2 border-slate-100 rounded-2xl bg-white text-sm font-bold focus:ring-4 focus:ring-primary-50 focus:border-primary-500 outline-none transition-all appearance-none"
                                        >
                                            <option value=""
                                                >Pilih Sub-Materi</option
                                            >
                                            {#each state.availableSubMaterials as sub}
                                                <option value={sub.id}
                                                    >{sub.title}</option
                                                >
                                            {/each}
                                        </select>
                                    </div>
                                {/if}

                                <div class="space-y-2">
                                    <span
                                        class="text-[10px] font-bold uppercase text-slate-400 block"
                                        >Algoritma Tipe</span
                                    >
                                    <div class="grid grid-cols-1 gap-3">
                                        {#each ["radio_button", "fill_in_the_blank", "drag_and_drop"] as type}
                                            <button
                                                type="button"
                                                onclick={() =>
                                                    state.setType(type)}
                                                class={`py-3 px-4 rounded-2xl border-2 font-bold uppercase tracking-widest text-[10px] transition-all text-left flex items-center justify-between
                                        ${form.question_type === type ? "border-primary-600 bg-primary-50 text-primary-600" : "border-slate-100 bg-slate-50 text-slate-400"}`}
                                            >
                                                {type.replace(/_/g, " ")}
                                            </button>
                                        {/each}
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <span
                                        class="text-[10px] font-bold uppercase text-slate-400 block"
                                        >Tingkat Kesulitan</span
                                    >
                                    <div class="space-y-2">
                                        {#each [{ value: "beginner", label: "Mudah", color: "emerald" }, { value: "medium", label: "Sedang", color: "amber" }, { value: "hard", label: "Sulit", color: "rose" }] as diff}
                                            <button
                                                type="button"
                                                onclick={() =>
                                                    state.setDifficulty(
                                                        diff.value,
                                                    )}
                                                class={`w-full py-3 px-4 rounded-2xl border-2 font-bold uppercase tracking-widest text-[10px] transition-all text-left
                                        ${form.difficulty === diff.value ? `border-${diff.color}-600 bg-${diff.color}-50 text-${diff.color}-600` : "border-slate-100 bg-slate-50 text-slate-400"}`}
                                            >
                                                {diff.label}
                                            </button>
                                        {/each}
                                    </div>
                                    {#if form.errors && form.errors['difficulty']}
                                        <p
                                            class="text-[10px] font-bold text-rose-500 uppercase tracking-widest"
                                        >
                                            {form.errors['difficulty']}
                                        </p>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="pt-6 border-t border-slate-100 flex items-center justify-between gap-4"
                    >
                        <div class="flex items-center gap-3"></div>

                        <div class="flex gap-4">
                            <Button
                                type="submit"
                                variant="primary"
                                size="lg"
                                class="shadow-xl shadow-primary-900/20"
                                icon={RefreshCw}
                                disabled={form.processing}
                            >
                                {#if form.processing}
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
