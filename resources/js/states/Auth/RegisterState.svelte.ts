import { useForm } from "@inertiajs/svelte";
import { get } from "svelte/store";

export class RegisterState {
    form: any;

    constructor() {
        this.form = useForm({
            name: "",
            email: "",
            password: "",
            password_confirmation: "",
            register_as_admin: false,
        });
    }

    submit() {
        (get(this.form) as any).post("/register");
    }
}
