<script>
    import StatCard from "@/ui/StatCard.svelte";
    import { Layers, CalendarCheck, Video } from "lucide-svelte";

    export let totalMaterials;
    export let recentMaterials;
    export let totalMedia;
</script>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <StatCard
        title="Total Modul"
        value={totalMaterials}
        icon={Layers}
        variant="primary"
        footer="Modul instruksional aktif"
    />
    <StatCard
        title="Materi Baru"
        value={recentMaterials}
        icon={CalendarCheck}
        variant="success"
        footer="Penambahan 30 hari terakhir"
    />
    <StatCard
        title="Korpus Media"
        value={totalMedia}
        icon={Video}
        variant="primary"
        footer="Total aset multimedia"
    />
</div>
