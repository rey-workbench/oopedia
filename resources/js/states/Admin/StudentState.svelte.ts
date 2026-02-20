import { router } from "@inertiajs/svelte";
import { debounce } from "lodash";
import { confirmDelete } from "@/utils/confirmDelete";
import { BaseState } from "@/states/BaseState.svelte";
import { FormState } from "@/states/FormState.svelte";
import { ROUTES } from "@/utils/route";
import type { User, Pagination, MaterialWithProgress, MissingQuestionsItem } from "@/types";

/**
 * Student List State
 */
export class StudentListState extends BaseState {
    students = $state<Pagination<User>>({ data: [], links: [], current_page: 1, from: null, last_page: 1, path: "", per_page: 10, to: null, total: 0 });
    search = $state("");

    constructor(students: Pagination<User>, search: string) {
        super();
        this.students = students;
        this.search = search;
    }

    handleSearch = debounce(() => {
        router.get(
            ROUTES.ADMIN.STUDENTS.INDEX,
            { search: this.search },
            { preserveState: true, replace: true }
        );
    }, 300);

    handleDelete(id: number) {
        confirmDelete(
            ROUTES.ADMIN.STUDENTS.DELETE(id),
            "Hapus data mahasiswa ini?"
        );
    }
}

/**
 * Student Progress State
 */
export class StudentProgressState extends BaseState {
    student = $state<User>({} as User);
    materials = $state<MaterialWithProgress[]>([]);
    missingQuestionsByMaterial = $state<MissingQuestionsItem[]>([]);

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

    constructor(student: User, materials: MaterialWithProgress[], missingQuestionsByMaterial: MissingQuestionsItem[]) {
        super();
        this.student = student;
        this.materials = materials;
        this.missingQuestionsByMaterial = missingQuestionsByMaterial;
    }
}

/**
 * Student Register State (Form)
 */
export class StudentRegisterState extends FormState<any> {
    constructor() {
        super({
            name: "",
            email: "",
            password: "",
            password_confirmation: "",
        });
    }

    async submit(onSuccess?: () => void) {
        await this.submitForm("post", ROUTES.ADMIN.STUDENTS.INDEX, {
            onSuccess: () => {
                if (onSuccess) onSuccess();
                this.form.reset();
            },
        });
    }
}

/**
 * Student Import State (Form)
 */
export class StudentImportState extends FormState<any> {
    constructor() {
        super({
            excel_file: null,
        });
    }

    async submit() {
        await this.submitForm("post", ROUTES.ADMIN.STUDENTS.IMPORT);
    }

    handleFileChange(e: any) {
        this.form.excel_file = e.target.files[0];
    }
}
