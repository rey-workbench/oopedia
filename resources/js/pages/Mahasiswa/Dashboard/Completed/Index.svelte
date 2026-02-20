<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import { ArrowLeft } from "lucide-svelte";
    import CompletedMaterialList from "@/components/Mahasiswa/Dashboard/CompletedMaterialList.svelte";
    import { CompletedState } from "@/states/Mahasiswa/CompletedState.svelte";

    export let materials = [];

    const state = new CompletedState(materials);
</script>

<App title="Materi Selesai">
    <div class="space-y-12 pb-20">
        <PageHeader
            title="Hall of Fame"
            subtitle="Kumpulan modul pembelajaran yang telah berhasil Anda kuasai sepenuhnya."
        >
            <div slot="actions">
                <Button
                    href="/mahasiswa/dashboard"
                    variant="ghost"
                    icon={ArrowLeft}>KEMBALI</Button
                >
            </div>
        </PageHeader>

        <CompletedMaterialList materials={state.materials} />
    </div>
</App>
