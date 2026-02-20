<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import { ArrowLeft, BookOpen, Rocket } from "lucide-svelte";
    import { InProgressState } from "@/states/Mahasiswa/InProgressState.svelte";
    import InProgressCard from "@/components/Mahasiswa/Dashboard/InProgressCard.svelte";

    export let materialsWithStats = [];

    const state = new InProgressState(materialsWithStats);
</script>

<App title="Materi Sedang Dipelajari">
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <PageHeader
                title="Materi Sedang Dipelajari"
                subtitle="Terus asah kemampuan Anda dan selesaikan tantangan yang ada."
            >
                <div slot="actions">
                    <Button
                        href="/mahasiswa/dashboard"
                        variant="ghost"
                        icon={ArrowLeft}
                    >
                        Dashboard
                    </Button>
                </div>
            </PageHeader>

            <div class="mt-10">
                {#if state.materialsWithStats.length === 0}
                    <div
                        class="text-center py-24 bg-white rounded-[2.5rem] shadow-sm border border-slate-100"
                    >
                        <div
                            class="w-24 h-24 bg-primary-50 text-primary-500 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-inner"
                        >
                            <BookOpen size={48} strokeWidth={2} />
                        </div>
                        <h3
                            class="text-3xl font-bold text-slate-900 mb-4 uppercase tracking-widest"
                        >
                            Belum Ada Progres
                        </h3>
                        <p
                            class="text-slate-500 mb-10 max-w-md mx-auto font-medium"
                        >
                            Anda belum memulai materi apapun. Pilih materi yang
                            Anda minati dan mulai petualangan belajar Anda
                            sekarang!
                        </p>
                        <Button
                            href="/mahasiswa/materials"
                            variant="primary"
                            class="px-10 py-4 rounded-2xl font-bold uppercase transition-all shadow-xl shadow-primary-900/10"
                            icon={Rocket}
                        >
                            Mulai Belajar
                        </Button>
                    </div>
                {:else}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                        {#each state.materialsWithStats as materialData (materialData.material.id)}
                            <InProgressCard {materialData} />
                        {/each}
                    </div>
                {/if}
            </div>
        </div>
    </div>
</App>
