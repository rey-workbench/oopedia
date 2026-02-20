<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import MaterialGrid from "@/components/Mahasiswa/Materials/MaterialGrid.svelte";
    import { MaterialCatalogState } from "@/states/Mahasiswa/MaterialState.svelte";

    export let materials = [];

    const state = new MaterialCatalogState(materials);
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
