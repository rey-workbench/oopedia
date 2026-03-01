import { FormState } from "@/states/FormState.svelte";
import type { LearningProfile } from "@/types";

type ProfileForm = {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
};

export class ProfileState extends FormState<ProfileForm> {
    personalization = $state<LearningProfile | null>(null);

    constructor(personalization: LearningProfile) {
        super({ name: "", email: "", password: "", password_confirmation: "" });
        if (this.user) {
            this.form.name = this.user.name ?? "";
            this.form.email = this.user.email ?? "";
        }
        this.personalization = personalization;
    }

    submit() {
        this.submitForm('post', '/mahasiswa/profile', {
            _method: 'PUT',
            onSuccess: () => {
                this.form.password = "";
                this.form.password_confirmation = "";
            },
        });
    }
}
