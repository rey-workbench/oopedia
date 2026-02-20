<script>
    import StatCard from "@/components/ui/StatCard.svelte";
    import { BookOpen, Brain, Flame, Trophy } from "lucide-svelte";

    export let totalMaterials = 0;
    export let totalQuestions = 0;
    export let hardQuestions = 0;
</script>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
    <StatCard
        title="Materi Tersedia"
        value={totalMaterials}
        icon={BookOpen}
        variant="primary"
        footer="Konsep PBO dari Dasar"
    />
    <StatCard
        title="Total Soal"
        value={totalQuestions}
        icon={Brain}
        variant="success"
        footer="Latihan & Tantangan"
    />
    <StatCard
        title="Level Hard"
        value={hardQuestions}
        icon={Flame}
        variant="danger"
        footer="Tingkat Kesulitan Tinggi"
    />
    <StatCard
        title="Peringkat Anda"
        value="#12"
        icon={Trophy}
        variant="warning"
        footer="Terus tingkatkan skor!"
    />
</div>
