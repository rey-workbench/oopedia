<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import MaterialGrid from "@/components/Mahasiswa/Materials/MaterialGrid.svelte";
    import { MaterialCatalogState } from "@/states/Mahasiswa/MaterialCatalogState.svelte";

    export let materials = [];
    export let isGuest = false;

    const state = new MaterialCatalogState(materials, isGuest);
</script>

<App title="Materi Pembelajaran">
    <div class="space-y-12">
        <PageHeader
            title="Kurikulum PBO"
            subtitle="Kuasai konsep fondasi hingga tingkat lanjut Pemrograman Berorientasi Objek."
        />

        <MaterialGrid materials={state.materials} isGuest={state.isGuest} />
    </div>
</App>
