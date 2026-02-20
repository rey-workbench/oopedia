import { useForm, router } from "@inertiajs/svelte";
import { get } from "svelte/store";
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
     * Standard submit wrapper - Simple, DRY, and Robust.
     */
    protected submitForm(method: 'post' | 'put' | 'patch' | 'delete', url: string, options: any = {}) {
        const f = this.form as any;

        // 1. Try form-specific methods (post, put, etc. or general submit)
        const submitFn = f[method] || (f.submit ? (u: string, o: any) => f.submit(method, u, o) : null);

        if (typeof submitFn === 'function') {
            return submitFn(url, options);
        }

        // 2. Fallback: Use router directly (Handy when useForm returns a pure store)
        const data = typeof f.subscribe === 'function' ? get(this.form) : (f.data || f);
        return router[method](url, data, options);
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
