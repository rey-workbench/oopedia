export class LevelMapState {
    material = $state<any>({});
    levels = $state<any[]>([]);
    isGuest = $state(false);

    beginnerLevels = $derived(this.levels.filter(l => l.level === 'beginner'));
    mediumLevels = $derived(this.levels.filter(l => l.level === 'medium'));
    hardLevels = $derived(this.levels.filter(l => l.level === 'hard'));

    constructor(material: any, levels: any, isGuest: any) {
        this.material = material;
        this.levels = levels;
        this.isGuest = isGuest;
    }
}
