<script>
    import Card from "@/components/ui/Card.svelte";
    import { Link } from "@inertiajs/svelte";
    import { Book, FileText } from "lucide-svelte";

    export let materials = [];
    export let currentMaterialId;
</script>

<Card class="sticky top-4" padding="p-2">
    <div slot="header">
        <h5
            class="font-black text-slate-900 uppercase tracking-widest text-xs flex items-center gap-3"
        >
            <div
                class="w-8 h-8 rounded-xl bg-primary-600 text-white flex items-center justify-center shadow-lg shadow-primary-900/20"
            >
                <Book size={16} />
            </div>
            Daftar Materi
        </h5>
    </div>

    <ul class="space-y-1 p-2">
        {#each materials as m (m.id)}
            <li>
                <Link
                    href={`/mahasiswa/materials/${m.id}`}
                    class={`group flex items-center gap-3 p-3 rounded-xl transition-all font-bold tracking-tight text-xs uppercase
                        ${
                            m.id === currentMaterialId
                                ? "bg-primary-600 text-white shadow-xl shadow-primary-900/20"
                                : "text-slate-500 hover:bg-slate-50 hover:text-primary-600"
                        }`}
                >
                    <div
                        class={`w-8 h-8 rounded-lg flex items-center justify-center transition-colors
                        ${m.id === currentMaterialId ? "bg-white/20" : "bg-slate-100 group-hover:bg-primary-100"}`}
                    >
                        <FileText size={16} />
                    </div>
                    <span class="flex-1 truncate">{m.title}</span>
                </Link>
            </li>
        {/each}
    </ul>
</Card>
