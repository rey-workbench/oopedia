<script lang="ts">
    import App from '@/layouts/App.svelte';
    import GuestBanner from '@/components/shared/GuestBanner.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Badge from '@/components/ui/Badge.svelte';
    import Button from '@/components/ui/Button.svelte';
    import ProgressBar from '@/components/ui/ProgressBar.svelte';
    import { Shapes, Users, Puzzle, Play, Lock } from 'lucide-svelte';
    import { Link } from '@inertiajs/svelte';
    import { ROUTES } from '@/utils/route';
    import { untrack } from 'svelte';
    import { QuestionListState } from '@/states/Mahasiswa/QuizState.svelte';

    import type { Material } from '@/types';

    const { materials = [] }: { materials: Material[] } = $props();

    const state = untrack(() => new QuestionListState(materials));
</script>

<App title="Latihan Soal PBO">
    <div class="space-y-12">
        <div class="mb-8">
            <h1
                class="font-display text-3xl leading-tight font-extrabold tracking-tight text-slate-900 md:text-4xl"
            >
                Latihan Soal PBO
            </h1>
            <div class="mt-3 flex items-center gap-2" role="presentation">
                <div class="bg-primary-600 h-1.5 w-12 rounded-full"></div>
                <div class="h-1.5 w-4 rounded-full bg-slate-200"></div>
                <div class="h-1.5 w-2 rounded-full bg-slate-100"></div>
            </div>
            <p class="mt-4 max-w-3xl leading-relaxed font-medium text-slate-500">
                Uji pemahaman Anda dengan mengerjakan latihan soal untuk setiap materi
            </p>
        </div>

        {#if state.isGuest}
            <GuestBanner
                show={state.isGuest}
                variant="banner"
                title="Mode Tamu Aktif!"
                message="Anda hanya dapat melihat sebagian materi dan hanya 3 soal latihan dari setiap tingkat kesulitan. Untuk akses penuh, silakan login atau daftar."
            />
        {/if}

        <div class="grid grid-cols-1 gap-10">
            {#each state.materials as material (material.id)}
                <Card padding="p-0" hover={true} class="overflow-hidden">
                    <Link
                        href={ROUTES.MAHASISWA.MATERIALS.QUESTIONS.LEVELS(material.id)}
                        class="flex h-full flex-col md:flex-row"
                    >
                        <!-- Graphic Section -->
                        <div class="relative shrink-0 md:w-72 lg:w-96">
                            {#if material.media && material.media.length > 0}
                                <div class="h-60 md:h-full">
                                    <img
                                        src={material.media[0]?.full_url}
                                        alt={material.title}
                                        class="h-full w-full object-cover"
                                    />
                                    <div
                                        class="absolute inset-0 bg-slate-900/10 transition-colors group-hover:bg-transparent"
                                    ></div>
                                </div>
                            {:else}
                                <div
                                    class="bg-primary-600 flex h-60 items-center justify-center md:h-full"
                                >
                                    <Shapes
                                        size={96}
                                        class="text-white/10 transition-transform group-hover:rotate-6"
                                    />
                                </div>
                            {/if}
                            <div class="absolute top-6 left-6">
                                <Badge variant="primary" size="sm" class="shadow-xl"
                                    >MODUL AKTIF</Badge
                                >
                            </div>
                        </div>

                        <!-- Content Section -->
                        <div class="flex flex-1 flex-col justify-between p-10">
                            <div>
                                <div class="flex items-start justify-between gap-6">
                                    <div>
                                        <h2
                                            class="group-hover:text-primary-600 mb-3 text-3xl leading-tight font-bold tracking-widest text-slate-900 transition-colors"
                                        >
                                            {material.title}
                                        </h2>
                                        <div class="flex flex-wrap items-center gap-6">
                                            <div class="flex items-center gap-2.5">
                                                <div
                                                    class="bg-primary-50 text-primary-600 flex h-8 w-8 items-center justify-center rounded-xl text-xs shadow-inner"
                                                >
                                                    <Users size={14} />
                                                </div>
                                                <span
                                                    class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                                    >{material.student_count || 0} Mahasiswa</span
                                                >
                                            </div>
                                            <div class="flex items-center gap-2.5">
                                                <div
                                                    class="bg-primary-50 text-primary-600 flex h-8 w-8 items-center justify-center rounded-xl text-xs shadow-inner"
                                                >
                                                    <Puzzle size={14} />
                                                </div>
                                                <span
                                                    class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                                    >{material.total_questions || 0} Soal Latihan</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="group-hover:bg-primary-600 hidden h-14 w-14 items-center justify-center rounded-2xl bg-slate-50 text-slate-900 shadow-inner transition-all group-hover:text-white sm:flex"
                                    >
                                        <Play size={20} class="ml-1 fill-current" />
                                    </div>
                                </div>

                                {#if state.isGuest}
                                    <div
                                        class="mt-8 flex items-center gap-6 rounded-[2rem] border border-amber-100 bg-amber-50 p-6 ring-8 ring-amber-50/50"
                                    >
                                        <div
                                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-500 text-white shadow-lg shadow-amber-200"
                                        >
                                            <Lock size={20} />
                                        </div>
                                        <div>
                                            <span
                                                class="mb-1 block text-[10px] font-bold tracking-widest text-amber-800 uppercase"
                                                >Akses Terbatas</span
                                            >
                                            <p class="text-xs font-medium text-amber-700">
                                                Selesaikan pendaftaran untuk membuka semua level
                                                soal.
                                            </p>
                                        </div>
                                    </div>
                                {:else}
                                    <div class="mt-8 space-y-4">
                                        <div class="flex items-center justify-between px-1">
                                            <div class="flex items-center gap-3">
                                                <span
                                                    class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                                    >Progress Mastery</span
                                                >
                                                <Badge variant="success" size="xs"
                                                    >{material.progress_percentage || 0}%</Badge
                                                >
                                            </div>
                                            <span
                                                class="text-[10px] font-bold text-slate-300 uppercase"
                                                >{material.completed_questions ||
                                                    0}/{material.total_questions || 0} SOAL</span
                                            >
                                        </div>
                                        <ProgressBar
                                            value={material.progress_percentage || 0}
                                            height="h-2"
                                            color="emerald"
                                        />
                                    </div>
                                {/if}
                            </div>

                            <div class="mt-10 md:hidden">
                                <Button
                                    variant="primary"
                                    class="shadow-primary-900/20 w-full shadow-lg"
                                    icon={Play}>Mulai Latihan</Button
                                >
                            </div>
                        </div>
                    </Link>
                </Card>
            {/each}
        </div>
    </div>
</App>
