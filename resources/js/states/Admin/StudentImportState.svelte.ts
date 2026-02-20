import { useForm } from "@inertiajs/svelte";

export class StudentImportState {
    form;

    constructor() {
        this.form = useForm({
            excel_file: null,
        });
    }

    submit() {
        this.form.post("/admin/students/import");
    }

    handleFileChange(e: any) {
        this.form.excel_file = e.target.files[0];
    }
}
