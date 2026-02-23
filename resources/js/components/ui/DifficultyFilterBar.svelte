<script>
    import Button from "@/components/ui/Button.svelte";

    export let difficulty = "all";
    export let onFilter;
</script>

<div class="flex flex-wrap gap-2 mt-4">
    <Button
        variant={difficulty === "all" ? "primary" : "outline"}
        on:click={() => onFilter("all")}
        size="sm"
    >
        Semua
    </Button>

    <Button
        variant={difficulty === "beginner" ? "success" : "outline"}
        on:click={() => onFilter("beginner")}
        size="sm"
        class={difficulty === "beginner" ? "" : "text-emerald-600 border-emerald-600 hover:bg-emerald-50"}
    >
        Beginner
    </Button>

    <Button
        variant={difficulty === "medium" ? "warning" : "outline"}
        on:click={() => onFilter("medium")}
        size="sm"
        class={difficulty === "medium" ? "" : "text-amber-600 border-amber-600 hover:bg-amber-50"}
    >
        Medium
    </Button>

    <Button
        variant={difficulty === "hard" ? "danger" : "outline"}
        on:click={() => onFilter("hard")}
        size="sm"
        class={difficulty === "hard" ? "" : "text-rose-600 border-rose-600 hover:bg-rose-50"}
    >
        Hard
    </Button>
</div>
