export class CompletedState {
    materials = $state<any[]>([]);

    constructor(materials: any) {
        this.materials = materials;
    }
}
