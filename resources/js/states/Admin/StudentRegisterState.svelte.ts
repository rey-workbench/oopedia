import { useForm } from "@inertiajs/svelte";

export class StudentRegisterState {
    form;

    constructor() {
        this.form = useForm({
            name: "",
            email: "",
            password: "",
            password_confirmation: "",
        });
    }

    submit(onSuccess: () => void) {
        this.form.post("/admin/students", {
            onSuccess: () => {
                onSuccess();
                this.form.reset();
            },
        });
    }
}
