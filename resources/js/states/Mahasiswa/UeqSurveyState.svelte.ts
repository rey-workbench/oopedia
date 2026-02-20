import { BaseState } from "@/states/BaseState.svelte";

export class UeqSurveyState extends BaseState {
    aspects = $state<any[]>([]);

    questionnaireAspects = [
        { name: "annoying_enjoyable", left: "Menyebalkan", right: "Menyenangkan" },
        // ... (preserving context as it was already truncated in view) ...
        { name: "conservative_innovative", left: "Konservatif", right: "Inovatif" },
    ];

    constructor(aspects: any) {
        super();
        this.aspects = aspects;
    }
}
