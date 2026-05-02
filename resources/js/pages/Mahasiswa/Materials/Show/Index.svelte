<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Card from '@/components/ui/Card.svelte';
    import ContentDisplay from '@/components/ui/ContentDisplay.svelte';
    import { page } from '@inertiajs/svelte';
    import { Info, Puzzle } from 'lucide-svelte';
    import { untrack } from 'svelte';
    import { MaterialShowState } from '@/states/Mahasiswa/MaterialState.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import { ROUTES } from '@/utils/route';
    import type { Material } from '@/types';

    const { material }: { material: Material } = $props();

    // Initialize State
    const from_adaptive = (page.props as any)?.flash?.from_adaptive || false;
    const state = untrack(() => new MaterialShowState(material, from_adaptive));
</script>

<App title={state.material?.title || 'Material'}>
    <div class="space-y-8">
        <PageHeader id="page-header" title={state.material?.title || material.title} />

        {#if state.material.cover_url}
            <div id="material-cover" class="relative">
                <div class="relative aspect-32/9 w-full overflow-hidden rounded-[2.5rem] border-b-8 border-slate-700 bg-slate-100 shadow-2xl">
                    <img
                        src={state.material.cover_url}
                        alt={state.material.title}
                        class="h-full w-full object-cover transition-transform duration-700 hover:scale-105"
                    />
                    <div class="absolute inset-0 bg-linear-to-t from-slate-900/40 to-transparent"></div>
                    
                    <div class="absolute bottom-10 left-10 hidden md:block">
                        <div class="flex items-center gap-4 rounded-2xl border border-white/20 bg-white/10 p-4 backdrop-blur-md">
                             <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 text-white">
                                <Puzzle size={24} />
                             </div>
                             <div>
                                <p class="text-[10px] font-black tracking-widest text-white/70 uppercase">Modul Pembelajaran</p>
                                <h3 class="text-lg font-black text-white uppercase tracking-tight">{state.material.title}</h3>
                             </div>
                        </div>
                    </div>
                </div>
                
                <!-- Decorative Elements -->
                <div class="absolute -top-4 -right-4 -z-10 h-32 w-32 rounded-full bg-primary-400/20 blur-3xl"></div>
                <div class="absolute -bottom-10 -left-10 -z-10 h-64 w-64 rounded-full bg-indigo-500/10 blur-3xl"></div>
            </div>
        {/if}

        <!-- Adaptive System Alert -->
        {#if state.from_adaptive}
            <div id="adaptive-recommendation">
                <Card class="border-primary-500 bg-primary-50 border-l-4">
                    <div class="flex items-start gap-4">
                        <div
                            class="bg-primary-100 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                        >
                            <Info size={24} class="text-primary-600" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-primary-900 mb-1 text-lg font-bold">
                                Rekomendasi Sistem Adaptif
                            </h3>
                            <p class="text-primary-700 text-sm leading-relaxed">
                                Sistem merekomendasikan Anda untuk mengulas kembali materi ini.
                                Kerjakan latihan soal untuk memperkuat pemahaman Anda.
                            </p>
                        </div>
                    </div>
                </Card>
            </div>
        {/if}

        <!-- Material Content Section (Optional) -->
        {#if state.material.content}
            <div id="material-content">
                <Card>
                    <div class="prose max-w-none">
                        <div class="leading-relaxed font-medium text-slate-600">
                            <ContentDisplay content={state.material.content} />
                        </div>
                    </div>
                </Card>
            </div>
        {/if}

        <!-- Quiz Section -->
        <div id="quiz-entry-section">
            <Card class="border-t-primary-500 border-t-4">
                <div class="flex flex-col items-center justify-between gap-8 p-6 md:flex-row">
                    <div class="space-y-8">
                        <h3 class="mb-4 text-xl font-bold tracking-widest text-slate-900 uppercase">
                            Evaluasi & Latihan
                        </h3>
                        <p class="font-medium text-slate-500">
                            Uji pemahaman Anda melalui instrumen evaluasi adaptif.
                        </p>
                    </div>
                    <Button
                        href={ROUTES.MAHASISWA.MATERIALS.QUESTIONS.LEVELS(state.material.id)}
                        variant="primary"
                        size="lg"
                        icon={Puzzle}
                        class="shadow-primary-900/20 shadow-xl"
                    >
                        MULAI EVALUASI
                    </Button>
                </div>
            </Card>
        </div>
    </div>
</App>
