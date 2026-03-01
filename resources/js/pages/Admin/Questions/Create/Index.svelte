<script lang="ts">
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/shared/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import QuillEditor from "@/components/ui/QuillEditor.svelte";
    import { ArrowLeft, Save, Plus, X, CheckCircle2 } from "lucide-svelte";
    import { ROUTES } from "@/utils/route";
    import { untrack } from 'svelte';
    import { QuestionFormState } from "@/states/Admin/QuestionState.svelte";

    let { materials = [], material = null, subMaterials = [] } = $props();

    const state = untrack(() => new QuestionFormState(
        materials,
        material,
        subMaterials,
        null,
    ));
</script>

<App title="Buat Instrumen Baru">
    <div class="space-y-12 pb-20">
        <PageHeader
            title="Engineering Evaluasi"
            subtitle="Membangun instrumen penilaian baru dengan parameter algoritma yang presisi."
        >
            {#snippet actions()}
                <Button
                    href={material
                        ? ROUTES.ADMIN.MATERIALS.QUESTIONS.INDEX(material.id)
                        : ROUTES.ADMIN.QUESTIONS.INDEX}
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
                        Konten & Logika Pertanyaan
                    </h3>
                </div>

                <div class="space-y-10 p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                        <div class="lg:col-span-2 space-y-8">
                            <div class="space-y-2">
                                <span
                                    class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                                >
                                    Teks Pertanyaan (Rich Text)
                                </span>
                                <QuillEditor
                                    bind:value={state.form.question_text}
                                    placeholder="Deskripsikan problematik pemrograman di sini..."
                                />
                                {#if state.form.errors['question_text']}
                                    <p
                                        class="text-[10px] font-bold text-rose-500 uppercase tracking-widest"
                                    >
                                        {state.form.errors['question_text']}
                                    </p>
                                {/if}
                            </div>

                            <div class="space-y-6">
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                                    >
                                        Konfigurasi Opsi Jawaban
                                    </span>
                                    <Button
                                        type="button"
                                        onclick={() => state.addAnswer()}
                                        variant="ghost"
                                        size="sm"
                                        icon={Plus}
                                    >
                                        TAMBAH OPSI
                                    </Button>
                                </div>

                                <div class="space-y-4">
                                    {#each state.form.answers as answer, i}
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
                                                            state.form.correct_answer
                                                        }
                                                        class="w-5 h-5 text-primary-600 focus:ring-primary-500 border-slate-300 transition-all cursor-pointer"
                                                    />
                                                </div>
                                                <div class="flex-1 space-y-2">
                                                    <input
                                                        type="text"
                                                        bind:value={
                                                            answer.answer_text
                                                        }
                                                        placeholder={`Opsi Jawaban #${i + 1}`}
                                                        class="w-full bg-white border-2 border-slate-100 rounded-2xl px-6 py-3 text-xs font-bold uppercase tracking-widest text-slate-900 focus:border-primary-600 focus:outline-none transition-all"
                                                    />
                                                    <input
                                                        type="text"
                                                        bind:value={
                                                            answer.explanation
                                                        }
                                                        placeholder="Penjelasan/Feedback (Opsional)"
                                                        class="w-full bg-white/50 border border-slate-100 rounded-xl px-4 py-2 text-[10px] font-bold text-slate-500 focus:border-primary-400 focus:outline-none transition-all"
                                                    />
                                                </div>
                                                {#if state.form.answers.length > 1}
                                                    <Button
                                                        type="button"
                                                        onclick={() =>
                                                            state.removeAnswer(
                                                                i,
                                                            )}
                                                        variant="ghost"
                                                        size="sm"
                                                        icon={X}
                                                        class="text-slate-300 hover:text-rose-500 hover:bg-rose-50 p-2"
                                                    />
                                                {/if}
                                            </div>
                                        </div>
                                    {/each}
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-1 space-y-8">
                            <h6
                                class="text-xs font-bold tracking-widest uppercase text-slate-900 border-b border-slate-50 pb-4 mb-0"
                            >
                                Atribut Metadata
                            </h6>

                            <div class="space-y-6">
                                <!-- Modul Utama -->
                                <div class="space-y-2">
                                    <label
                                        for="material"
                                        class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                                    >
                                        Modul Utama
                                    </label>
                                    <select
                                        id="material"
                                        bind:value={state.form.material_id}
                                        onchange={() =>
                                            state.handleMaterialChange()}
                                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-900 focus:border-primary-600 focus:outline-none transition-all cursor-pointer"
                                    >
                                        <option value="">PILIH MODUL</option>
                                        {#each state.materials as m}
                                            <option value={m.id}
                                                >{m.title}</option
                                            >
                                        {/each}
                                    </select>
                                    {#if state.form.errors['material_id']}
                                        <p
                                            class="text-[10px] font-bold text-rose-500 uppercase tracking-widest"
                                        >
                                            {state.form.errors['material_id']}
                                        </p>
                                    {/if}
                                </div>

                                <!-- Unit Spesifik -->
                                <div class="space-y-2">
                                    <label
                                        for="sub_material"
                                        class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                                    >
                                        Unit Spesifik (Opsional)
                                    </label>
                                    <select
                                        id="sub_material"
                                        bind:value={state.form.sub_material_id}
                                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-900 focus:border-primary-600 focus:outline-none transition-all cursor-pointer disabled:opacity-50"
                                        disabled={!state.form.material_id}
                                    >
                                        <option value=""
                                            >TAG UNIT TERKAIT</option
                                        >
                                        {#each state.availableSubMaterials as sm}
                                            <option value={sm.id}
                                                >{sm.title}</option
                                            >
                                        {/each}
                                    </select>
                                </div>

                                <!-- Tipe Algoritma -->
                                <div class="space-y-2">
                                    <span
                                        class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                                        >Tipe Algoritma</span
                                    >
                                    <div class="grid grid-cols-1 gap-2">
                                        {#each ["radio_button", "fill_in_the_blank", "drag_and_drop"] as type}
                                            <button
                                                type="button"
                                                onclick={() =>
                                                    state.setType(type)}
                                                class={`py-4 px-6 rounded-2xl border-2 font-bold uppercase tracking-widest text-[10px] text-left transition-all flex items-center justify-between
                                            ${state.form.question_type === type ? "border-primary-600 bg-primary-50 text-primary-600" : "border-slate-50 bg-slate-50 text-slate-400"}`}
                                            >
                                                {type.replace(/_/g, " ")}
                                                {#if state.form.question_type === type}
                                                    <CheckCircle2 size={16} />
                                                {/if}
                                            </button>
                                        {/each}
                                    </div>
                                </div>

                                <!-- Level Kesulitan -->
                                <div class="space-y-2">
                                    <span
                                        class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                                        >Level Kesulitan</span
                                    >
                                    <div class="flex gap-2">
                                        {#each ["beginner", "medium", "hard"] as diff}
                                            <button
                                                type="button"
                                                onclick={() =>
                                                    state.setDifficulty(diff)}
                                                class={`flex-1 py-3 px-2 rounded-xl border-2 font-bold uppercase tracking-widest text-[9px] transition-all
                                            ${state.form.difficulty === diff ? "border-primary-600 bg-primary-50 text-primary-600" : "border-slate-50 bg-slate-50 text-slate-400"}`}
                                            >
                                                {diff}
                                            </button>
                                        {/each}
                                    </div>
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
                                icon={Save}
                                disabled={state.form.processing}
                            >
                                {#if state.form.processing}
                                    Memproses...
                                {:else}
                                    SIMPAN INSTRUMEN
                                {/if}
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</App>
