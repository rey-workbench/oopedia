<script>
    import App from "../../../layouts/App.svelte";
    import PageHeader from "../../../components/ui/PageHeader.svelte";
    import Card from "../../../components/ui/Card.svelte";
    import Button from "../../../components/ui/Button.svelte";
    import { Code, Puzzle, BookOpen, Ghost, ArrowRight } from "lucide-svelte";
    import { formatDate } from "../../../utils/formatters";

    export let materials = [];
    export let isGuest = false;
</script>

<App title="Materi Pembelajaran">
    <div class="space-y-12">
        <PageHeader
            title="Kurikulum PBO"
            subtitle="Kuasai konsep fondasi hingga tingkat lanjut Pemrograman Berorientasi Objek."
        />

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            {#each materials as material (material.id)}
                <Card
                    padding="p-0 flex flex-col flex-1"
                    class="flex flex-col h-full group overflow-hidden"
                    data-intro="Ini adalah modul pembelajaran. Setiap kartu mewakili topik PBO yang bisa kamu pelajari."
                    data-step="6"
                >
                    <!-- Image Section -->
                    <div class="relative h-60 overflow-hidden shrink-0">
                        {#if material.media && material.media.length > 0}
                            <img
                                src={material.media[0].media_url}
                                alt={material.title}
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                            />
                        {:else}
                            <div
                                class="w-full h-full bg-primary-600 flex items-center justify-center"
                            >
                                <Code
                                    size={96}
                                    class="text-white/10 group-hover:rotate-12 transition-transform"
                                />
                            </div>
                        {/if}
                        <div class="absolute inset-0 bg-slate-900/40"></div>

                        <div
                            class="absolute bottom-6 left-6 right-6 flex justify-between items-center"
                        >
                            <div
                                class="px-4 py-2 bg-white/10 backdrop-blur-md rounded-2xl text-white text-[10px] font-bold uppercase tracking-widest border border-white/20"
                            >
                                {formatDate(material.updated_at, {
                                    year: "numeric",
                                    month: "short",
                                })}
                            </div>
                            <div
                                class="flex items-center gap-2 px-4 py-2 bg-primary-600 rounded-2xl text-white text-[10px] font-bold uppercase tracking-widest shadow-xl shadow-primary-500/20"
                            >
                                <Puzzle size={14} />
                                {material.total_questions} Tantangan
                            </div>
                        </div>
                    </div>

                    <!-- Content Section -->
                    <div class="p-8 flex-1 flex flex-col">
                        <div class="mb-4 min-h-[4.5rem] flex items-start">
                            <h2
                                class="text-2xl font-bold text-slate-900 leading-tight group-hover:text-primary-600 transition-colors uppercase tracking-widest"
                            >
                                {material.title}
                            </h2>
                        </div>

                        <div class="mb-6">
                            <div class="flex items-center gap-3 text-slate-400">
                                <div
                                    class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center text-xs text-slate-500 shadow-inner"
                                >
                                    <BookOpen size={14} />
                                </div>
                                <span
                                    class="text-[10px] font-bold uppercase tracking-widest"
                                    >{material.creator
                                        ? material.creator.name
                                        : "Admin System"}</span
                                >
                            </div>
                        </div>

                        {#if isGuest}
                            <div
                                class="mb-8 p-5 bg-amber-50 rounded-3xl border border-amber-100 flex items-start gap-4 ring-4 ring-amber-50/50 min-h-[100px]"
                            >
                                <div
                                    class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-lg shadow-amber-200"
                                >
                                    <Ghost size={24} />
                                </div>
                                <div>
                                    <span
                                        class="text-[10px] font-bold text-amber-800 uppercase tracking-widest block mb-1"
                                        >Mode Tamu</span
                                    >
                                    <p
                                        class="text-xs text-amber-700 font-medium leading-relaxed"
                                    >
                                        Akses terbatas ke materi & soal-soal
                                        pilihan.
                                    </p>
                                </div>
                            </div>
                        {:else}
                            <!-- Spacer to maintain alignment when guest banner is absent -->
                            <div class="mb-8 min-h-[100px]"></div>
                        {/if}

                        <div class="mt-auto pt-6">
                            <Button
                                href={`/mahasiswa/materials/${material.id}`}
                                variant="primary"
                                class="w-full"
                                size="lg"
                                icon={ArrowRight}
                                data-intro="Klik tombol ini untuk masuk ke dalam modul dan melihat materi serta tantangan di dalamnya."
                                data-step="7"
                            >
                                MULAI BELAJAR
                            </Button>
                        </div>
                    </div>
                </Card>
            {/each}
        </div>
    </div>
</App>
