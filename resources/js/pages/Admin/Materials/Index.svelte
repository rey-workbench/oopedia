<script lang="ts">
    import App from '@/layouts/App.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Card from '@/components/ui/Card.svelte';
    import DataTable from '@/components/ui/DataTable.svelte';
    import EmptyState from '@/components/ui/EmptyState.svelte';
    import UserAvatar from '@/components/ui/UserAvatar.svelte';
    import { untrack } from 'svelte';
    import { MaterialListState } from '@/states/Admin/MaterialState.svelte';
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
    } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';

    let { materials = [] }: { materials: any[] } = $props();

    const totalMaterials = $derived(materials.length);
    const recentMaterials = $derived(
        materials.filter((m) => {
            const date = new Date(m.created_at);
            const thirtyDaysAgo = new Date();
            thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
            return date >= thirtyDaysAgo;
        }).length
    );
    const totalMedia = $derived(
        materials.reduce((acc: number, m: any) => acc + (m.media ? m.media.length : 0), 0)
    );

    let search = new URLSearchParams(window.location.search).get('search') || '';
    const listState = untrack(() => new MaterialListState(materials, search));

    const columns = $derived([
        { key: 'visual', label: 'Pratinjau Visual', align: 'left' },
        { key: 'identity', label: 'Identitas Modul', align: 'left' },
        { key: 'author', label: 'Penulis Utama', align: 'left' },
        { key: 'sync', label: 'Sinkronisasi Awal', align: 'center' },
        { key: 'actions', label: 'Operasi', align: 'right' },
    ]);

    const materialStats = $derived([
        {
            title: 'Total Modul',
            value: totalMaterials,
            icon: Layers,
            variant: 'primary',
            footer: 'Modul instruksional aktif',
        },
        {
            title: 'Materi Baru',
            value: recentMaterials,
            icon: CalendarCheck,
            variant: 'success',
            footer: 'Penambahan 30 hari terakhir',
        },
        {
            title: 'Korpus Media',
            value: totalMedia,
            icon: Video,
            variant: 'primary',
            footer: 'Total aset multimedia',
        },
    ]);
</script>

