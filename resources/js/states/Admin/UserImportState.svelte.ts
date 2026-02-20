import { useForm } from "@inertiajs/svelte";

export class UserImportState {
    form;

    constructor() {
        this.form = useForm({
            excel_file: null,
        });
    }

    submit() {
        this.form.post("/admin/users/import");
    }

    handleFileChange(e: any) {
        this.form.excel_file = e.target.files[0];
    }
}
