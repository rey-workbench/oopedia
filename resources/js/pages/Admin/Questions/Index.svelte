<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import QuestionList from "@/components/Admin/Questions/QuestionList.svelte";
    import QuestionFilter from "@/components/Admin/Questions/QuestionFilter.svelte";
    import { Plus, ArrowLeft } from "lucide-svelte";
    import { QuestionListAdminState } from "@/states/Admin/QuestionListAdminState.svelte";

    export let questions = { data: [] };
    export let material = null;
    export let search = "";
    export let difficulty = "";

    const state = new QuestionListAdminState(
        questions,
        material,
        search,
        difficulty,
    );
</script>

<App
    title={`Kelola Bank Soal ${state.material ? ": " + state.material.title : ""}`}
>
    <div class="space-y-12">
        <PageHeader
            title="Repositori Evaluasi"
            subtitle={state.material
                ? `Kumpulan instrumen penilaian untuk materi: ${state.material.title}`
                : "Manajemen komprehensif seluruh bank soal evaluasi sistem."}
        >
            <div slot="actions" class="flex flex-wrap items-center gap-4">
                <Button
                    href={state.material
                        ? `/admin/materials/${state.material.id}/questions/create`
                        : "/admin/questions/create"}
                    variant="primary"
                    icon={Plus}>TAMBAH INSTRUMEN</Button
                >
                {#if state.material}
                    <Button
                        href="/admin/materials"
                        variant="ghost"
                        icon={ArrowLeft}>KEMBALI</Button
                    >
                {/if}
            </div>
        </PageHeader>

        <QuestionFilter {state} />

        <QuestionList {state} />
    </div>
</App>
