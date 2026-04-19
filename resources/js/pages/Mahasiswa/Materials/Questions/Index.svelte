<script lang="ts">
    import App from '@/layouts/App.svelte';
    import GuestBanner from '@/components/layout/GuestBanner.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Badge from '@/components/ui/Badge.svelte';
    import Button from '@/components/ui/Button.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
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

<App title="Latihan soal PBO">
    <div class="space-y-12">
        <PageHeader
            id="page-header"
            title="Latihan soal PBO"
            subtitle="Uji pemahaman Anda dengan mengerjakan latihan soal untuk setiap materi"
        />

        {#if state.isGuest}
            <GuestBanner
                show={state.isGuest}
                variant="banner"
                title="Mode Tamu Aktif!"
                message="Anda hanya dapat melihat sebagian materi dan hanya 3 soal latihan dari setiap tingkat kesulitan. Untuk akses penuh, silakan login atau daftar."
            />
        {/if}

        <div id="module-list" class="grid grid-cols-1 gap-10">
            {#each state.materials as material (material.id)}
                {@const isLocked = !state.isGuest && !!material.is_locked}

                <Card
                    id="quiz-card-{material.id}"
                    padding="p-0"
                    hover={!isLocked}
                    class="group overflow-hidden {isLocked ? 'opacity-70 grayscale' : ''}"
                >
                    <!-- ── Graphic + Content (shared markup) ─────────────────── -->
                    {#snippet cardInner()}
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
                                {#if isLocked}
                                    <Badge variant="warning" size="sm" class="shadow-xl"
                                        >TERKUNCI</Badge
                                    >
                                {:else}
                                    <Badge variant="primary" size="sm" class="shadow-xl"
                                        >MODUL AKTIF</Badge
                                    >
                                {/if}
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
                                        class="{isLocked
                                            ? 'bg-slate-100 text-slate-400'
                                            : 'group-hover:bg-primary-600 bg-slate-50 text-slate-900 group-hover:text-white group-active:scale-95'} hidden h-14 w-14 shrink-0 items-center justify-center rounded-2xl shadow-inner transition-all duration-150 sm:flex"
                                    >
                                        {#if isLocked}
                                            <Lock size={20} />
                                        {:else}
                                            <Play size={20} class="ml-1 fill-current" />
                                        {/if}
                                    </div>
                                </div>

                                <!-- Status banners -->
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
                                {:else if isLocked}
                                    <div
                                        class="mt-8 flex items-center gap-6 rounded-[2rem] border border-slate-200 bg-slate-100 p-6"
                                    >
                                        <div
                                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-slate-400 text-white shadow-lg"
                                        >
                                            <Lock size={20} />
                                        </div>
                                        <div>
                                            <span
                                                class="mb-1 block text-[10px] font-bold tracking-widest text-slate-600 uppercase"
                                                >Modul Terkunci</span
                                            >
                                            <p class="text-xs font-medium text-slate-500">
                                                Selesaikan modul sebelumnya untuk membuka modul ini.
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

                            {#if !isLocked && !state.isGuest}
                                <div class="mt-10 md:hidden">
                                    <Button
                                        variant="primary"
                                        class="shadow-primary-900/20 w-full shadow-lg"
                                        icon={Play}>Mulai Latihan</Button
                                    >
                                </div>
                            {/if}
                        </div>
                    {/snippet}

                    <!-- Render as link if unlocked, plain div if locked -->
                    {#if isLocked}
                        <div class="flex h-full flex-col md:flex-row">
                            {@render cardInner()}
                        </div>
                    {:else}
                        <Link
                            href={ROUTES.MAHASISWA.MATERIALS.QUESTIONS.LEVELS(material.id)}
                            class="flex h-full flex-col md:flex-row"
                        >
                            {@render cardInner()}
                        </Link>
                    {/if}
                </Card>
            {/each}
        </div>
    </div>
</App>
