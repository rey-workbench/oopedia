<script>
    import App from "../../../layouts/App.svelte";
    import PageHeader from "../../../components/ui/PageHeader.svelte";
    import StatCard from "../../../components/ui/StatCard.svelte";
    import Card from "../../../components/ui/Card.svelte";
    import Button from "../../../components/ui/Button.svelte";
    import Badge from "../../../components/ui/Badge.svelte";
    import { Link, router } from "@inertiajs/svelte";
    import { confirmDelete } from "../../../utils/confirmDelete";
    import { onMount } from "svelte";
    import {
        Plus,
        Layers,
        CalendarCheck,
        Video,
        Search,
        Inbox,
        FileText,
        List,
        FlaskConical,
        Edit2,
        Trash2,
    } from "lucide-svelte";

    export let materials = []; // The original blade passed a collection, Inertia will serialize it to array
    // If paginated, structure will differ. Assuming getAllMaterials returns a collection or simple array based on controller.
    // Controller: $materials = $this->materialService->getAllMaterials($search, $sort, $direction);
    // It seems to be a Collection.

    // Also we need search param
    let search =
        new URLSearchParams(window.location.search).get("search") || "";

    function handleSearch() {
        router.get(
            "/admin/materials",
            { search },
            { preserveState: true, replace: true },
        );
    }

    function handleDelete(id) {
        confirmDelete(
            `/admin/materials/${id}`,
            "Hapus materi ini secara permanen dari basis data?",
        );
    }

    // Derive stats
    $: totalMaterials = materials.length;
    $: recentMaterials = materials.filter((m) => {
        const date = new Date(m.created_at);
        const thirtyDaysAgo = new Date();
        thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
        return date >= thirtyDaysAgo;
    }).length;
    $: totalMedia = materials.reduce(
        (acc, m) => acc + (m.media ? m.media.length : 0),
        0,
    );
</script>

