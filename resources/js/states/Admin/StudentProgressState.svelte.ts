export class StudentProgressState {
    student = $state<any>({});
    materials = $state<any[]>([]);
    missingQuestionsByMaterial = $state<any[]>([]);

    avgProgress = $derived(
        this.materials.length > 0
            ? (
                this.materials.reduce(
                    (acc, m) => acc + (Number(m.progress) || 0),
                    0
                ) / this.materials.length
            ).toFixed(1)
            : "0.0"
    );

    completedModules = $derived(
        this.materials.filter((m) => Number(m.progress) === 100).length
    );

    totalModules = $derived(this.materials.length);

    missingQuestions = $derived(
        this.missingQuestionsByMaterial.reduce(
            (acc, item) => acc + item.missing_count,
            0
        )
    );

    constructor(student: any, materials: any, missingQuestionsByMaterial: any) {
        this.student = student;
        this.materials = materials;
        this.missingQuestionsByMaterial = missingQuestionsByMaterial;
    }
}
