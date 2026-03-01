<script lang="ts">
    import App from "@/layouts/App.svelte";
    import GuestBanner from "@/components/shared/GuestBanner.svelte";
    import Card from "@/components/ui/Card.svelte";
    import Badge from "@/components/ui/Badge.svelte";
    import Button from "@/components/ui/Button.svelte";
    import ProgressBar from "@/components/ui/ProgressBar.svelte";
    import { Shapes, Users, Puzzle, Play, Lock } from "lucide-svelte";
    import { Link } from "@inertiajs/svelte";
    import { ROUTES } from "@/utils/route";
    import { QuestionListState } from "@/states/Mahasiswa/QuizState.svelte";

    import type { Material } from "@/types";

    const { materials = [] }: { materials: Material[] } = $props();

    const state = new QuestionListState(materials);
</script>

<App title="Latihan Soal PBO">
    <div class="space-y-12">
        
<div class="mb-8">
    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 leading-tight font-display">
        Latihan Soal PBO
    </h1>
    <div class="flex items-center gap-2 mt-3" role="presentation">
        <div class="h-1.5 w-12 bg-primary-600 rounded-full"></div>
        <div class="h-1.5 w-4 bg-slate-200 rounded-full"></div>
        <div class="h-1.5 w-2 bg-slate-100 rounded-full"></div>
    </div>
    <p class="mt-4 text-slate-500 font-medium leading-relaxed max-w-3xl">
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
                        href={ROUTES.MAHASISWA.MATERIALS.QUESTIONS.LEVELS(
                            material.id,
                        )}
                        class="flex flex-col md:flex-row h-full"
                    >
                        <!-- Graphic Section -->
                        <div class="md:w-72 lg:w-96 relative shrink-0">
                            {#if material.media && material.media.length > 0}
                                <div class="h-60 md:h-full">
                                    <img
                                        src={material.media[0]?.full_url}
                                        alt={material.title}
                                        class="w-full h-full object-cover"
                                    />
                                    <div
                                        class="absolute inset-0 bg-slate-900/10 group-hover:bg-transparent transition-colors"
                                    ></div>
                                </div>
                            {:else}
                                <div
                                    class="h-60 md:h-full bg-primary-600 flex items-center justify-center"
                                >
                                    <Shapes
                                        size={96}
                                        class="text-white/10 group-hover:rotate-6 transition-transform"
                                    />
                                </div>
                            {/if}
                            <div class="absolute top-6 left-6">
                                <Badge
                                    variant="primary"
                                    size="sm"
                                    class="shadow-xl">MODUL AKTIF</Badge
                                >
                            </div>
                        </div>

                        <!-- Content Section -->
                        <div class="flex-1 p-10 flex flex-col justify-between">
                            <div>
                                <div
                                    class="flex justify-between items-start gap-6"
                                >
                                    <div>
                                        <h2
                                            class="text-3xl font-bold text-slate-900 leading-tight mb-3 group-hover:text-primary-600 transition-colors tracking-widest"
                                        >
                                            {material.title}
                                        </h2>
                                        <div
                                            class="flex flex-wrap items-center gap-6"
                                        >
                                            <div
                                                class="flex items-center gap-2.5"
                                            >
                                                <div
                                                    class="w-8 h-8 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center text-xs shadow-inner"
                                                >
                                                    <Users size={14} />
                                                </div>
                                                <span
                                                    class="text-[10px] font-bold uppercase tracking-widest text-slate-400"
                                                    >{material.student_count ||
                                                        0} Mahasiswa</span
                                                >
                                            </div>
                                            <div
                                                class="flex items-center gap-2.5"
                                            >
                                                <div
                                                    class="w-8 h-8 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center text-xs shadow-inner"
                                                >
                                                    <Puzzle size={14} />
                                                </div>
                                                <span
                                                    class="text-[10px] font-bold uppercase tracking-widest text-slate-400"
                                                    >{material.total_questions ||
                                                        0} Soal Latihan</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="hidden sm:flex w-14 h-14 rounded-2xl bg-slate-50 text-slate-900 items-center justify-center shadow-inner group-hover:bg-primary-600 group-hover:text-white transition-all"
                                    >
                                        <Play
                                            size={20}
                                            class="ml-1 fill-current"
                                        />
                                    </div>
                                </div>

                                {#if state.isGuest}
                                    <div
                                        class="mt-8 p-6 bg-amber-50 rounded-[2rem] border border-amber-100 flex items-center gap-6 ring-8 ring-amber-50/50"
                                    >
                                        <div
                                            class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-lg shadow-amber-200"
                                        >
                                            <Lock size={20} />
                                        </div>
                                        <div>
                                            <span
                                                class="text-[10px] font-bold text-amber-800 uppercase tracking-widest block mb-1"
                                                >Akses Terbatas</span
                                            >
                                            <p
                                                class="text-xs text-amber-700 font-medium"
                                            >
                                                Selesaikan pendaftaran untuk
                                                membuka semua level soal.
                                            </p>
                                        </div>
                                    </div>
                                {:else}
                                    <div class="mt-8 space-y-4">
                                        <div
                                            class="flex justify-between items-center px-1"
                                        >
                                            <div
                                                class="flex items-center gap-3"
                                            >
                                                <span
                                                    class="text-[10px] font-bold uppercase tracking-widest text-slate-400"
                                                    >Progress Mastery</span
                                                >
                                                <Badge
                                                    variant="success"
                                                    size="xs"
                                                    >{material.progress_percentage ||
                                                        0}%</Badge
                                                >
                                            </div>
                                            <span
                                                class="text-[10px] font-bold text-slate-300 uppercase"
                                                >{material.completed_questions ||
                                                    0}/{material.total_questions ||
                                                    0} SOAL</span
                                            >
                                        </div>
                                        <ProgressBar
                                            value={material.progress_percentage ||
                                                0}
                                            height="h-2"
                                            color="emerald"
                                        />
                                    </div>
                                {/if}
                            </div>

                            <div class="mt-10 md:hidden">
                                <Button
                                    variant="primary"
                                    class="w-full shadow-lg shadow-primary-900/20"
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
