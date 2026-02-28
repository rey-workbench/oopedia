<script>
    import StatCard from "@/components/ui/StatCard.svelte";

    export let stats = [];
    export let gridClass = "grid-cols-1 md:grid-cols-2 lg:grid-cols-4";
</script>

<div class="grid {gridClass} gap-6">
    {#each stats as stat}
        <StatCard
            title={stat.title}
            value={stat.value}
            icon={stat.icon}
            variant={stat.variant || "primary"}
            footer={stat.footer}
        />
    {/each}
</div>
