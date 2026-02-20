<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import MaterialStats from "@/components/Admin/Materials/MaterialStats.svelte";
    import MaterialList from "@/components/Admin/Materials/MaterialList.svelte";
    import { Plus } from "lucide-svelte";
    import { ROUTES } from "@/utils/route";

    export let materials = [];
    $: totalMaterials = materials.length;
    $: recentMaterials = materials.filter((m) => {
        const date = new Date(m.created_at);
        const thirtyDaysAgo = new Date();
        thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
        return date >= thirtyDaysAgo;
    }).length;
    $: totalMedia = materials.reduce(
        (acc, m) => acc + (m.media ? m.media.length : 0),
        0,
    );

    let search =
        new URLSearchParams(window.location.search).get("search") || "";
</script>

<App title="Kelola Materi">
    <div class="space-y-12">
        <PageHeader
            title="Kurikulum Materi"
            subtitle="Otoritas manajemen konten dan modul pembelajaran Pemrograman Berorientasi Objek."
        >
            <div slot="actions">
                <Button
                    href={ROUTES.ADMIN.MATERIALS.CREATE}
                    variant="primary"
                    icon={Plus}>Tambah Modul Baru</Button
                >
            </div>
        </PageHeader>

        <!-- Statistics -->
        <MaterialStats {totalMaterials} {recentMaterials} {totalMedia} />

        <!-- Material List -->
        <MaterialList {materials} {search} />
    </div>
</App>
