<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import StudentProgressStats from "@/components/Admin/Students/StudentProgressStats.svelte";
    import StudentProgressTable from "@/components/Admin/Students/StudentProgressTable.svelte";
    import { ArrowLeft } from "lucide-svelte";
    import { ROUTES } from "@/utils/route";
    import { StudentProgressState } from "@/states/Admin/StudentState.svelte";

    export let student;
    export let materials = [];
    export let missingQuestionsByMaterial = [];

    const state = new StudentProgressState(
        student,
        materials,
        missingQuestionsByMaterial,
    );
</script>

<App title="Progress Mahasiswa">
    <div class="space-y-12">
        <PageHeader
            title="Wawasan Performa Siswa"
            subtitle={`Analisis trajectory pembelajaran untuk entitas ${state.student.name}.`}
        >
            <div slot="actions">
                <Button
                    href={ROUTES.ADMIN.STUDENTS.INDEX}
                    variant="ghost"
                    icon={ArrowLeft}>KEMBALI KE DAFTAR</Button
                >
            </div>
        </PageHeader>

        <!-- Summary Cards -->
        <StudentProgressStats
            avgProgress={state.avgProgress}
            completedModules={state.completedModules}
            totalModules={state.totalModules}
            missingQuestions={state.missingQuestions}
        />

        <!-- Tables -->
        <StudentProgressTable
            materials={state.materials}
            missingQuestionsByMaterial={state.missingQuestionsByMaterial}
        />
    </div>
</App>
