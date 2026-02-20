<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import { ArrowLeft } from "lucide-svelte";
    import UeqStudentProfile from "@/components/Admin/Ueq/UeqStudentProfile.svelte";
    import UeqScoreCards from "@/components/Admin/Ueq/UeqScoreCards.svelte";
    import UeqFeedbackCards from "@/components/Admin/Ueq/UeqFeedbackCards.svelte";
    import UeqMatrixTable from "@/components/Admin/Ueq/UeqMatrixTable.svelte";
    import { UeqDetailState } from "@/states/Admin/UeqDetailState.svelte";

    export let user;
    export let survey;

    const state = new UeqDetailState(user, survey);
</script>

<App title={`Detail UEQ - ${state.user.name}`}>
    <div class="space-y-12 pb-20">
        <PageHeader
            title="Detail Evaluasi UEQ"
            subtitle={`Analisis mendalam pengalaman pengguna untuk ${state.user.name}.`}
        >
            <div slot="actions">
                <Button href="/admin/ueq" variant="ghost" icon={ArrowLeft}
                    >KEMBALI KE DAFTAR</Button
                >
            </div>
        </PageHeader>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <!-- Left Column: User Profile & Score Summary -->
            <div class="space-y-10">
                <UeqStudentProfile user={state.user} survey={state.survey} />

                <UeqScoreCards dimensions={state.dimensions} />
            </div>

            <!-- Right Column: Qualitative & Matrix -->
            <div class="lg:col-span-2 space-y-10">
                <UeqFeedbackCards survey={state.survey} />

                <UeqMatrixTable survey={state.survey} aspects={state.aspects} />
            </div>
        </div>
    </div>
</App>
