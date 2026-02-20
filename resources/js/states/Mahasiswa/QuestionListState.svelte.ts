export class QuestionListState {
    materials = $state<any[]>([]);
    isGuest = $state(false);

    constructor(materials: any, isGuest: any) {
        this.materials = materials;
        this.isGuest = isGuest;
    }
}
