<script>
    import Card from "@/components/ui/Card.svelte";
    import Button from "@/components/ui/Button.svelte";
    import Badge from "@/components/ui/Badge.svelte";
    import { Layers, Plus, Edit2, Trash2 } from "lucide-svelte";
    import { SubmaterialListState } from "@/states/Admin/MaterialState.svelte";

    export let material;
    export let subMaterials = [];

    const state = new SubmaterialListState(material, subMaterials);
</script>

<Card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
    <div slot="header" class="flex items-center justify-between">
        <p
            class="text-[10px] font-bold uppercase tracking-widest text-slate-400"
        >
            Hierarki Pembelajaran
        </p>
        <div
            class="px-3 py-1 bg-primary-50 text-primary-600 rounded-full text-[10px] font-bold uppercase tracking-widest"
        >
            {state.subMaterials.length} UNIT TOTAL
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
                {#if state.subMaterials.length === 0}
                    <tr>
                        <td colspan="4" class="p-20 text-center">
                            <div
                                class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6"
                            >
                                <Layers
                                    size={32}
                                    strokeWidth={1.5}
                                    class="text-slate-200"
                                />
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
                                href={`/admin/materials/${state.material.id}/submaterials/create`}
                                variant="primary"
                                icon={Plus}>Buat Unit Pertama</Button
                            >
                        </td>
                    </tr>
                {:else}
                    {#each state.subMaterials as sub}
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
                                    variant={sub.jenis_konten === "teori"
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
                                        href={`/admin/materials/${state.material.id}/submaterials/${sub.id}/edit`}
                                        icon={Edit2}
                                    />
                                    <button
                                        on:click={() =>
                                            state.handleDelete(sub.id)}
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
</Card>
