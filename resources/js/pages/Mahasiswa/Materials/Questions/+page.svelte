<script>
    import App from "../../../../layouts/App.svelte";
    import PageHeader from "../../../../components/ui/PageHeader.svelte";
    import Card from "../../../../components/ui/Card.svelte";
    import Button from "../../../../components/ui/Button.svelte";
    import Badge from "../../../../components/ui/Badge.svelte";
    import ProgressBar from "../../../../components/ui/ProgressBar.svelte";
    import { Link } from "@inertiajs/svelte";
    import { Shapes, Users, Puzzle, Play, Lock } from "lucide-svelte";
    import GuestBanner from "../../../../components/ui/GuestBanner.svelte";

    export let materials = [];
    export let isGuest = false;
</script>

<App title="Latihan Soal PBO">
    <div class="space-y-12">
        <PageHeader
            title="Latihan Soal PBO"
            subtitle="Uji pemahaman Anda dengan mengerjakan latihan soal untuk setiap materi"
        />

        {#if isGuest}
            <GuestBanner
                show={isGuest}
                variant="banner"
                title="Mode Tamu Aktif!"
                message="Anda hanya dapat melihat sebagian materi dan hanya 3 soal latihan dari setiap tingkat kesulitan. Untuk akses penuh, silakan login atau daftar."
            />
        {/if}

        <div class="grid grid-cols-1 gap-10">
            {#each materials as material (material.id)}
                <Card padding="p-0" hover={true} class="overflow-hidden">
                    <Link
                        href={`/mahasiswa/materials/${material.id}/questions/levels`}
                        class="flex flex-col md:flex-row h-full"
                    >
                        <!-- Graphic Section -->
                        <div class="md:w-72 lg:w-96 relative shrink-0">
                            {#if material.media && material.media.length > 0}
                                <div class="h-60 md:h-full">
                                    <img
                                        src={material.media[0].media_url}
                                        alt={material.title}
                                        class="w-full h-full object-cover"
                                    />
                                    <div
                                        class="absolute inset-0 bg-slate-900/10 group-hover:bg-transparent transition-colors"
                                    ></div>
                                </div>
                            {:else}
                                <div
                                    class="h-60 md:h-full bg-gradient-to-br from-indigo-600 to-blue-700 flex items-center justify-center"
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
                                            class="text-3xl font-bold text-slate-900 leading-tight mb-3 group-hover:text-blue-600 transition-colors tracking-widest"
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
                                                    class="w-8 h-8 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center text-xs shadow-inner"
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
                                                    class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center text-xs shadow-inner"
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
                                        class="hidden sm:flex w-14 h-14 rounded-2xl bg-slate-50 text-slate-900 items-center justify-center shadow-inner group-hover:bg-blue-600 group-hover:text-white transition-all"
                                    >
                                        <Play
                                            size={20}
                                            class="ml-1 fill-current"
                                        />
                                    </div>
                                </div>

                                {#if isGuest}
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
                                    class="w-full"
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
