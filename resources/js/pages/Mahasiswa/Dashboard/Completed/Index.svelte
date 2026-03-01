<script lang="ts">
    import App from "@/layouts/App.svelte";
    import Button from "@/components/ui/Button.svelte";
    import EmptyState from "@/components/ui/EmptyState.svelte";
    import Card from "@/components/ui/Card.svelte";
    import Badge from "@/components/ui/Badge.svelte";
    import { ArrowLeft, Medal, GraduationCap, Book, CheckCircle2, RotateCcw } from "lucide-svelte";
    import { untrack } from 'svelte';
    import { CompletedState } from "@/states/Mahasiswa/MaterialState.svelte";
    import { ROUTES } from "@/utils/route";
    import type { MaterialWithStats } from "@/types";

    const { materialsWithStats = [] }: { materialsWithStats: MaterialWithStats[] } = $props();

    const state = untrack(() => new CompletedState(materialsWithStats));
</script>

<App title="Materi Selesai">
    <div class="space-y-12 pb-20">
        
<div class="mb-8">
    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 leading-tight font-display">
        Hall of Fame
    </h1>
    <div class="flex items-center gap-2 mt-3" role="presentation">
        <div class="h-1.5 w-12 bg-primary-600 rounded-full"></div>
        <div class="h-1.5 w-4 bg-slate-200 rounded-full"></div>
        <div class="h-1.5 w-2 bg-slate-100 rounded-full"></div>
    </div>
    <p class="mt-4 text-slate-500 font-medium leading-relaxed max-w-3xl">
        Kumpulan modul pembelajaran yang telah berhasil Anda kuasai sepenuhnya.
    </p>
    <div class="mt-6 flex flex-wrap gap-4">
        <div>
                <Button
                    href={ROUTES.MAHASISWA.DASHBOARD}
                    variant="ghost"
                    icon={ArrowLeft}>KEMBALI</Button
                >
            </div>
    </div>
</div>

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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                {#each state.materialsWithStats as { material }}
                    <Card class="hover:border-primary-400 border-slate-100 shadow-xl transition-all group relative overflow-hidden">
                        <div class="absolute -top-6 -right-6 w-24 h-24 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-500 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-500">
                            <CheckCircle2 size={32} strokeWidth={2.5} />
                        </div>

                        <div class="space-y-6">
                            <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-primary-600 transition-colors group-hover:bg-primary-600 group-hover:text-white">
                                <Book size={24} strokeWidth={2.5} />
                            </div>

                            <div>
                                <h4 class="text-lg font-bold tracking-widest text-slate-900 uppercase mb-2 leading-tight">{material.title}</h4>
                                <p class="text-xs font-medium text-slate-500 line-clamp-2 leading-relaxed">
                                    {material.description || "Deskripsi modul tidak tersedia."}
                                </p>
                            </div>

                            <div class="pt-6 border-t border-slate-50 flex items-center justify-between">
                                <Badge variant="success" size="xs">MASTERED</Badge>
                                <Button variant="ghost" size="sm" href={ROUTES.MAHASISWA.MATERIALS.SHOW(material.id)} icon={RotateCcw}>
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
