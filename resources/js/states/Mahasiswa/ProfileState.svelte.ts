import { FormState } from '@/states/FormState.svelte';
import type { StudentProfile, ProfileForm } from '@/types';

export class ProfileState extends FormState<ProfileForm> {
    personalization = $state<StudentProfile | null>(null);

    constructor(personalization: StudentProfile | null) {
        super({ name: '', email: '', password: '', password_confirmation: '' });
        this.hydrate({ personalization });
        if (this.user) {
            this.form.name = this.user.name ?? '';
            this.form.email = this.user.email ?? '';
        }
    }

    submit() {
        this.submitForm('post', '/mahasiswa/profile', {
            _method: 'PUT',
            onSuccess: () => {
                this.form.password = '';
                this.form.password_confirmation = '';
            },
        });
    }
}
