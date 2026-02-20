import { FormState } from "@/states/FormState.svelte";
import { ROUTES } from "@/utils/route";

/**
 * Login State
 */
export class LoginState extends FormState<any> {
    constructor() {
        super({
            email: "",
            password: "",
        });
    }

    async submit() {
        await this.submitForm('post', ROUTES.AUTH.LOGIN);
    }

    async submitAsGuest() {
        this.form.transform((data: any) => ({ ...data, is_guest: true }));
        await this.submitForm('post', ROUTES.AUTH.LOGIN);
    }
}

/**
 * Register State
 */
export class RegisterState extends FormState<any> {
    constructor() {
        super({
            name: "",
            email: "",
            password: "",
            password_confirmation: "",
            register_as_admin: false,
        });
    }

    async submit() {
        await this.submitForm('post', ROUTES.AUTH.REGISTER);
    }
}
