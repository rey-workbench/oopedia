<script>
    import Card from "@/components/ui/Card.svelte";
    import Button from "@/components/ui/Button.svelte";
    import Badge from "@/components/ui/Badge.svelte";
    import EmptyState from "@/components/ui/EmptyState.svelte";
    import Pagination from "@/components/ui/Pagination.svelte";
    import { FlaskConical, Edit2, Trash2 } from "lucide-svelte";

    export let state;

    function getDifficultyColor(diff) {
        if (diff === "beginner") return "success";
        if (diff === "medium") return "warning";
        return "danger";
    }
</script>

<Card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr>
                    <th
                        class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                        >Pertanyaan</th
                    >
                    <th
                        class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                        >Tipe</th
                    >
                    <th
                        class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                        >Tingkat</th
                    >
                    {#if !state.material}
                        <th
                            class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                            >Modul</th
                        >
                    {/if}
                    <th
                        class="p-6 text-right text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                        >Aksi</th
                    >
                </tr>
            </thead>
            <tbody>
                {#if state.questions.data.length === 0}
                    <tr>
                        <td colspan={state.material ? 4 : 5} class="p-0">
                            <EmptyState
                                title="Basis Data Kosong"
                                description="Tidak ditemukan instrumen evaluasi yang sesuai dengan filter pencarian."
                                icon={FlaskConical}
                            />
                        </td>
                    </tr>
                {:else}
                    {#each state.questions.data as question}
                        <tr
                            class="group hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0"
                        >
                            <td class="px-6 py-6">
                                <div class="flex flex-col gap-1 max-w-xl">
                                    <span
                                        class="text-xs font-bold text-slate-900 line-clamp-2 leading-relaxed"
                                        >{@html question.question_text}</span
                                    >
                                    <span
                                        class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                                        >{question.answers_count} OPSI JAWABAN</span
                                    >
                                </div>
                            </td>
                            <td class="px-6 py-6">
                                <Badge variant="ghost" size="xs">
                                    {question.question_type
                                        .replace(/_/g, " ")
                                        .toUpperCase()}
                                </Badge>
                            </td>
                            <td
                                class="px-6 py-6 font-bold text-xs uppercase text-slate-600"
                            >
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
                                            : `/admin/questions/${question.id}/edit`}
                                        icon={Edit2}
                                    />
                                    <button
                                        on:click={() =>
                                            state.handleDelete(question.id)}
                                        class="p-2 rounded-xl font-bold uppercase tracking-widest transition-all duration-300 flex items-center justify-center gap-2 hover:bg-slate-100 text-slate-300 hover:text-rose-500 text-xs"
                                    >
                                        <Trash2 size={18} strokeWidth={2.5} />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    {/each}
                {/if}
            </tbody>
        </table>
    </div>

    <Pagination links={state.questions.links} />
</Card>
