<script>
    import Card from "@/components/ui/Card.svelte";
    import Button from "@/components/ui/Button.svelte";
    import Input from "@/components/ui/Input.svelte";
    import QuillEditor from "@/components/ui/QuillEditor.svelte";
    import {
        Edit2,
        Plus,
        X,
        CheckCircle2,
        Save,
        RefreshCw,
    } from "lucide-svelte";
    import { QuestionFormState } from "@/states/Admin/QuestionState.svelte";

    export let materials = [];
    export let material = null;
    export let subMaterials = [];
    export let question = null; // For Edit mode

    const state = new QuestionFormState(
        materials,
        material,
        subMaterials,
        question,
    );
    const form = state.form;
</script>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
    <div class="lg:col-span-2 space-y-10">
        <Card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
            <div slot="header" class="flex items-center gap-3">
                <div
                    class="w-8 h-8 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center"
                >
                    <Edit2 size={16} />
                </div>
                <h6
                    class="text-xs font-bold tracking-widest uppercase mb-0 text-slate-800"
                >
                    Konten & Logika Pertanyaan
                </h6>
            </div>

            <div class="p-8 space-y-8">
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
                        <span
                            class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                            >Konfigurasi Opsi Jawaban</span
                        >

                        <Button
                            type="button"
                            on:click={() => state.addAnswer()}
                            variant="ghost"
                            size="xs"
                            icon={Plus}>TAMBAH OPSI</Button
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
                                            bind:group={$form.correct_answer}
                                            class="w-5 h-5 text-primary-600 focus:ring-primary-500 border-slate-300 transition-all cursor-pointer"
                                        />
                                    </div>
                                    <div class="flex-1 space-y-2">
                                        <input
                                            type="text"
                                            bind:value={answer.answer_text}
                                            placeholder={`Opsi Jawaban #${i + 1}`}
                                            class="w-full bg-white border-2 border-slate-100 rounded-2xl px-6 py-3 text-xs font-bold uppercase tracking-widest text-slate-900 focus:border-primary-600 focus:outline-none transition-all"
                                        />
                                        <input
                                            type="text"
                                            bind:value={answer.explanation}
                                            placeholder="Penjelasan/Feedback (Opsional)"
                                            class="w-full bg-white/50 border border-slate-100 rounded-xl px-4 py-2 text-[10px] font-bold text-slate-500 focus:border-primary-400 focus:outline-none transition-all"
                                        />
                                    </div>
                                    {#if $form.answers.length > 1}
                                        <button
                                            type="button"
                                            on:click={() =>
                                                state.removeAnswer(i)}
                                            class="p-2 text-slate-300 hover:text-rose-500 transition-colors"
                                        >
                                            <X size={18} />
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
        <Card padding="p-8" class="border-slate-100 shadow-2xl space-y-8">
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
                        on:change={() => state.handleMaterialChange()}
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-900 focus:border-primary-600 focus:outline-none transition-all cursor-pointer"
                    >
                        <option value="">PILIH MODUL</option>
                        {#each state.materials as m}
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
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-900 focus:border-primary-600 focus:outline-none transition-all cursor-pointer disabled:opacity-50"
                        disabled={!$form.material_id}
                    >
                        <option value="">TAG UNIT TERKAIT</option>
                        {#each state.availableSubMaterials as sm}
                            <option value={sm.id}>{sm.title}</option>
                        {/each}
                    </select>
                </div>

                <div class="space-y-2">
                    <span
                        class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                        >Tipe Algoritma</span
                    >

                    <div class="grid grid-cols-1 gap-2">
                        {#each ["radio_button", "fill_in_the_blank", "drag_and_drop"] as type}
                            <button
                                type="button"
                                on:click={() => state.setType(type)}
                                class={`py-4 px-6 rounded-2xl border-2 font-bold uppercase tracking-widest text-[10px] text-left transition-all flex items-center justify-between
                {$form.question_type === type ? "border-primary-600 bg-primary-50 text-primary-600" : "border-slate-50 bg-slate-50 text-slate-400"}`}
                            >
                                {type.replace(/_/g, " ")}
                                {#if $form.question_type === type}
                                    <CheckCircle2 size={16} />
                                {/if}
                            </button>
                        {/each}
                    </div>
                </div>

                <div class="space-y-2">
                    <span
                        class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                        >Level Kesulitan</span
                    >

                    <div class="flex gap-2">
                        {#each ["beginner", "medium", "hard"] as diff}
                            <button
                                type="button"
                                on:click={() => state.setDifficulty(diff)}
                                class={`flex-1 py-3 px-2 rounded-xl border-2 font-bold uppercase tracking-widest text-[9px] transition-all
                {$form.difficulty === diff ? "border-primary-600 bg-primary-50 text-primary-600" : "border-slate-50 bg-slate-50 text-slate-400"}`}
                            >
                                {diff}
                            </button>
                        {/each}
                    </div>
                </div>
            </div>

            <div class="pt-6">
                <Button
                    on:click={() => state.submit()}
                    variant="primary"
                    class="w-full py-4 shadow-xl shadow-primary-900/20"
                    icon={state.isEdit ? RefreshCw : Save}
                    disabled={$form.processing}
                >
                    {#if $form.processing}
                        {state.isEdit ? "MEMPERBARUI..." : "SINGKRONISASI..."}
                    {:else}
                        {state.isEdit
                            ? "PERBARUI INSTRUMEN"
                            : "SIMPAN INSTRUMEN"}
                    {/if}
                </Button>
            </div>
        </Card>
    </div>
</div>
