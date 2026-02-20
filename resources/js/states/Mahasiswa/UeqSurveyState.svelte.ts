export class UeqSurveyState {
    aspects = $state<any[]>([]);

    questionnaireAspects = [
        { name: "annoying_enjoyable", left: "Menyebalkan", right: "Menyenangkan" },
        // ... lines 6-31 omitted for brevity ...
        { name: "conservative_innovative", left: "Konservatif", right: "Inovatif" },
    ];

    constructor(aspects: any) {
        this.aspects = aspects;
    }
}
