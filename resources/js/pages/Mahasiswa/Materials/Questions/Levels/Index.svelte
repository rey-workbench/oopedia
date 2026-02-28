<script lang="ts">
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Card from "@/components/ui/Card.svelte";
    import Button from "@/components/ui/Button.svelte";
    import GuestBanner from "@/components/ui/GuestBanner.svelte";
    import { ArrowLeft, Map as MapIcon } from "lucide-svelte";
    import { LevelMapState } from "@/states/Mahasiswa/QuizState.svelte";
    import { ROUTES } from "@/utils/route";
    import LevelMapLegend from "@/components/quiz/LevelMapLegend.svelte";
    import LevelMapCanvas from "@/components/quiz/LevelMapCanvas.svelte";
    import type { Material } from "@/types";
    import type { LevelItem } from "@/states/Mahasiswa/QuizState.svelte";

    interface Props {
        material: Material;
        levels: unknown[];
    }

    let { material, levels }: Props = $props();

    const state = new LevelMapState(material, levels as LevelItem[]);
</script>

<App title={`Peta Tantangan - ${state.material.title}`}>
    <div class="space-y-12">
        <PageHeader
            title="Peta Tantangan"
            subtitle={`Selesaikan setiap level untuk menguasai ${state.material.title || "modul ini"}.`}
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
                    class="w-20 h-20 bg-slate-50 text-slate-300 rounded-[2rem] flex items-center justify-center mx-auto mb-8 shadow-inner"
                >
                    <MapIcon size={40} />
                </div>
                <h3
                    class="text-2xl font-bold text-slate-900 mb-4 uppercase tracking-widest"
                >
                    Belum Ada Level
                </h3>
                <p class="text-slate-500 mb-10 max-w-md mx-auto font-medium">
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
