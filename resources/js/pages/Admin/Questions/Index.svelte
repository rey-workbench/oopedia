<script>
    import App from "../../../layouts/App.svelte";
    import PageHeader from "../../../components/ui/PageHeader.svelte";
    import Card from "../../../components/ui/Card.svelte";
    import Button from "../../../components/ui/Button.svelte";
    import Badge from "../../../components/ui/Badge.svelte";
    import { router, Link } from "@inertiajs/svelte";
    import {
        Plus,
        ArrowLeft,
        Search,
        FlaskConical,
        Edit2,
        Trash2,
    } from "lucide-svelte";
    import { debounce } from "lodash";

    export let questions = { data: [] };
    export let material = null;
    export let search = "";
    export let difficulty = "";

    let searchQuery = search;
    let difficultyFilter = difficulty;

    const handleSearch = debounce(() => {
        router.get(
            material
                ? `/admin/materials/${material.id}/questions`
                : "/admin/questions",
            {
                search: searchQuery,
                difficulty: difficultyFilter,
            },
            { preserveState: true, preserveScroll: true },
        );
    }, 300);

    function handleDifficultyChange() {
        handleSearch();
    }

    function handleDelete(id) {
        if (confirm("Hapus soal ini?")) {
            router.delete(
                material
                    ? `/admin/materials/${material.id}/questions/${id}`
                    : `/admin/questions/${id}`,
            );
        }
    }

    function getDifficultyColor(diff) {
        if (diff === "beginner") return "success";
        if (diff === "medium") return "warning";
        return "danger";
    }
</script>

<App title={`Kelola Bank Soal ${material ? ": " + material.title : ""}`}>
    <div class="space-y-12">
        <PageHeader
            title="Repositori Evaluasi"
            subtitle={material
                ? `Kumpulan instrumen penilaian untuk materi: ${material.title}`
                : "Manajemen komprehensif seluruh bank soal evaluasi sistem."}
        >
            <div slot="actions" class="flex flex-wrap items-center gap-4">
                <Button
                    href={material
                        ? `/admin/materials/${material.id}/questions/create`
                        : "/admin/questions/create"}
                    variant="primary"
                    icon={Plus}>TAMBAH INSTRUMEN</Button
                >
                {#if material}
                    <Button
                        href="/admin/materials"
                        variant="ghost"
                        icon={ArrowLeft}>KEMBALI</Button
                    >
                {/if}
            </div>
        </PageHeader>

        <div class="flex flex-col md:flex-row gap-6 items-end">
            <div class="flex-1 space-y-2">
                <label
                    for="search"
                    class="text-[10px] font-bold uppercase text-slate-400 font-poppins ml-2"
                    >Pencarian Soal</label
                >
                <div class="relative group">
                    <input
                        type="text"
                        id="search"
                        bind:value={searchQuery}
                        on:input={handleSearch}
                        placeholder="Cari teks soal atau identitas..."
                        class="w-full bg-white border-2 border-slate-100 rounded-[2rem] px-8 py-4 text-xs font-bold uppercase tracking-widest text-slate-900 group-hover:border-blue-400 focus:border-blue-600 focus:outline-none transition-all duration-300 shadow-xl shadow-slate-100"
                    />
                    <Search
                        size={20}
                        strokeWidth={2.5}
                        class="absolute right-8 top-1/2 -translate-y-1/2 text-slate-300 group-hover:text-blue-500 transition-colors"
                    />
                </div>
            </div>

            <div class="w-full md:w-64 space-y-2">
                <label
                    for="difficulty"
                    class="text-[10px] font-bold uppercase text-slate-400 font-poppins ml-2"
                    >Tingkat Kesulitan</label
                >
                <select
                    id="difficulty"
                    bind:value={difficultyFilter}
                    on:change={handleDifficultyChange}
                    class="w-full bg-white border-2 border-slate-100 rounded-[1.5rem] px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-900 focus:border-blue-600 focus:outline-none transition-all cursor-pointer shadow-xl shadow-slate-100"
                >
                    <option value="">SEMUA LEVEL</option>
                    <option value="beginner">BEGINNER</option>
                    <option value="medium">MEDIUM</option>
                    <option value="hard">HARD</option>
                </select>
            </div>
        </div>

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
                            {#if !material}
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
                        {#if questions.data.length === 0}
                            <tr>
                                <td
                                    colspan={material ? 4 : 5}
                                    class="p-20 text-center"
                                >
                                    <div
                                        class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6"
                                    >
                                        <FlaskConical
                                            size={32}
                                            strokeWidth={1.5}
                                            class="text-slate-200"
                                        />
                                    </div>
                                    <h3
                                        class="text-lg font-bold uppercase tracking-widest text-slate-900 mb-2"
                                    >
                                        Basis Data Kosong
                                    </h3>
                                    <p
                                        class="text-[10px] font-bold text-slate-400 uppercase tracking-widest max-w-xs mx-auto mb-8"
                                    >
                                        Tidak ditemukan instrumen evaluasi yang
                                        sesuai dengan filter pencarian.
                                    </p>
                                </td>
                            </tr>
                        {:else}
                            {#each questions.data as question}
                                <tr
                                    class="group hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0"
                                >
                                    <td class="px-6 py-6">
                                        <div
                                            class="flex flex-col gap-1 max-w-xl"
                                        >
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
                                    {#if !material}
                                        <td class="px-6 py-6">
                                            <div
                                                class="text-[10px] font-bold text-blue-600 uppercase tracking-widest"
                                            >
                                                {question.material?.title ||
                                                    "GENERAL"}
                                            </div>
                                        </td>
                                    {/if}
                                    <td class="px-6 py-6">
                                        <div class="flex justify-end gap-2">
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                href={material
                                                    ? `/admin/materials/${material.id}/questions/${question.id}/edit`
                                                    : `/admin/questions/${question.id}/edit`}
                                                icon={Edit2}
                                            />
                                            <button
                                                on:click={() =>
                                                    handleDelete(question.id)}
                                                class="p-2 rounded-xl font-bold uppercase tracking-widest transition-all duration-300 flex items-center justify-center gap-2 hover:bg-slate-100 text-slate-300 hover:text-rose-500 text-xs"
                                            >
                                                <Trash2
                                                    size={18}
                                                    strokeWidth={2.5}
                                                />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            {/each}
                        {/if}
                    </tbody>
                </table>
            </div>

            {#if questions.links && questions.links.length > 3}
                <div
                    class="p-6 bg-slate-50/50 border-t border-slate-50 flex justify-center gap-2"
                >
                    {#each questions.links as link}
                        <Link
                            href={link.url || "#"}
                            class={`px-4 py-2 rounded-xl text-[10px] font-bold transition-all ${link.active ? "bg-slate-900 text-white shadow-lg" : "bg-white text-slate-400 hover:text-slate-900 border border-slate-100"}`}
                        >
                            {@html link.label}
                        </Link>
                    {/each}
                </div>
            {/if}
        </Card>
    </div>
</App>
