import { router } from "@inertiajs/svelte";

export class ReviewState {
    material = $state<any>({});
    materials = $state<any[]>([]);
    questions = $state<any[]>([]);
    difficulty = $state("");

    constructor(material: any, materials: any, questions: any, difficulty: any) {
        this.material = material;
        this.materials = materials;
        this.questions = questions;
        this.difficulty = difficulty;
    }

    getDifficultyLabel(d: any) {
        return d === 'beginner' ? 'Pemula' : d === 'medium' ? 'Menengah' : 'Sulit';
    }

    getDifficultyColor(d: any) {
        return d === 'beginner' ? 'text-emerald-600 bg-emerald-50' : d === 'medium' ? 'text-amber-600 bg-amber-50' : 'text-rose-600 bg-rose-50';
    }

    filterDifficulty(d: any) {
        router.get(
            `/mahasiswa/materials/${this.material.id}/questions/review`,
            { difficulty: d },
            {
                preserveState: true,
                preserveScroll: true,
                only: ["questions", "difficulty"],
            }
        );
    }
}
