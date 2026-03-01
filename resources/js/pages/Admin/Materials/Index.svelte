<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/shared/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import StatsGrid from "@/components/shared/StatsGrid.svelte";
    import DataTable from "@/components/shared/DataTable.svelte";
    import EmptyState from "@/components/ui/EmptyState.svelte";
    import UserAvatar from "@/components/ui/UserAvatar.svelte";
    import { MaterialListState } from "@/states/Admin/MaterialState.svelte";
    import {
        Plus,
        Layers,
        CalendarCheck,
        Video,
        FileText,
        List,
        FlaskConical,
        Edit2,
        Trash2,
    } from "lucide-svelte";
    import { ROUTES } from "@/utils/route";

    export let materials = [];
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

    let search =
        new URLSearchParams(window.location.search).get("search") || "";
    const listState = new MaterialListState(materials, search);

    $: columns = [
        { key: "visual", label: "Pratinjau Visual", align: "left" },
        { key: "identity", label: "Identitas Modul", align: "left" },
        { key: "author", label: "Penulis Utama", align: "left" },
        { key: "sync", label: "Sinkronisasi Awal", align: "center" },
        { key: "actions", label: "Operasi", align: "right" },
    ];

    $: materialStats = [
        {
            title: "Total Modul",
            value: totalMaterials,
            icon: Layers,
            variant: "primary",
            footer: "Modul instruksional aktif",
        },
        {
            title: "Materi Baru",
            value: recentMaterials,
            icon: CalendarCheck,
            variant: "success",
            footer: "Penambahan 30 hari terakhir",
        },
        {
            title: "Korpus Media",
            value: totalMedia,
            icon: Video,
            variant: "primary",
            footer: "Total aset multimedia",
        },
    ];
</script>

<App title="Kelola Materi">
    <div class="space-y-12">
        <PageHeader
            title="Kurikulum Materi"
            subtitle="Otoritas manajemen konten dan modul pembelajaran Pemrograman Berorientasi Objek."
        >
            {#snippet actions()}
                <Button
                    href={ROUTES.ADMIN.MATERIALS.CREATE}
                    variant="primary"
                    icon={Plus}>Tambah Modul Baru</Button
                >
            {/snippet}
        </PageHeader>

        <!-- Statistics -->
        <StatsGrid
            stats={materialStats}
            gridClass="grid-cols-1 md:grid-cols-3"
        />

        <!-- Material List -->
        <DataTable
            title="Inventaris Konten"
            items={listState.materials}
            bind:search={listState.search}
            onSearch={() => listState.handleSearch()}
            searchPlaceholder="Pindai materi..."
            {columns}
        >
            {#snippet empty()}
                <EmptyState
                    title="Kurikulum Kosong"
                    description="Basis data materi instruksional kosong. Lakukan injeksi modul baru untuk memulai siklus pembelajaran."
                >
                    <Button
                        href={ROUTES.ADMIN.MATERIALS.CREATE}
                        variant="primary"
                        icon={Plus}>Inisialisasi Kurikulum</Button
                    >
                </EmptyState>
            {/snippet}

            {#snippet row(material)}
                <td
                    class="px-6 py-6 border-l-4 border-transparent group-hover:border-primary-600"
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
                    class="px-6 py-6 border-l-4 border-transparent group-hover:border-primary-600"
                >
                    <div>
                        <div
                            class="font-bold text-slate-900 tracking-widest mb-1"
                        >
                            {material.title}
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span
                                class="text-[9px] font-bold bg-primary-50 text-primary-600 px-2 py-0.5 rounded-full uppercase tracking-widest"
                            >
                                MOD-{String(material.id).padStart(3, "0")}
                            </span>
                            <span
                                class="text-[9px] font-bold bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded-full uppercase tracking-widest"
                            >
                                {material.sub_materials
                                    ? material.sub_materials.length
                                    : 0} SUB-MATERI
                            </span>
                            <p
                                class="text-[10px] font-medium text-slate-400 line-clamp-1 max-w-sm"
                            >
                                {material.content
                                    .replace(/<[^>]*>?/gm, "")
                                    .substring(0, 60)}...
                            </p>
                        </div>
                    </div>
                </td>

                <td class="px-6 py-6">
                    <div class="flex items-center gap-3">
                        <UserAvatar
                            name={material.creator?.name ?? "S"}
                            size="sm"
                        />
                        <span
                            class="text-[11px] font-bold text-slate-600 uppercase tracking-widest"
                        >
                            {material.creator?.name || "System Admin"}
                        </span>
                    </div>
                </td>

                <td class="px-6 py-6 text-center">
                    <span
                        class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                    >
                        {new Date(material.created_at).toLocaleDateString(
                            "id-ID",
                        )}
                    </span>
                </td>

                <td class="px-6 py-6">
                    <div class="flex justify-end gap-3">
                        <Button
                            variant="ghost"
                            size="sm"
                            href={ROUTES.ADMIN.MATERIALS.SUBMATERIALS.INDEX(
                                material.id,
                            )}
                            icon={List}
                            class="text-emerald-500 hover:text-emerald-600"
                            title="Kelola Sub-materi"
                        />
                        <Button
                            variant="ghost"
                            size="sm"
                            href={ROUTES.ADMIN.MATERIALS.QUESTIONS.INDEX(
                                material.id,
                            )}
                            icon={FlaskConical}
                            class="text-primary-500 hover:text-primary-600"
                            title="Kelola Soal"
                        />
                        <Button
                            variant="ghost"
                            size="sm"
                            href={ROUTES.ADMIN.MATERIALS.EDIT(material.id)}
                            icon={Edit2}
                        />
                        <Button
                            variant="ghost"
                            size="sm"
                            onclick={() => listState.handleDelete(material.id)}
                            icon={Trash2}
                            class="text-slate-300 hover:text-rose-500"
                        />
                    </div>
                </td>
            {/snippet}
        </DataTable>
    </div>
</App>
