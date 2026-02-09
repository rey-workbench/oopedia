<script>
    import App from "../../../../layouts/App.svelte";
    import PageHeader from "../../../../components/ui/PageHeader.svelte";
    import Card from "../../../../components/ui/Card.svelte";
    import Button from "../../../../components/ui/Button.svelte";
    import Badge from "../../../../components/ui/Badge.svelte";
    import { router, Link } from "@inertiajs/svelte";

    export let material;
    export let subMaterials = [];

    function handleDelete(id) {
        if (confirm("Hapus sub-materi ini?")) {
            router.delete(`/admin/materials/${material.id}/submaterials/${id}`);
        }
    }
</script>

<App title={`Kelola Sub-Materi: ${material.title}`}>
    <div class="space-y-12">
        <PageHeader
            title="Organisasi Sub-Materi"
            subtitle={`Daftar unit pembelajaran untuk modul utama: ${material.title}`}
        >
            <div slot="actions" class="flex flex-wrap items-center gap-4">
                <Button
                    href={`/admin/materials/${material.id}/submaterials/create`}
                    variant="primary"
                    icon="fas fa-plus">Tambah Sub-Materi</Button
                >
                <Button
                    href="/admin/materials"
                    variant="ghost"
                    icon="fas fa-arrow-left">KEMBALI KE MATERI</Button
                >
            </div>
        </PageHeader>

        <Card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
            <div
                slot="header"
                class="px-6 py-4 border-b border-slate-50 flex items-center justify-between"
            >
                <p
                    class="text-[10px] font-bold uppercase tracking-widest text-slate-400"
                >
                    Hierarki Pembelajaran
                </p>
                <div
                    class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-bold uppercase tracking-widest"
                >
                    {subMaterials.length} UNIT TOTAL
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr>
                            <th
                                class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50 w-20"
                                >Urutan</th
                            >
                            <th
                                class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                                >Judul Sub-Materi</th
                            >
                            <th
                                class="p-6 text-center text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                                >Jenis Konten</th
                            >
                            <th
                                class="p-6 text-right text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                                >Aksi</th
                            >
                        </tr>
                    </thead>
                    <tbody>
                        {#if subMaterials.length === 0}
                            <tr>
                                <td colspan="4" class="p-20 text-center">
                                    <div
                                        class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6"
                                    >
                                        <i
                                            class="fas fa-layer-group text-slate-200 text-3xl"
                                        ></i>
                                    </div>
                                    <h3
                                        class="text-xl font-bold uppercase tracking-widest text-slate-900 mb-2"
                                    >
                                        Belum Ada Sub-Materi
                                    </h3>
                                    <p
                                        class="text-slate-400 text-sm max-w-xs mx-auto mb-8"
                                    >
                                        Pecah materi utama Anda menjadi beberapa
                                        sub-unit yang lebih spesifik.
                                    </p>
                                    <Button
                                        href={`/admin/materials/${material.id}/submaterials/create`}
                                        variant="primary"
                                        icon="fas fa-plus"
                                        >Buat Unit Pertama</Button
                                    >
                                </td>
                            </tr>
                        {:else}
                            {#each subMaterials as sub}
                                <tr
                                    class="group hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0"
                                >
                                    <td class="px-6 py-6">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center font-bold text-xs shadow-lg shadow-slate-200"
                                        >
                                            {sub.order}
                                        </div>
                                    </td>
                                    <td class="px-6 py-6">
                                        <div
                                            class="font-bold text-slate-900 uppercase tracking-widest"
                                        >
                                            {sub.title}
                                        </div>
                                    </td>
                                    <td class="px-6 py-6 text-center">
                                        <Badge
                                            variant={sub.jenis_konten ===
                                            "teori"
                                                ? "primary"
                                                : sub.jenis_konten === "sintaks"
                                                  ? "success"
                                                  : "warning"}
                                            size="xs"
                                        >
                                            {sub.jenis_konten.toUpperCase()}
                                        </Badge>
                                    </td>
                                    <td class="px-6 py-6">
                                        <div class="flex justify-end gap-2">
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                href={`/admin/materials/${material.id}/submaterials/${sub.id}/edit`}
                                                icon="fas fa-edit"
                                            />
                                            <button
                                                on:click={() =>
                                                    handleDelete(sub.id)}
                                                class="p-2 rounded-xl font-bold uppercase tracking-widest transition-all duration-300 flex items-center justify-center gap-2 hover:bg-slate-100 text-slate-300 hover:text-rose-500 text-xs"
                                            >
                                                <i class="fas fa-trash-alt"></i>
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
