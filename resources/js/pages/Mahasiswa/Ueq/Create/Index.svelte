<script>
    import App from "@/layouts/App.svelte";
    import UeqForm from "@/components/Mahasiswa/Ueq/UeqForm.svelte";
    import { ClipboardList } from "lucide-svelte";
    import { UeqSurveyState } from "@/states/Mahasiswa/UeqSurveyState.svelte";

    export let aspects = [];

    const state = new UeqSurveyState(aspects);
</script>

<App title="UEQ Survey">
    <div class="space-y-12 pb-20">
        <div class="text-center space-y-6">
            <div
                class="inline-flex items-center justify-center w-20 h-20 bg-primary-50 text-primary-600 rounded-[2rem] shadow-inner"
            >
                <ClipboardList size={32} />
            </div>
            <h1
                class="text-4xl font-bold text-slate-900 tracking-[0.2em] uppercase"
            >
                User Experience <span class="text-primary-600"
                    >Questionnaire</span
                >
            </h1>
            <p class="text-slate-500 text-lg max-w-2xl mx-auto font-medium">
                Kami mengundang Anda untuk memberikan evaluasi objektif terhadap
                pengalaman interaksi Anda dengan platform OOPEDIA.
            </p>
        </div>

        <UeqForm
            aspects={state.aspects}
            questionnaireAspects={state.questionnaireAspects}
        />
    </div>
</App>
