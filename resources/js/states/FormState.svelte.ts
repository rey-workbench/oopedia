import { useForm, router } from "@inertiajs/svelte";
import { get } from "svelte/store";
import { BaseState } from "./BaseState.svelte";

type VisitOptions = Parameters<typeof router.visit>[1];
type FormSubmitOptions = Omit<VisitOptions, 'method'> & { [key: string]: unknown };

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
    protected submitForm(method: 'post' | 'put' | 'patch' | 'delete', url: string, options: FormSubmitOptions = {}) {
        // Inertia useForm returns a store-like object; access methods via index
        const f = this.form as Record<string, unknown>;
        const methodFn = f[method];

        // 1. Use the form helper's own method (post, put, patch, delete)
        if (typeof methodFn === 'function') {
            return (methodFn as (url: string, opts: FormSubmitOptions) => void)(url, options);
        }

        // 2. Use the generic submit method if available
        const submitFn = f['submit'];
        if (typeof submitFn === 'function') {
            return (submitFn as (method: string, url: string, opts: FormSubmitOptions) => void)(method, url, options);
        }

        // 3. Fallback: Use router directly
        const data = typeof f['subscribe'] === 'function' ? get(this.form) : (f['data'] ?? f);
        return router[method](url, data as TForm, options);
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
