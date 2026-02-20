<script>
    import Card from "@/components/ui/Card.svelte";
    import Button from "@/components/ui/Button.svelte";
    import { Link } from "@inertiajs/svelte";
    import { Code2, Plus } from "lucide-svelte";
    import { ROUTES } from "@/utils/route";
</script>

<div class="space-y-8">
    <h3 class="text-xl font-bold tracking-widest text-slate-900 uppercase">
        Materi Unggulan
    </h3>

    <div class="space-y-6">
        <Card
            padding="p-8"
            class="bg-white border border-slate-100 shadow-2xl relative overflow-hidden group"
        >
            <div
                class="absolute -top-10 -right-10 w-32 h-32 bg-primary-100/50 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-1000"
            ></div>
            <div class="mb-6 text-primary-600">
                <svelte:component this={Code2} size={32} strokeWidth={2.5} />
            </div>
            <h4
                class="text-lg font-bold tracking-widest mb-2 uppercase text-slate-900"
            >
                Dasar PBO: Class & Object
            </h4>
            <p class="text-slate-500 text-sm font-medium mb-8 leading-relaxed">
                Fundamental utama pemrograman berorientasi objek yang harus
                dikuasai.
            </p>
            <Button
                variant="primary"
                size="sm"
                class="w-full py-4 uppercase font-bold tracking-widest"
                href={ROUTES.MAHASISWA.MATERIALS.INDEX}
                >PELAJARI SEKARANG</Button
            >
        </Card>

        <Card
            padding="p-8"
            class="border-2 border-dashed border-slate-200 shadow-none hover:border-primary-400 hover:bg-primary-50/20 transition-all cursor-pointer group"
        >
            <Link
                href={ROUTES.MAHASISWA.MATERIALS.INDEX}
                class="flex flex-col items-center text-center"
            >
                <div
                    class="w-12 h-12 rounded-xl bg-slate-100/50 text-slate-500 flex items-center justify-center mb-4 group-hover:bg-primary-600 group-hover:text-white transition-all"
                >
                    <svelte:component this={Plus} size={24} strokeWidth={3} />
                </div>
                <h4
                    class="text-[10px] font-bold text-slate-600 uppercase tracking-widest group-hover:text-primary-600"
                >
                    Eksplorasi Katalog Materi
                </h4>
                <p
                    class="mt-2 text-[9px] font-bold text-primary-600 uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-opacity"
                >
                    LIHAT SEMUA
                </p>
            </Link>
        </Card>
    </div>
</div>
