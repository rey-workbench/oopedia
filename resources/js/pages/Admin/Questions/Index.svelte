<script lang="ts">
    import App from "@/layouts/App.svelte";
    import Button from "@/components/ui/Button.svelte";
    import DataTable from "@/components/shared/DataTable.svelte";
    import Badge from "@/components/ui/Badge.svelte";
    import EmptyState from "@/components/ui/EmptyState.svelte";
    import Pagination from "@/components/ui/Pagination.svelte";
    import {
        Plus,
        ArrowLeft,
        FlaskConical,
        Search,
        Edit2,
        Trash2,
    } from "lucide-svelte";
    import PageHeader from "@/components/shared/PageHeader.svelte";
    import { ROUTES } from "@/utils/route";
    import { untrack } from 'svelte';
    import { QuestionListAdminState } from "@/states/Admin/QuestionState.svelte";

    let { questions = { data: [] }, material = null, search = "", difficulty = "" }: { questions: any; material: any; search: string; difficulty: string } = $props();

    const state = untrack(() => new QuestionListAdminState(
        questions,
        material,
        search,
        difficulty,
    ));

    const columns = $derived([
        { key: "question", label: "Pertanyaan", align: "left" },
        { key: "type", label: "Tipe", align: "left" },
        { key: "difficulty", label: "Tingkat", align: "left" },
        ...(state.material
            ? []
            : [{ key: "material", label: "Modul", align: "left" }]),
        { key: "actions", label: "Aksi", align: "right" },
    ]);

    function getDifficultyColor(diff: string) {
        if (diff === "beginner") return "success";
        if (diff === "medium") return "warning";
        return "danger";
    }
</script>

<App
    title={`Kelola Bank Soal ${state.material ? ": " + state.material.title : ""}`}
>
    <PageHeader
        title="Repositori Evaluasi"
        subtitle={state.material
            ? `Kumpulan instrumen penilaian untuk materi: ${state.material.title}`
            : "Manajemen komprehensif seluruh bank soal evaluasi sistem."}
    >
        {#snippet actions()}
            <Button
                href={state.material
                    ? ROUTES.ADMIN.MATERIALS.QUESTIONS.CREATE(state.material.id)
                    : ROUTES.ADMIN.QUESTIONS.CREATE}
                variant="primary"
                icon={Plus}>TAMBAH INSTRUMEN</Button
            >
            {#if state.material}
                <Button
                    href={ROUTES.ADMIN.MATERIALS.INDEX}
                    variant="ghost"
                    icon={ArrowLeft}>KEMBALI</Button
                >
            {/if}
        {/snippet}
    </PageHeader>

    <div class="flex flex-col md:flex-row gap-6 items-end">
        <div class="flex-1 space-y-2">
            <label
                for="q-search"
                class="text-[10px] font-bold uppercase text-slate-400 font-poppins ml-2"
            >
                Pencarian Soal
            </label>
            <div class="relative group">
                <input
                    type="text"
                    id="q-search"
                    bind:value={state.search}
                    oninput={state.handleSearch}
                    placeholder="Cari teks soal atau identitas..."
                    class="w-full bg-white border border-slate-100 rounded-2xl px-8 py-4 text-xs font-bold uppercase tracking-widest text-slate-900 group-hover:border-primary-400 focus:border-primary-600 focus:ring-4 focus:ring-primary-100 focus:outline-none transition-all duration-300 shadow-xl shadow-slate-100"
                />
                <Search
                    size={20}
                    strokeWidth={2.5}
                    class="absolute right-8 top-1/2 -translate-y-1/2 text-slate-300 group-hover:text-primary-600 transition-colors"
                />
            </div>
        </div>

        <div class="w-full md:w-64 space-y-2">
            <label
                for="q-difficulty"
                class="text-[10px] font-bold uppercase text-slate-400 font-poppins ml-2"
            >
                Tingkat Kesulitan
            </label>
            <select
                id="q-difficulty"
                bind:value={state.difficulty}
                onchange={() => state.setDifficulty(state.difficulty)}
                class="w-full bg-white border border-slate-100 rounded-2xl px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-900 focus:border-primary-600 focus:ring-4 focus:ring-primary-100 focus:outline-none transition-all cursor-pointer shadow-xl shadow-slate-100"
            >
                <option value="">SEMUA LEVEL</option>
                <option value="beginner">BEGINNER</option>
                <option value="medium">MEDIUM</option>
                <option value="hard">HARD</option>
            </select>
        </div>
    </div>

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

        {#snippet row(question)}
            <td class="px-6 py-6">
                <div class="flex flex-col gap-1 max-w-xl">
                    <span
                        class="text-xs font-bold text-slate-900 line-clamp-2 leading-relaxed"
                    >
                        {@html question.question_text}
                    </span>
                    <span
                        class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                    >
                        {question.answers_count} OPSI JAWABAN
                    </span>
                </div>
            </td>
            <td class="px-6 py-6">
                <Badge variant="secondary" size="xs">
                    {question.question_type.replace(/_/g, " ").toUpperCase()}
                </Badge>
            </td>
            <td class="px-6 py-6 font-bold text-xs uppercase text-slate-600">
                <div class="flex items-center gap-2">
                    <span
                        class={`w-2 h-2 rounded-full bg-${getDifficultyColor(question.difficulty)}-500`}
                    ></span>
                    {question.difficulty}
                </div>
            </td>
            {#if !state.material}
                <td class="px-6 py-6">
                    <div
                        class="text-[10px] font-bold text-primary-600 uppercase tracking-widest"
                    >
                        {question.material?.title || "GENERAL"}
                    </div>
                </td>
            {/if}
            <td class="px-6 py-6">
                <div class="flex justify-end gap-2">
                    <Button
                        variant="ghost"
                        size="sm"
                        href={state.material
                            ? `/admin/materials/${state.material.id}/questions/${question.id}/edit`
                            : ROUTES.ADMIN.QUESTIONS.EDIT(question.id)}
                        icon={Edit2}
                    />
                    <Button
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

    {#if state.questions.data.length > 0}
        <Pagination links={state.questions.links} />
    {/if}
</App>
