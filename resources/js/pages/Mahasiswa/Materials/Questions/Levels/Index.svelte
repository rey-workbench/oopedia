<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Button from '@/components/ui/Button.svelte';
    import GuestBanner from '@/components/layout/GuestBanner.svelte';
    import { ArrowLeft, Map as MapIcon } from 'lucide-svelte';
    import { untrack } from 'svelte';
    import { LevelMapState } from '@/states/Mahasiswa/QuizState.svelte';
    import { ROUTES } from '@/utils/route';
    import LevelMapLegend from '@/components/layout/LevelMapLegend.svelte';
    import LevelMapCanvas from '@/components/layout/LevelMapCanvas.svelte';
    import type { Material } from '@/types';
    import type { LevelItem } from '@/states/Mahasiswa/QuizState.svelte';

    interface Props {
        material: Material;
        levels: unknown[];
    }

    let { material, levels }: Props = $props();

    const state = untrack(() => new LevelMapState(material, levels as LevelItem[]));
</script>

<App title={`Peta Tantangan - ${state.material.title}`}>
    <div class="space-y-12">
        <div class="mb-8">
            <h1
                class="font-display text-3xl leading-tight font-extrabold tracking-tight text-slate-900 md:text-4xl"
            >
                Peta Tantangan
            </h1>
            <div class="mt-3 flex items-center gap-2" role="presentation">
                <div class="bg-primary-600 h-1.5 w-12 rounded-full"></div>
                <div class="h-1.5 w-4 rounded-full bg-slate-200"></div>
                <div class="h-1.5 w-2 rounded-full bg-slate-100"></div>
            </div>
            <p class="mt-4 max-w-3xl leading-relaxed font-medium text-slate-500">
                {`Selesaikan setiap level untuk menguasai ${state.material.title || 'modul ini'}.`}
            </p>
            <div class="mt-6 flex flex-wrap gap-4">
                <Button
                    href={ROUTES.MAHASISWA.MATERIALS.SHOW(state.material.id)}
                    variant="ghost"
                    icon={ArrowLeft as any}
                >
                    Kembali
                </Button>
            </div>
        </div>

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
                <h3 class="mb-4 text-2xl font-bold tracking-widest text-slate-900 uppercase">
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
            <div class="space-y-10">
                <LevelMapLegend />
                <LevelMapCanvas
                    material={state.material}
                    sortedLevels={state.sortedLevels}
                    allCompleted={state.allCompleted}
                />
            </div>
        {/if}
    </div>
</App>
