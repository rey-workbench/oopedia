<script lang="ts">
    import App from "@/layouts/App.svelte";
        import Card from "@/components/ui/Card.svelte";
    import Button from "@/components/ui/Button.svelte";
    import GuestBanner from "@/components/shared/GuestBanner.svelte";
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
        
<div class="mb-8">
    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 leading-tight font-display">
        Peta Tantangan
    </h1>
    <div class="flex items-center gap-2 mt-3" role="presentation">
        <div class="h-1.5 w-12 bg-primary-600 rounded-full"></div>
        <div class="h-1.5 w-4 bg-slate-200 rounded-full"></div>
        <div class="h-1.5 w-2 bg-slate-100 rounded-full"></div>
    </div>
    <p class="mt-4 text-slate-500 font-medium leading-relaxed max-w-3xl">
        {`Selesaikan setiap level untuk menguasai ${state.material.title || "modul ini"}.`}
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
