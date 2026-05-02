import { FormState } from '@/states/FormState.svelte';
import { ROUTES } from '@/utils/route';
import type { ProfileForm } from '@/types';

export class AdminProfileState extends FormState<ProfileForm> {
    constructor() {
        super(AdminProfileState.createInitialFields());
        this.initializeFromUser();
    }

    private static createInitialFields(): ProfileForm {
        return {
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
        };
    }

    private initializeFromUser() {
        if (this.user) {
            this.form.name = this.user.name ?? '';
            this.form.email = this.user.email ?? '';
        }
    }

    public submit() {
        this.submitForm('post', ROUTES.ADMIN.PROFILE, {
            _method: 'PUT',
            onSuccess: () => this.resetPasswordFields(),
        });
    }

    private resetPasswordFields() {
        this.form.password = '';
        this.form.password_confirmation = '';
    }
}
