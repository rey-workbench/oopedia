<script>
    import StatCard from "@/components/ui/StatCard.svelte";
    import { Users, Signal, FolderTree, Cpu } from "lucide-svelte";

    export let totalStudents;
    export let activeStudents;
    export let totalMaterials;
    export let totalQuestions;
</script>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <StatCard
        title="Total Mahasiswa"
        value={totalStudents}
        icon={Users}
        variant="primary"
        footer="Entitas terdaftar"
    />
    <StatCard
        title="Node Aktif"
        value={activeStudents}
        icon={Signal}
        variant="success"
        footer="Sesi aktif hari ini"
    />
    <StatCard
        title="Modul Instruksional"
        value={totalMaterials}
        icon={FolderTree}
        variant="primary"
        footer="Konten aktif"
    />
    <StatCard
        title="Korpus Evaluasi"
        value={totalQuestions}
        icon={Cpu}
        variant="success"
        footer="Total butir evaluasi"
    />
</div>
