import { useForm } from "@inertiajs/svelte";
import { BaseState } from "./BaseState.svelte";

/**
 * FormState - Standardized state class for handling forms
 * Extends BaseState to include Inertia form management.
 */
export class FormState<TForm extends Record<string, any>> extends BaseState {
    public form;
    public isEdit = $state(false);

    constructor(initialValues: TForm, isEdit: boolean = false) {
        super();
        this.isEdit = isEdit;
        this.form = useForm(initialValues);
    }

    /**
     * Standard submit wrapper
     */
    protected async submitForm(method: 'post' | 'put' | 'patch' | 'delete', url: string, options: any = {}) {
        return new Promise((resolve, reject) => {
            this.form[method](url, {
                ...options,
                onSuccess: (params: any) => {
                    if (options.onSuccess) options.onSuccess(params);
                    resolve(params);
                },
                onError: (err: any) => {
                    if (options.onError) options.onError(err);
                    reject(err);
                }
            } as any);
        });
    }

    /**
     * Get form processing state
     */
    get processing() {
        return this.form.processing;
    }

    /**
     * Get form errors
     */
    get formErrors() {
        return this.form.errors;
    }
}
