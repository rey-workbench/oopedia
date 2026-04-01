import { FormState, type FormStateOptions } from '@/states/FormState.svelte';
import { ROUTES } from '@/utils/route';

/**
 * Login State
 */
export class LoginState extends FormState<{ email: string; password: string; is_guest?: boolean }> {
    constructor(options?: FormStateOptions) {
        super(
            {
                email: '',
                password: '',
            },
            options
        );
    }

    async submit() {
        await this.submitForm('post', ROUTES.AUTH.LOGIN);
    }

    async submitAsGuest() {
        this.form.is_guest = true;
        await this.submitForm('post', ROUTES.AUTH.LOGIN);
    }
}

/**
 * Register State
 */
export class RegisterState extends FormState<{
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
    register_as_admin: boolean;
}> {
    constructor(options?: FormStateOptions) {
        super(
            {
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
                register_as_admin: false,
            },
            options
        );
    }

    async submit() {
        await this.submitForm('post', ROUTES.AUTH.REGISTER);
    }
}