<App title="Kelola Materi">
    <div class="space-y-12">
        <PageHeader
            title="Kurikulum Materi"
            subtitle="Otoritas manajemen konten dan modul pembelajaran Pemrograman Berorientasi Objek."
        >
            <div slot="actions">
                <Button
                    href="/admin/materials/create"
                    variant="primary"
                    icon={Plus}>Tambah Modul Baru</Button
                >
            </div>
        </PageHeader>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <StatCard
                title="Total Modul"
                value={totalMaterials}
                icon={Layers}
                variant="primary"
                footer="Modul instruksional aktif"
            />
            <StatCard
                title="Materi Baru"
                value={recentMaterials}
                icon={CalendarCheck}
                variant="success"
                footer="Penambahan 30 hari terakhir"
            />
            <StatCard
                title="Korpus Media"
                value={totalMedia}
                icon={Video}
                variant="indigo"
                footer="Total aset multimedia"
            />
        </div>

        <Card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
            <div
                class="flex flex-col md:flex-row justify-between items-center gap-6 w-full px-6 py-4 border-b border-slate-50"
            >
                <p
                    class="text-[10px] font-bold uppercase tracking-widest text-slate-400"
                >
                    Inventaris Konten
                </p>
                <div class="w-full md:w-auto">
                    <div class="relative group">
                        <Search
                            size={18}
                            strokeWidth={2.5}
                            class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition-colors"
                        />
                        <input
                            type="text"
                            bind:value={search}
                            on:input={handleSearch}
                            placeholder="Pindai materi..."
                            class="w-full md:w-64 pl-12 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-100 focus:border-blue-600 transition-all outline-none"
                        />
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr>
                            <th
                                class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                                >Pratinjau Visual</th
                            >
                            <th
                                class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                                >Identitas Modul</th
                            >
                            <th
                                class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                                >Penulis Utama</th
                            >
                            <th
                                class="p-6 text-center text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                                >Sinkronisasi Awal</th
                            >
                            <th
                                class="p-6 text-right text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                                >Operasi</th
                            >
                        </tr>
                    </thead>
                    <tbody>
                        {#if materials.length === 0}
                            <tr>
                                <td colspan="5" class="p-24 text-center">
                                    <div
                                        class="w-24 h-24 bg-slate-50 rounded-[2.5rem] flex items-center justify-center mx-auto mb-8 shadow-inner"
                                    >
                                        <Inbox
                                            size={48}
                                            strokeWidth={1.5}
                                            class="text-slate-200"
                                        />
                                    </div>
                                    <h3
                                        class="text-2xl font-bold tracking-widest text-slate-900 mb-2"
                                    >
                                        Kurikulum Kosong
                                    </h3>
                                    <p
                                        class="text-slate-400 text-sm max-w-xs mx-auto mb-8"
                                    >
                                        Basis data materi instruksional kosong.
                                        Lakukan injeksi modul baru untuk memulai
                                        siklus pembelajaran.
                                    </p>
                                    <Button
                                        href="/admin/materials/create"
                                        variant="primary"
                                        icon={Plus}
                                        >Inisialisasi Kurikulum</Button
                                    >
                                </td>
                            </tr>
                        {:else}
                            {#each materials as material (material.id)}
                                <tr
                                    class="group hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0"
                                >
                                    <td
                                        class="px-6 py-6 border-l-4 border-transparent group-hover:border-blue-600"
                                    >
                                        {#if material.media && material.media.length > 0}
                                            <div
                                                class="w-20 h-14 rounded-xl overflow-hidden shadow-lg shadow-slate-200 group-hover:scale-105 transition-transform"
                                            >
                                                <img
                                                    src={`/${material.media[0].media_url}`}
                                                    alt={material.title}
                                                    class="w-full h-full object-cover"
                                                />
                                            </div>
                                        {:else}
                                            <div
                                                class="w-20 h-14 rounded-xl bg-slate-100 flex items-center justify-center text-slate-300 border border-dashed border-slate-200"
                                            >
                                                <FileText
                                                    size={24}
                                                    strokeWidth={2}
                                                    class="opacity-30"
                                                />
                                            </div>
                                        {/if}
                                    </td>
                                    <td
                                        class="px-6 py-6 border-l-4 border-transparent group-hover:border-blue-600"
                                    >
                                        <div>
                                            <div
                                                class="font-bold text-slate-900 tracking-widest mb-1"
                                            >
                                                {material.title}
                                            </div>
                                            <div
                                                class="flex items-center gap-2 flex-wrap"
                                            >
                                                <span
                                                    class="text-[9px] font-bold bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full uppercase tracking-widest"
                                                    >MOD-{String(
                                                        material.id,
                                                    ).padStart(3, "0")}</span
                                                >
                                                <span
                                                    class="text-[9px] font-bold bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded-full uppercase tracking-widest"
                                                    >{material.sub_materials
                                                        ? material.sub_materials
                                                              .length
                                                        : 0} SUB-MATERI</span
                                                >
                                                <p
                                                    class="text-[10px] font-medium text-slate-400 line-clamp-1 max-w-sm"
                                                >
                                                    {material.content
                                                        .replace(
                                                            /<[^>]*>?/gm,
                                                            "",
                                                        )
                                                        .substring(0, 60)}...
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center text-[10px] font-bold shadow-lg shadow-slate-200"
                                            >
                                                {(
                                                    material.creator?.name ||
                                                    "S"
                                                ).charAt(0)}
                                            </div>
                                            <span
                                                class="text-[11px] font-bold text-slate-600 uppercase tracking-widest"
                                                >{material.creator?.name ||
                                                    "System Admin"}</span
                                            >
                                        </div>
                                    </td>
                                    <td class="px-6 py-6 text-center">
                                        <span
                                            class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                                        >
                                            {new Date(
                                                material.created_at,
                                            ).toLocaleDateString("id-ID")}
                                        </span>
                                    </td>
                                    <td class="px-6 py-6">
                                        <div class="flex justify-end gap-3">
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                href={`/admin/materials/${material.id}/submaterials`}
                                                icon={List}
                                                class="text-emerald-500 hover:text-emerald-600"
                                                title="Kelola Sub-materi"
                                            />
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                href={`/admin/materials/${material.id}/questions`}
                                                icon={FlaskConical}
                                                class="text-indigo-500 hover:text-indigo-600"
                                                title="Kelola Soal"
                                            />
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                href={`/admin/materials/${material.id}/edit`}
                                                icon={Edit2}
                                            />
                                            <button
                                                on:click={() =>
                                                    handleDelete(material.id)}
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
        </Card>
    </div>
</App>
