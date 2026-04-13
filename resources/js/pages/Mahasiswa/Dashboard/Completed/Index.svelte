<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Button from '@/components/ui/Button.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import EmptyState from '@/components/ui/EmptyState.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Badge from '@/components/ui/Badge.svelte';
    import { ArrowLeft, Medal, GraduationCap, Book, CheckCircle2, RotateCcw } from 'lucide-svelte';
    import { untrack } from 'svelte';
    import { CompletedState } from '@/states/Mahasiswa/MaterialState.svelte';
    import { ROUTES } from '@/utils/route';
    import type { MaterialWithStats } from '@/types';

    const { materialsWithStats = [] }: { materialsWithStats: MaterialWithStats[] } = $props();

    const state = untrack(() => new CompletedState(materialsWithStats));
</script>

<App title="Materi Selesai">
    <div class="space-y-12 pb-20">
        <PageHeader
            id="page-header"
            title="Hall of Fame"
            subtitle="Kumpulan modul pembelajaran yang telah berhasil Anda kuasai sepenuhnya."
        >
            {#snippet actions()}
                <Button href={ROUTES.MAHASISWA.DASHBOARD} variant="ghost" icon={ArrowLeft}>
                    KEMBALI
                </Button>
            {/snippet}
        </PageHeader>

        {#if state.materialsWithStats.length === 0}
            <EmptyState
                title="Belum Ada Sertifikat"
                description="Selesaikan setidaknya satu modul pembelajaran untuk melihatnya di sini."
                icon={Medal}
            >
                <Button
                    href={ROUTES.MAHASISWA.MATERIALS.INDEX}
                    variant="primary"
                    icon={GraduationCap}>MULAI BELAJAR</Button
                >
            </EmptyState>
        {:else}
            <div
                id="completed-materials-grid"
                class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3"
            >
                {#each state.materialsWithStats as { material }}
                    <Card
                        class="hover:border-primary-400 group relative overflow-hidden border-slate-100 shadow-xl transition-all"
                    >
                        <div
                            class="absolute -top-6 -right-6 flex h-24 w-24 items-center justify-center rounded-full bg-emerald-50 text-emerald-500 transition-all duration-500 group-hover:bg-emerald-500 group-hover:text-white"
                        >
                            <CheckCircle2 size={32} strokeWidth={2.5} />
                        </div>

                        <div class="space-y-6">
                            <div
                                class="text-primary-600 group-hover:bg-primary-600 flex h-12 w-12 items-center justify-center rounded-xl bg-slate-50 transition-colors group-hover:text-white"
                            >
                                <Book size={24} strokeWidth={2.5} />
                            </div>

                            <div>
                                <h4
                                    class="mb-2 text-lg leading-tight font-bold tracking-widest text-slate-900 uppercase"
                                >
                                    {material.title}
                                </h4>
                                <p
                                    class="line-clamp-2 text-xs leading-relaxed font-medium text-slate-500"
                                >
                                    {material.description || 'Deskripsi modul tidak tersedia.'}
                                </p>
                            </div>

                            <div
                                class="flex items-center justify-between border-t border-slate-50 pt-6"
                            >
                                <Badge variant="success" size="xs">MASTERED</Badge>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    href={ROUTES.MAHASISWA.MATERIALS.SHOW(material.id)}
                                    icon={RotateCcw}
                                >
                                    ULAS MATERI
                                </Button>
                            </div>
                        </div>
                    </Card>
                {/each}
            </div>
        {/if}
    </div>
</App>
