import { router } from "@inertiajs/svelte";
import { debounce } from "lodash";
import { confirmDelete } from "@/utils/confirmDelete";

export class QuestionListAdminState {
    questions = $state<any>({ data: [] });
    material = $state<any>(null);
    search = $state("");
    difficulty = $state("");

    constructor(questions: any, material: any, search: any, difficulty: any) {
        this.questions = questions;
        this.material = material;
        this.search = search;
        this.difficulty = difficulty;
    }

    handleSearch = debounce(() => {
        router.get(
            this.material
                ? `/admin/materials/${this.material.id}/questions`
                : "/admin/questions",
            {
                search: this.search,
                difficulty: this.difficulty,
            },
            { preserveState: true, preserveScroll: true }
        );
    }, 300);

    handleDelete(id: any) {
        const url = this.material
            ? `/admin/materials/${this.material.id}/questions/${id}`
            : `/admin/questions/${id}`;
        confirmDelete(url, "Hapus soal ini?");
    }

    setDifficulty(diff: any) {
        this.difficulty = diff;
        this.handleSearch();
    }
}
