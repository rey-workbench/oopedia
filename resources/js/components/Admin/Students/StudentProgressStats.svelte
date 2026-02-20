<script>
    import StatCard from "@/components/ui/StatCard.svelte";
    import { LineChart, CheckCheck, Zap } from "lucide-svelte";

    export let avgProgress = "0";
    export let completedModules = 0;
    export let totalModules = 0;
    export let missingQuestions = 0;
</script>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <StatCard
        title="Lintasan Pembelajaran"
        value={`${avgProgress}%`}
        icon={LineChart}
        variant="primary"
        footer="Rata-rata penyelesaian modul"
    />
    <StatCard
        title="Modul Berhasil Diselesaikan"
        value={`${completedModules} / ${totalModules}`}
        icon={CheckCheck}
        variant="success"
        footer="Penyelesaian 100% tercapai"
    />
    <StatCard
        title="Sisa Unit Tantangan"
        value={missingQuestions}
        icon={Zap}
        variant="danger"
        footer="Jawaban benar tertunda"
    />
</div>
