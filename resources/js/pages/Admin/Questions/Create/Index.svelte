<script lang="ts">
    import App from '@/layouts/App.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import Button from '@/components/ui/Button.svelte';
    import QuillEditor from '@/components/ui/QuillEditor.svelte';
    import DragDropEditor from '@/components/shared/DragDropEditor.svelte';
    import DragDropHandle from '@/components/shared/DragDropHandle.svelte';
    import { ArrowLeft, Save, Plus, X, CheckCircle2 } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';
    import { untrack } from 'svelte';
    import { QuestionFormState } from '@/states/Admin/QuestionState.svelte';

    let { materials = [], material = null, subMaterials = [] } = $props();

    const state = untrack(() => new QuestionFormState(materials, material, subMaterials, null));
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
                class="group relative overflow-hidden rounded-3xl border border-slate-100 bg-white p-6 shadow-2xl transition-transform duration-300 hover:-translate-y-1"
            >
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Konten & Logika Pertanyaan</h3>
                </div>

                <div class="space-y-10 p-6">
                    <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">
                        <div class="space-y-8 lg:col-span-2">
                            <div class="space-y-2">
                                <span
                                    class="font-poppins text-[10px] font-bold text-slate-400 uppercase"
                                >
                                    Teks Pertanyaan {#if state.form.question_type !== 'drag_and_drop'} (Rich Text) {/if}
                                </span>
                                {#if state.form.question_type === 'drag_and_drop'}
                                    <DragDropEditor bind:value={state.form.question_text} />
                                {:else}
                                    <QuillEditor
                                        bind:value={state.form.question_text}
                                        placeholder="Deskripsikan problematik pemrograman di sini..."
                                    />
                                {/if}
                                {#if state.form.errors['question_text']}
                                    <p
                                        class="text-[10px] font-bold tracking-widest text-rose-500 uppercase"
                                    >
                                        {state.form.errors['question_text']}
                                    </p>
                                {/if}
                            </div>

                            <div class="space-y-6">
                                <div class="flex items-center justify-between">
                                    <span
                                        class="font-poppins text-[10px] font-bold text-slate-400 uppercase"
                                    >
                                        Konfigurasi Opsi Jawaban
                                    </span>
                                    {#if state.form.question_type !== 'fill_in_the_blank'}
                                    <Button
                                        type="button"
                                        onclick={() => state.addAnswer()}
                                        variant="ghost"
                                        size="sm"
                                        icon={Plus}
                                    >
                                        TAMBAH OPSI
                                    </Button>
                                    {/if}
                                </div>

                                <div class="space-y-4">
                                    {#each state.form.answers as answer, i}
                                        <div
                                            class="group relative space-y-4 rounded-3xl border border-slate-100 bg-slate-50 p-6"
                                        >
                                            <div class="flex items-start gap-4">
                                                {#if state.form.question_type === 'radio_button' || state.form.question_type === 'multiple_choice'}
                                                    <div class="pt-2">
                                                        <input
                                                            type="radio"
                                                            name="correct_answer"
                                                            value={i}
                                                            bind:group={state.form.correct_answer}
                                                            class="text-primary-600 focus:ring-primary-500 h-5 w-5 cursor-pointer border-slate-300 transition-all"
                                                        />
                                                    </div>
                                                {:else if state.form.question_type === 'drag_and_drop'}
                                                    <div class="pt-2">
                                                        <DragDropHandle text={answer.answer_text as string} />
                                                    </div>
                                                {:else}
                                                    <div class="pt-2 flex flex-col items-center gap-1">
                                                        <input
                                                            type="checkbox"
                                                            checked={answer.is_correct === 1}
                                                            onchange={(e) => {
                                                                answer.is_correct = (e.target as HTMLInputElement).checked ? 1 : 0;
                                                            }}
                                                            class="text-primary-600 focus:ring-primary-500 h-5 w-5 cursor-pointer border-slate-300 transition-all"
                                                        />
                                                        <span class="text-[8px] font-bold tracking-widest text-slate-500 uppercase">Benar</span>
                                                    </div>
                                                {/if}

                                                <div class="flex-1 space-y-3 w-full max-w-full overflow-hidden">
                                                    {#if state.form.question_type === 'fill_in_the_blank'}
                                                        <div class="w-full bg-white rounded-xl overflow-hidden border-2 border-slate-100">
                                                            <QuillEditor
                                                                bind:value={answer.answer_text as string}
                                                                placeholder="Ketik isi / bagian rumpang di sini..."
                                                            />
                                                        </div>
                                                    {:else if state.form.question_type === 'drag_and_drop'}
                                                        <input
                                                            type="text"
                                                            bind:value={answer.answer_text}
                                                            placeholder="Teks Jawaban (Item Drag)"
                                                            class="focus:border-primary-600 w-full rounded-2xl border-2 border-slate-100 bg-white px-6 py-3 text-xs font-bold tracking-widest text-slate-900 transition-all focus:outline-none"
                                                        />
                                                    {:else}
                                                        <input
                                                            type="text"
                                                            bind:value={answer.answer_text}
                                                            placeholder={`Opsi Jawaban #${i + 1}`}
                                                            class="focus:border-primary-600 w-full rounded-2xl border-2 border-slate-100 bg-white px-6 py-3 text-xs font-bold tracking-widest text-slate-900 uppercase transition-all focus:outline-none"
                                                        />
                                                    {/if}

                                                    <input
                                                        type="text"
                                                        bind:value={answer.explanation}
                                                        placeholder="Penjelasan/Feedback (Opsional)"
                                                        class="focus:border-primary-400 w-full rounded-xl border border-slate-100 bg-white/50 px-4 py-2 text-[10px] font-bold text-slate-500 transition-all focus:outline-none"
                                                    />
                                                </div>
                                                {#if state.form.question_type !== 'fill_in_the_blank' && state.form.answers.length > 1}
                                                <Button
                                                    type="button"
                                                    onclick={() => state.removeAnswer(i)}
                                                    variant="ghost"
                                                    size="sm"
                                                    icon={X}
                                                    class="p-2 text-slate-300 hover:bg-rose-50 hover:text-rose-500 shrink-0"
                                                />
                                                {/if}
                                            </div>
                                        </div>
                                    {/each}
                                </div>
                            </div>
                        </div>

                        <div class="space-y-8 lg:col-span-1">
                            <h6
                                class="mb-0 border-b border-slate-50 pb-4 text-xs font-bold tracking-widest text-slate-900 uppercase"
                            >
                                Atribut Metadata
                            </h6>

                            <div class="space-y-6">
                                <!-- Modul Utama -->
                                <div class="space-y-2">
                                    <label
                                        for="material"
                                        class="font-poppins text-[10px] font-bold text-slate-400 uppercase"
                                    >
                                        Modul Utama
                                    </label>
                                    <select
                                        id="material"
                                        bind:value={state.form.material_id}
                                        onchange={() => state.handleMaterialChange()}
                                        class="focus:border-primary-600 w-full cursor-pointer rounded-2xl border-2 border-slate-100 bg-slate-50 px-6 py-4 text-xs font-bold tracking-widest text-slate-900 uppercase transition-all focus:outline-none"
                                    >
                                        <option value="">PILIH MODUL</option>
                                        {#each state.materials as m}
                                            <option value={m.id}>{m.title}</option>
                                        {/each}
                                    </select>
                                    {#if state.form.errors['material_id']}
                                        <p
                                            class="text-[10px] font-bold tracking-widest text-rose-500 uppercase"
                                        >
                                            {state.form.errors['material_id']}
                                        </p>
                                    {/if}
                                </div>

                                <!-- Unit Spesifik -->
                                <div class="space-y-2">
                                    <label
                                        for="sub_material"
                                        class="font-poppins text-[10px] font-bold text-slate-400 uppercase"
                                    >
                                        Unit Spesifik (Opsional)
                                    </label>
                                    <select
                                        id="sub_material"
                                        bind:value={state.form.sub_material_id}
                                        class="focus:border-primary-600 w-full cursor-pointer rounded-2xl border-2 border-slate-100 bg-slate-50 px-6 py-4 text-xs font-bold tracking-widest text-slate-900 uppercase transition-all focus:outline-none disabled:opacity-50"
                                        disabled={!state.form.material_id}
                                    >
                                        <option value="">TAG UNIT TERKAIT</option>
                                        {#each state.availableSubMaterials as sm}
                                            <option value={sm.id}>{sm.title}</option>
                                        {/each}
                                    </select>
                                </div>

                                <!-- Tipe Algoritma -->
                                <div class="space-y-2">
                                    <span
                                        class="font-poppins text-[10px] font-bold text-slate-400 uppercase"
                                        >Tipe Algoritma</span
                                    >
                                    <div class="grid grid-cols-1 gap-2">
                                        {#each ['radio_button', 'fill_in_the_blank', 'drag_and_drop'] as type}
                                            <button
                                                type="button"
                                                onclick={() => state.setType(type)}
                                                class={`flex items-center justify-between rounded-2xl border-2 px-6 py-4 text-left text-[10px] font-bold tracking-widest uppercase transition-all
                                            ${state.form.question_type === type ? 'border-primary-600 bg-primary-50 text-primary-600' : 'border-slate-50 bg-slate-50 text-slate-400'}`}
                                            >
                                                {type.replace(/_/g, ' ')}
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
                                        class="font-poppins text-[10px] font-bold text-slate-400 uppercase"
                                        >Level Kesulitan</span
                                    >
                                    <div class="flex gap-2">
                                        {#each ['beginner', 'medium', 'hard'] as diff}
                                            <button
                                                type="button"
                                                onclick={() => state.setDifficulty(diff)}
                                                class={`flex-1 rounded-xl border-2 px-2 py-3 text-[9px] font-bold tracking-widest uppercase transition-all
                                            ${state.form.difficulty === diff ? 'border-primary-600 bg-primary-50 text-primary-600' : 'border-slate-50 bg-slate-50 text-slate-400'}`}
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
                        class="flex items-center justify-between gap-4 border-t border-slate-100 pt-6"
                    >
                        <div class="flex items-center gap-3"></div>

                        <div class="flex gap-4">
                            <Button
                                type="submit"
                                variant="primary"
                                size="lg"
                                class="shadow-primary-900/20 shadow-xl"
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
