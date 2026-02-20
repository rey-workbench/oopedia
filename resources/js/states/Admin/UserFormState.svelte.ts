import { useForm } from "@inertiajs/svelte";
import { get } from "svelte/store";

export class UserFormState {
    form;
    isEdit = $state(false);
    user = $state<any>(null);

    constructor(user: any) {
        this.user = user;
        this.isEdit = !!user;

        this.form = useForm({
            name: user ? user.name : "",
            email: user ? user.email : "",
            password: "",
            role_id: user ? user.role_id : 3,
            gamification_level: user?.gamification
                ? user.gamification.current_level
                : "Pemula",
            xp: user?.gamification ? user.global_xp : 0,
        });
    }

    submit() {
        if (this.isEdit) {
            (get(this.form) as any).put(`/admin/users/${this.user.id}`);
        } else {
            (get(this.form) as any).post("/admin/users");
        }
    }
}
