import { FormState } from "@/states/FormState.svelte";


type ProfileForm = {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
};

export class ProfileState extends FormState<ProfileForm> {
    personalization = $state<any>(null);

    constructor(personalization: any) {
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
