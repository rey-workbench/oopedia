<script lang="ts">
    import App from '@/layouts/App.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import Button from '@/components/ui/Button.svelte';
    import DataTable from '@/components/ui/DataTable.svelte';
    import EmptyState from '@/components/ui/EmptyState.svelte';
    import Select from '@/components/ui/Select.svelte';
    import { ROUTES } from '@/utils/route';
    import { FlaskConical, Plus, Trash2, Edit2, ArrowLeft } from '@lucide/svelte';
    import ActionMenu from '@/components/ui/ActionMenu.svelte';
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
            <div class="w-48">
                <Select
                    bind:value={state.difficulty}
                    placeholder="SEMUA LEVEL"
                    options={difficultyOptions}
                    onchange={() => state.setDifficulty(state.difficulty)}
                />
            </div>
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

    <div id="question-table" class="mt-8">
        <DataTable
            title="Daftar Instrumen Evaluasi"
            items={state.questions.data}
            {columns}
            bind:search={state.search}
            searchPlaceholder="Cari teks soal atau identitas..."
            onsearch={state.handleSearch}
            links={state.questions.links}
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
                    <div class="flex justify-end">
                        <ActionMenu
                            id={index === 0 ? 'question-actions' : undefined}
                            items={[
                                {
                                    label: 'Edit Soal',
                                    icon: Edit2,
                                    href: state.material
                                        ? `/admin/materials/${state.material.id}/questions/${question.id}/edit`
                                        : ROUTES.ADMIN.QUESTIONS.EDIT(question.id),
                                },
                                { label: 'Hapus Soal', icon: Trash2, onclick: () => state.handleDelete(question.id), variant: 'danger' },
                            ]}
                        />
                    </div>
                </td>
            {/snippet}
        </DataTable>
    </div>
</App>
