import { useForm } from "@inertiajs/svelte";
import { get } from "svelte/store";

export class LoginState {
    form: any;

    constructor() {
        this.form = useForm({
            email: "",
            password: "",
        });
    }

    submit() {
        (get(this.form) as any).post("/login");
    }

    submitAsGuest() {
        (get(this.form) as any)
            .transform((data: any) => ({ ...data, is_guest: true }))
            .post("/login");
    }
}
