<script lang="ts"> 
    import App from '@/layouts/App.svelte';
    import Button from '@/components/ui/Button.svelte';
    import DataTable from '@/components/ui/DataTable.svelte';
    import EmptyState from '@/components/ui/EmptyState.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import Badge from '@/components/ui/Badge.svelte';
    import { Plus, ArrowLeft, Layers, Edit2, Trash2 } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';
    import { untrack } from 'svelte';
    import { SubmaterialListState } from '@/states/Admin/MaterialState.svelte';

    let { material, subMaterials = [] }: { material: any; subMaterials: any[] } = $props();

    const state = untrack(() => new SubmaterialListState(material, subMaterials));

    const columns = $derived([
        { key: 'order', label: 'Urutan', align: 'left' },
        { key: 'title', label: 'Judul Sub-Materi', align: 'left' },
        { key: 'jenis_konten', label: 'Jenis Konten', align: 'center' },
        { key: 'actions', label: 'Aksi', align: 'right' },
    ]);
</script>

<App title={`Kelola Sub-Materi: ${material.title}`}>
    <div class="space-y-12">
        <PageHeader
            title="Organisasi Sub-Materi"
            subtitle={`Daftar unit pembelajaran untuk modul utama: ${material.title}`}
        >
            {#snippet actions()}
                <Button
                    id="add-submaterial-btn"
                    href={ROUTES.ADMIN.MATERIALS.SUBMATERIALS.CREATE(material.id)}
                    variant="primary"
                    icon={Plus}>Tambah Sub-Materi</Button
                >
                <Button href={ROUTES.ADMIN.MATERIALS.INDEX} variant="ghost" icon={ArrowLeft}
                    >KEMBALI KE MATERI</Button
                >
            {/snippet}
        </PageHeader>

        <DataTable
            id="submaterial-hierarchy-table"
            title="Hierarki Pembelajaran"
            items={state.subMaterials}
            hideSearch={true}
            {columns}
        >
            {#snippet empty()}
                <EmptyState
                    title="Belum Ada Sub-Materi"
                    description="Pecah materi utama Anda menjadi beberapa sub-unit yang lebih spesifik."
                    icon={Layers}
                >
                    <div class="flex justify-center gap-4">
                        <Button
                            href={ROUTES.ADMIN.MATERIALS.SUBMATERIALS.CREATE(state.material!.id)}
                            variant="primary"
                            icon={Plus}>Buat Unit Pertama</Button
                        >
                    </div>
                </EmptyState>
            {/snippet}

            {#snippet row(sub)}
                <td class="group-hover:border-primary-600 border-l-4 border-transparent px-6 py-6">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-900 text-xs font-bold text-white shadow-lg shadow-slate-200"
                    >
                        {sub.order}
                    </div>
                </td>
                <td class="px-6 py-6">
                    <div class="font-bold tracking-widest text-slate-900 uppercase">
                        {sub.title}
                    </div>
                </td>
                <td class="px-6 py-6 text-center">
                    <Badge
                        variant={sub.jenis_konten === 'teori'
                            ? 'primary'
                            : sub.jenis_konten === 'sintaks'
                              ? 'success'
                              : 'warning'}
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
                            href={ROUTES.ADMIN.MATERIALS.SUBMATERIALS.EDIT(
                                state.material!.id,
                                sub.id
                            )}
                            icon={Edit2}
                        />
                        <Button
                            variant="ghost"
                            size="sm"
                            onclick={() => state.handleDelete(sub.id)}
                            icon={Trash2}
                            class="text-slate-300 hover:text-rose-500"
                        />
                    </div>
                </td>
            {/snippet}
        </DataTable>
    </div>
</App>
