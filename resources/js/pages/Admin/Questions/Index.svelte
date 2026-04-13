<script lang="ts">
    import App from '@/layouts/App.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import Button from '@/components/ui/Button.svelte';
    import DataTable from '@/components/ui/DataTable.svelte';
    import EmptyState from '@/components/ui/EmptyState.svelte';
    import Pagination from '@/components/ui/Pagination.svelte';
    import Select from '@/components/ui/Select.svelte';
    import { ROUTES } from '@/utils/route';
    import { FlaskConical, Plus, Trash2, Search, Edit2, ArrowLeft } from 'lucide-svelte';
    import { QuestionListAdminState } from '@/states/Admin/QuestionState.svelte';
    import { untrack } from 'svelte';
    import Badge from '@/components/ui/Badge.svelte';

    import type { Pagination as PaginationType, Question } from '@/types';

    let { questions } = $props<{ questions: PaginationType<Question> }>();

    const state = untrack(() => new QuestionListAdminState(questions, null, '', ''));

    const columns = $derived([
        { key: 'question', label: 'Pertanyaan', align: 'left' },
        { key: 'type', label: 'Tipe', align: 'left' },
        { key: 'difficulty', label: 'Tingkat', align: 'left' },
        ...(state.material ? [] : [{ key: 'material', label: 'Modul', align: 'left' }]),
        { key: 'actions', label: 'Aksi', align: 'right' },
    ]);

    const difficultyOptions = [
        { value: '', label: 'SEMUA LEVEL' },
        { value: 'beginner', label: 'BEGINNER' },
        { value: 'medium', label: 'MEDIUM' },
        { value: 'hard', label: 'HARD' },
    ];
</script>

<App title={`Kelola Bank Soal ${state.material ? ': ' + state.material.title : ''}`}>
    <PageHeader
        id="page-header"
        title="Repositori Evaluasi"
        subtitle={state.material
            ? `Kumpulan instrumen penilaian untuk materi: ${state.material.title}`
            : 'Manajemen komprehensif seluruh bank soal evaluasi sistem.'}
    >
        {#snippet actions()}
            <Button
                id="add-question-btn"
                href={state.material
                    ? ROUTES.ADMIN.MATERIALS.QUESTIONS.CREATE(state.material.id)
                    : ROUTES.ADMIN.QUESTIONS.CREATE}
                variant="primary"
                icon={Plus}>TAMBAH INSTRUMEN</Button
            >
            {#if state.material}
                <Button href={ROUTES.ADMIN.MATERIALS.INDEX} variant="ghost" icon={ArrowLeft}
                    >KEMBALI</Button
                >
            {/if}
        {/snippet}
    </PageHeader>

    <div class="flex flex-col items-end gap-6 md:flex-row">
        <div id="question-filter-search" class="flex-1 space-y-2">
            <label
                for="q-search"
                class="font-poppins ml-2 text-[10px] font-bold text-slate-400 uppercase"
            >
                Pencarian Soal
            </label>
            <div class="group relative">
                <input
                    type="text"
                    id="q-search"
                    bind:value={state.search}
                    oninput={state.handleSearch}
                    placeholder="Cari teks soal atau identitas..."
                    class="group-hover:border-primary-400 focus:border-primary-600 focus:ring-primary-100 w-full rounded-2xl border border-slate-100 bg-white px-8 py-4 text-xs font-bold tracking-widest text-slate-900 uppercase shadow-xl shadow-slate-100 transition-all duration-300 focus:ring-4 focus:outline-none"
                />
                <Search
                    size={20}
                    strokeWidth={2.5}
                    class="group-hover:text-primary-600 absolute top-1/2 right-8 -translate-y-1/2 text-slate-300 transition-colors"
                />
            </div>
        </div>

        <div id="question-filter-difficulty" class="w-full space-y-2 md:w-64">
            <Select
                bind:value={state.difficulty}
                placeholder="SEMUA LEVEL"
                options={difficultyOptions}
                onchange={() => state.setDifficulty(state.difficulty)}
            />
        </div>
    </div>

    <div id="question-table">
        <DataTable
            title="Daftar Instrumen Evaluasi"
            items={state.questions.data}
            {columns}
            hideSearch={true}
        >
            {#snippet empty()}
                <EmptyState
                    title="Basis Data Kosong"
                    description="Tidak ditemukan instrumen evaluasi yang sesuai dengan filter pencarian."
                    icon={FlaskConical}
                />
            {/snippet}

            {#snippet row(question, index)}
                <td class="px-6 py-6">
                    <div class="flex max-w-xl flex-col gap-1">
                        <span class="line-clamp-2 text-xs leading-relaxed font-bold text-slate-900">
                            {@html question.question_text}
                        </span>
                        <span
                            class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                        >
                            {question.answers_count} OPSI JAWABAN
                        </span>
                    </div>
                </td>
                <td class="px-6 py-6">
                    <Badge variant="secondary" size="xs">
                        {question.question_type.replace(/_/g, ' ').toUpperCase()}
                    </Badge>
                </td>
                <td class="px-6 py-6 text-xs font-bold text-slate-600 uppercase">
                    <div class="flex items-center gap-2">
                        <span
                            class={`h-2 w-2 rounded-full ${
                                question.difficulty === 'beginner'
                                    ? 'bg-emerald-500'
                                    : question.difficulty === 'medium'
                                      ? 'bg-amber-500'
                                      : 'bg-rose-500'
                            }`}
                        ></span>
                        {question.difficulty}
                    </div>
                </td>
                {#if !state.material}
                    <td class="px-6 py-6">
                        <div
                            class="text-primary-600 text-[10px] font-bold tracking-widest uppercase"
                        >
                            {question.material?.title || 'GENERAL'}
                        </div>
                    </td>
                {/if}
                <td class="px-6 py-6">
                    <div
                        id={index === 0 ? 'question-actions' : undefined}
                        class="flex justify-end gap-2"
                    >
                        <Button
                            id={index === 0 ? 'btn-edit-question' : undefined}
                            variant="ghost"
                            size="sm"
                            href={state.material
                                ? `/admin/materials/${state.material.id}/questions/${question.id}/edit`
                                : ROUTES.ADMIN.QUESTIONS.EDIT(question.id)}
                            icon={Edit2}
                        />
                        <Button
                            id={index === 0 ? 'btn-delete-question' : undefined}
                            variant="ghost"
                            size="sm"
                            onclick={() => state.handleDelete(question.id)}
                            icon={Trash2}
                            class="text-slate-300 hover:text-rose-500"
                        />
                    </div>
                </td>
            {/snippet}
        </DataTable>
    </div>

    {#if state.questions.data.length > 0}
        <Pagination links={state.questions.links} />
    {/if}
</App>
