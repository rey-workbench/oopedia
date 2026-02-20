<script>
    import Card from "@/components/ui/Card.svelte";
    import { Link } from "@inertiajs/svelte";
    import { Book, FileText } from "lucide-svelte";

    export let materials = [];
    export let currentMaterialId;
</script>

<Card class="sticky top-4">
    <div class="mb-4">
        <h5 class="font-bold text-lg text-slate-900">
            <Book size={18} class="mr-2" />Daftar Materi
        </h5>
    </div>
    <ul class="space-y-2">
        {#each materials as m (m.id)}
            <li>
                <Link
                    href={`/mahasiswa/materials/${m.id}`}
                    class={`block p-3 rounded-lg transition-colors ${m.id === currentMaterialId ? "bg-primary-600 text-white" : "text-slate-700 hover:bg-slate-100"}`}
                >
                    <FileText size={16} class="mr-2" />{m.title}
                </Link>
            </li>
        {/each}
    </ul>
</Card>