<App title="Kelola Materi">
    <div class="space-y-12">
        <PageHeader
            title="Kurikulum Materi"
            subtitle="Otoritas manajemen konten dan modul pembelajaran Pemrograman Berorientasi Objek."
        >
            {#snippet actions()}
                <Button href={ROUTES.ADMIN.MATERIALS.CREATE} variant="primary" icon={Plus}
                    >Tambah Modul Baru</Button
                >
            {/snippet}
        </PageHeader>

        <!-- Statistics -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            {#each materialStats as stat}
                <Card hover={true} class="group relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 text-slate-400 opacity-10">
                        {#if typeof stat.icon !== 'string'}
                            {@const IconComponent = stat.icon}
                            <div
                                class="scale-[4] transition-transform duration-500 group-hover:scale-[4.5]"
                            >
                                <IconComponent size={24} strokeWidth={2.5} />
                            </div>
                        {/if}
                    </div>

                    <div class="relative z-10">
                        <div
                            class="glass mb-6 flex h-14 w-14 items-center justify-center rounded-2xl shadow-sm
                            {stat.variant === 'success'
                                ? 'bg-emerald-100 text-emerald-600'
                                : 'bg-primary-100 text-primary-600'}"
                        >
                            {#if typeof stat.icon === 'string'}
                                <i class={stat.icon}></i>
                            {:else}
                                {@const IconComponent = stat.icon}
                                <IconComponent size={24} strokeWidth={2.5} />
                            {/if}
                        </div>

                        <h3
                            class="mb-2 text-[10px] font-bold tracking-wider text-slate-600 uppercase"
                        >
                            {stat.title}
                        </h3>
                        <div
                            class="font-display mb-2 text-4xl font-black tracking-tight text-slate-900"
                        >
                            {stat.value}
                        </div>

                        {#if stat.footer}
                            <div class="flex items-center gap-2">
                                <div
                                    class="h-1.5 w-1.5 rounded-full {stat.variant === 'success'
                                        ? 'bg-emerald-500'
                                        : 'bg-primary-500'}"
                                ></div>
                                <p
                                    class="text-[10px] font-bold tracking-widest text-slate-500 uppercase"
                                >
                                    {stat.footer}
                                </p>
                            </div>
                        {/if}
                    </div>
                </Card>
            {/each}
        </div>

        <!-- Material List -->
        <DataTable
            title="Inventaris Konten"
            items={listState.materials}
            bind:search={listState.search}
            onsearch={() => listState.handleSearch()}
            searchPlaceholder="Pindai materi..."
            {columns}
        >
            {#snippet empty()}
                <EmptyState
                    title="Kurikulum Kosong"
                    description="Basis data materi instruksional kosong. Lakukan injeksi modul baru untuk memulai siklus pembelajaran."
                >
                    <Button href={ROUTES.ADMIN.MATERIALS.CREATE} variant="primary" icon={Plus}
                        >Inisialisasi Kurikulum</Button
                    >
                </EmptyState>
            {/snippet}

            {#snippet row(material)}
                <td class="group-hover:border-primary-600 border-l-4 border-transparent px-6 py-6">
                    {#if material.media && material.media.length > 0}
                        <div
                            class="h-14 w-20 overflow-hidden rounded-xl shadow-lg shadow-slate-200 transition-transform group-hover:scale-105"
                        >
                            <img
                                src={`/${material.media[0].media_url}`}
                                alt={material.title}
                                class="h-full w-full object-cover"
                            />
                        </div>
                    {:else}
                        <div
                            class="flex h-14 w-20 items-center justify-center rounded-xl border border-dashed border-slate-200 bg-slate-100 text-slate-300"
                        >
                            <FileText size={24} strokeWidth={2} class="opacity-30" />
                        </div>
                    {/if}
                </td>

                <td class="group-hover:border-primary-600 border-l-4 border-transparent px-6 py-6">
                    <div>
                        <div class="mb-1 font-bold tracking-widest text-slate-900">
                            {material.title}
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span
                                class="bg-primary-50 text-primary-600 rounded-full px-2 py-0.5 text-[9px] font-bold tracking-widest uppercase"
                            >
                                MOD-{String(material.id).padStart(3, '0')}
                            </span>
                            <span
                                class="rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-bold tracking-widest text-emerald-600 uppercase"
                            >
                                {material.sub_materials ? material.sub_materials.length : 0} SUB-MATERI
                            </span>
                            <p class="line-clamp-1 max-w-sm text-[10px] font-medium text-slate-400">
                                {material.content.replace(/<[^>]*>?/gm, '').substring(0, 60)}...
                            </p>
                        </div>
                    </div>
                </td>

                <td class="px-6 py-6">
                    <div class="flex items-center gap-3">
                        <UserAvatar name={material.creator?.name ?? 'S'} size="sm" />
                        <span
                            class="text-[11px] font-bold tracking-widest text-slate-600 uppercase"
                        >
                            {material.creator?.name || 'System Admin'}
                        </span>
                    </div>
                </td>

                <td class="px-6 py-6 text-center">
                    <span class="text-[10px] font-bold tracking-widest text-slate-400 uppercase">
                        {new Date(material.created_at).toLocaleDateString('id-ID')}
                    </span>
                </td>

                <td class="px-6 py-6">
                    <div class="flex justify-end gap-3">
                        <Button
                            variant="ghost"
                            size="sm"
                            href={ROUTES.ADMIN.MATERIALS.SUBMATERIALS.INDEX(material.id)}
                            icon={List}
                            class="text-emerald-500 hover:text-emerald-600"
                            title="Kelola Sub-materi"
                        />
                        <Button
                            variant="ghost"
                            size="sm"
                            href={ROUTES.ADMIN.MATERIALS.QUESTIONS.INDEX(material.id)}
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
