<script>
    import App from "../../../../layouts/App.svelte";
    import PageHeader from "../../../../components/ui/PageHeader.svelte";
    import Card from "../../../../components/ui/Card.svelte";
    import Button from "../../../../components/ui/Button.svelte";
    import Badge from "../../../../components/ui/Badge.svelte";
    import {
        ArrowLeft,
        Medal,
        GraduationCap,
        CheckCircle2,
        Book,
        RotateCcw,
    } from "lucide-svelte";

    export let materials = [];
</script>

<App title="Materi Selesai">
    <div class="space-y-12 pb-20">
        <PageHeader
            title="Hall of Fame"
            subtitle="Kumpulan modul pembelajaran yang telah berhasil Anda kuasai sepenuhnya."
        >
            <div slot="actions">
                <Button
                    href="/mahasiswa/dashboard"
                    variant="ghost"
                    icon={ArrowLeft}>KEMBALI</Button
                >
            </div>
        </PageHeader>

        {#if materials.length === 0}
            <Card
                padding="p-20"
                class="text-center border-dashed border-slate-200 shadow-none"
            >
                <div
                    class="w-24 h-24 bg-slate-50 rounded-[2.5rem] flex items-center justify-center mx-auto mb-8 text-slate-200"
                >
                    <Medal size={48} strokeWidth={1.5} />
                </div>
                <h3
                    class="text-xl font-bold tracking-widest text-slate-900 mb-4 uppercase"
                >
                    Belum Ada Sertifikat
                </h3>
                <p
                    class="text-slate-400 text-sm font-bold uppercase tracking-widest max-w-xs mx-auto mb-8"
                >
                    Selesaikan setidaknya satu modul pembelajaran untuk
                    melihatnya di sini.
                </p>
                <Button
                    href="/mahasiswa/materials"
                    variant="primary"
                    icon={GraduationCap}>MULAI BELAJAR</Button
                >
            </Card>
        {:else}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                {#each materials as material}
                    <Card
                        padding="p-8"
                        class="hover:border-blue-400 border-slate-100 shadow-xl transition-all group relative overflow-hidden"
                    >
                        <div
                            class="absolute -top-6 -right-6 w-24 h-24 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-500 group-hover:bg-emerald-500 group-hover:text-white transition-all"
                        >
                            <CheckCircle2 size={32} strokeWidth={2.5} />
                        </div>

                        <div class="space-y-6">
                            <div
                                class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-blue-600 transition-colors group-hover:bg-blue-600 group-hover:text-white"
                            >
                                <Book size={24} strokeWidth={2.5} />
                            </div>

                            <div>
                                <h4
                                    class="text-lg font-bold tracking-widest text-slate-900 uppercase mb-2 leading-tight"
                                >
                                    {material.title}
                                </h4>
                                <p
                                    class="text-xs font-medium text-slate-500 line-clamp-2 leading-relaxed"
                                >
                                    {material.description ||
                                        "Deskripsi modul tidak tersedia."}
                                </p>
                            </div>

                            <div
                                class="pt-6 border-t border-slate-50 flex items-center justify-between"
                            >
                                <Badge variant="success" size="xs"
                                    >MASTERED</Badge
                                >
                                <Button
                                    variant="ghost"
                                    size="xs"
                                    href={`/mahasiswa/materials/${material.id}`}
                                    icon={RotateCcw}>ULAS MATERI</Button
                                >
                            </div>
                        </div>
                    </Card>
                {/each}
            </div>
        {/if}
    </div>
</App>
