import { BaseState } from "@/states/BaseState.svelte";
import type { UeqSurvey } from "@/types";

export class UeqSurveyState extends BaseState {
    aspects = $state<Partial<UeqSurvey>[]>([]);
    questionnaireAspects = [
        { name: "annoying_enjoyable", left: "Menyebalkan", right: "Menyenangkan" },
        // ... (preserving context as it was already truncated in view) ...
        { name: "conservative_innovative", left: "Konservatif", right: "Inovatif" },
    ];

    constructor(aspects: Partial<UeqSurvey>[]) {
        super();
        this.aspects = aspects;
    }
}
