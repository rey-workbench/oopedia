<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Button from '@/components/ui/Button.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import { LevelMapLegend, LevelMapCanvas, GuestBanner } from '@/components/layout';
    import { router } from '@inertiajs/svelte';
    import { ArrowLeft, Map as MapIcon } from '@lucide/svelte';
    import { untrack } from 'svelte';
    import { LevelMapState } from '@/states/Mahasiswa/QuizState.svelte';
    import { ROUTES } from '@/utils/route';
    import type { Material, LevelItem } from '@/types';

    interface Props {
        material: Material;
        levels: unknown[];
    }

    let { material, levels }: Props = $props();

    const state = untrack(() => new LevelMapState(material, levels as LevelItem[]));

    const handleLevelClick = (level: LevelItem) => {
        if (level.status === 'locked') return;

        router.visit(ROUTES.MAHASISWA.MATERIALS.QUESTIONS.SHOW(state.material.id));
    };
</script>

<App title={`Peta Tantangan - ${state.material.title}`}>
    <div class="space-y-8">
        <PageHeader
            id="page-header"
            title="Peta Tantangan"
            subtitle={`Selesaikan setiap level untuk menguasai ${state.material.title || 'modul ini'}.`}
        >
            {#snippet actions()}
                <Button
                    href={ROUTES.MAHASISWA.MATERIALS.SHOW(state.material.id)}
                    variant="ghost"
                    icon={ArrowLeft as any}
                >
                    Kembali
                </Button>
            {/snippet}
        </PageHeader>

        {#if state.isGuest}
            <GuestBanner show={state.isGuest} variant="inline" />
        {/if}

        {#if state.levels.length === 0}
            <Card class="py-24 text-center">
                <div
                    class="mx-auto mb-8 flex h-20 w-20 items-center justify-center rounded-[2rem] bg-slate-50 text-slate-300 shadow-inner"
                >
                    <MapIcon size={40} />
                </div>
                <h3 class="mb-4 text-xl font-bold tracking-widest text-slate-900 uppercase">
                    Belum Ada Level
                </h3>
                <p class="mx-auto mb-10 max-w-md font-medium text-slate-500">
                    Tim kami sedang merancang tantangan menarik.
                </p>
                <Button
                    href={ROUTES.MAHASISWA.MATERIALS.INDEX}
                    variant="primary"
                    icon={ArrowLeft as any}
                >
                    Kembali ke Katalog
                </Button>
            </Card>
        {:else}
            <div class="space-y-8">
                <div id="levels-legend">
                    <LevelMapLegend />
                </div>
                <LevelMapCanvas
                    sortedLevels={state.sortedLevels}
                    allCompleted={state.allCompleted}
                    onLevelClick={handleLevelClick}
                />
            </div>
        {/if}
    </div>
</App>
