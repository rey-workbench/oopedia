<script lang="ts">
    import App from '@/layouts/App.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import Button from '@/components/ui/Button.svelte';
    import QuillEditor from '@/components/ui/QuillEditor.svelte';
    import DragDropEditor from '@/components/shared/DragDropEditor.svelte';
    import DragDropHandle from '@/components/shared/DragDropHandle.svelte';
    import { ArrowLeft, RefreshCw, Plus, X } from 'lucide-svelte';
    import { untrack } from 'svelte';
    import { QuestionEditState } from '@/states/Admin/QuestionState.svelte';

    let { materials = [], material = null, subMaterials = [], question } = $props();

    const state = untrack(() => new QuestionEditState(question, subMaterials));
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
                        : '/admin/questions'}
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
                    <h3 class="text-lg font-bold text-slate-800">Update Konten & Logika</h3>
                </div>

                <div class="space-y-10 p-6">
                    <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">
                        <div class="space-y-8 lg:col-span-2">
                            <div class="space-y-2">
                                <span
                                    class="font-poppins text-[10px] font-bold text-slate-400 uppercase"
                                    >Teks Pertanyaan {#if form.question_type !== 'drag_and_drop'} (Rich Text) {/if}</span
                                >
                                {#if form.question_type === 'drag_and_drop'}
                                    <DragDropEditor bind:value={form.question_text} />
                                {:else}
                                    <QuillEditor
                                        bind:value={form.question_text}
                                        placeholder="Deskripsikan problematik pemrograman di sini..."
                                    />
                                {/if}
                                {#if form.errors && form.errors['question_text']}
                                    <p
                                        class="text-[10px] font-bold tracking-widest text-rose-500 uppercase"
                                    >
                                        {form.errors['question_text']}
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
                                    {#if form.question_type !== 'fill_in_the_blank'}
                                    <Button
                                        type="button"
                                        onclick={() => state.addAnswer()}
                                        variant="ghost"
                                        size="sm"
                                        icon={Plus}
                                    >
                                        Tambah Opsi
                                    </Button>
                                    {/if}
                                </div>

                                {#each form.answers || [] as answer, i}
                                    <div
                                        class="group relative space-y-4 rounded-3xl border border-slate-100 bg-slate-50 p-6"
                                    >
                                        <div class="flex items-start gap-4">
                                            {#if form.question_type === 'radio_button' || form.question_type === 'multiple_choice'}
                                                <div class="pt-2">
                                                    <input
                                                        type="radio"
                                                        name="correct_answer"
                                                        value={i}
                                                        bind:group={form.correct_answer}
                                                        class="text-primary-600 focus:ring-primary-500 h-5 w-5 cursor-pointer border-slate-300 transition-all"
                                                    />
                                                </div>
                                            {:else if form.question_type === 'drag_and_drop'}
                                                <div class="pt-2">
                                                    <DragDropHandle data={answer.answer_text as string} />
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
                                                {#if form.question_type === 'fill_in_the_blank'}
                                                    <div class="w-full bg-white rounded-xl overflow-hidden border-2 border-slate-100">
                                                        <QuillEditor
                                                            bind:value={answer.answer_text as string}
                                                            placeholder="Ketik isi / bagian rumpang di sini..."
                                                        />
                                                    </div>
                                                {:else if form.question_type === 'drag_and_drop'}
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
                                            {#if form.question_type !== 'fill_in_the_blank' && (form.answers || []).length > 1}
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

                        <div class="lg:col-span-1">
                            <div class="space-y-8">
                                <h3
                                    class="text-sm font-bold tracking-widest text-slate-900 uppercase"
                                >
                                    Parametrik
                                </h3>

                                <div class="space-y-2">
                                    <label
                                        for="material_id"
                                        class="text-[10px] font-bold text-slate-400 uppercase"
                                        >Modul Materi</label
                                    >
                                    <select
                                        id="material_id"
                                        bind:value={form.material_id}
                                        onchange={() => state.handleMaterialChange()}
                                        class="focus:ring-primary-50 focus:border-primary-500 w-full appearance-none rounded-2xl border-2 border-slate-100 bg-white px-4 py-3 text-sm font-bold transition-all outline-none focus:ring-4"
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
                                            for="sub_material_id"
                                            class="text-[10px] font-bold text-slate-400 uppercase"
                                            >Sub-Materi</label
                                        >
                                        <select
                                            id="sub_material_id"
                                            bind:value={form.sub_material_id}
                                            class="focus:ring-primary-50 focus:border-primary-500 w-full appearance-none rounded-2xl border-2 border-slate-100 bg-white px-4 py-3 text-sm font-bold transition-all outline-none focus:ring-4"
                                        >
                                            <option value="">Pilih Sub-Materi</option>
                                            {#each state.availableSubMaterials as sub}
                                                <option value={sub.id}>{sub.title}</option>
                                            {/each}
                                        </select>
                                    </div>
                                {/if}

                                <div class="space-y-2">
                                    <span
                                        class="block text-[10px] font-bold text-slate-400 uppercase"
                                        >Algoritma Tipe</span
                                    >
                                    <div class="grid grid-cols-1 gap-3">
                                        {#each ['radio_button', 'fill_in_the_blank', 'drag_and_drop'] as type}
                                            <button
                                                type="button"
                                                onclick={() => state.setType(type)}
                                                class={`flex items-center justify-between rounded-2xl border-2 px-4 py-3 text-left text-[10px] font-bold tracking-widest uppercase transition-all
                                        ${form.question_type === type ? 'border-primary-600 bg-primary-50 text-primary-600' : 'border-slate-100 bg-slate-50 text-slate-400'}`}
                                            >
                                                {type.replace(/_/g, ' ')}
                                            </button>
                                        {/each}
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <span
                                        class="block text-[10px] font-bold text-slate-400 uppercase"
                                        >Tingkat Kesulitan</span
                                    >
                                    <div class="space-y-2">
                                        {#each [{ value: 'beginner', label: 'Mudah', color: 'emerald' }, { value: 'medium', label: 'Sedang', color: 'amber' }, { value: 'hard', label: 'Sulit', color: 'rose' }] as diff}
                                            <button
                                                type="button"
                                                onclick={() => state.setDifficulty(diff.value)}
                                                class={`w-full rounded-2xl border-2 px-4 py-3 text-left text-[10px] font-bold tracking-widest uppercase transition-all
                                        ${form.difficulty === diff.value ? `border-${diff.color}-600 bg-${diff.color}-50 text-${diff.color}-600` : 'border-slate-100 bg-slate-50 text-slate-400'}`}
                                            >
                                                {diff.label}
                                            </button>
                                        {/each}
                                    </div>
                                    {#if form.errors && form.errors['difficulty']}
                                        <p
                                            class="text-[10px] font-bold tracking-widest text-rose-500 uppercase"
                                        >
                                            {form.errors['difficulty']}
                                        </p>
                                    {/if}
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
