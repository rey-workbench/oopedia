<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import UeqStats from "@/components/Admin/Ueq/UeqStats.svelte";
    import UeqList from "@/components/Admin/Ueq/UeqList.svelte";
    import { FileDown } from "lucide-svelte";
    import { UeqListState } from "@/states/Admin/UeqState.svelte";

    export let surveys = [];
    export let averages = {};
    export let classes = [];
    export let activeClass = "";

    const state = new UeqListState(surveys, averages, classes, activeClass);
</script>

<App title="Hasil Survey UEQ">
    <div class="space-y-12 pb-20">
        <PageHeader
            title="Analitik User Experience"
            subtitle="Metrik komprehensif kepuasan pengguna menggunakan kuesioner UEQ (User Experience Questionnaire)."
        >
            <div slot="actions">
                <Button
                    on:click={() => state.exportResults()}
                    variant="success"
                    icon={FileDown}>EKSPOR CSV</Button
                >
            </div>
        </PageHeader>

        <!-- Averages Overview -->
        <UeqStats averages={state.averages} />

        <UeqList {state} />
    </div>
</App>
