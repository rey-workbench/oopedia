/**
 * resources/js/types/forms/AuthForms.ts
 */

export interface LoginForm {
    email: string;
    password: string;
    remember: boolean;
}

export interface RegisterForm {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
    nim: string;
    class: string;
}

export interface ProfileForm {
    name: string;
    email: string;
    password?: string;
    password_confirmation?: string;
    nim?: string;
    phone?: string;
    class?: string;
}
