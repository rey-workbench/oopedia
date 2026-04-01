import { router } from '@inertiajs/svelte';
import { BaseState } from './BaseState.svelte';
import { toasts } from '@/stores/toast';

type FormSubmitOptions = {
    forceFormData?: boolean;
    _method?: string;
    onSuccess?: () => void;
    onError?: (errors: Record<string, string>) => void;
    onFinish?: () => void;
    showSuccessToast?: string | boolean;
    showErrorToast?: boolean;
    [key: string]: unknown;
};

export interface FormStateOptions {
    isEdit?: boolean;
    showSuccessToast?: string | boolean;
    showErrorToast?: boolean;
}

/**
 * FormState - Svelte 5 native form state using $state runes.
 * No Svelte 4 store (useForm) — fully reactive via $state proxy.
 */
export class FormState<TForm extends Record<string, any>> extends BaseState {
    form = $state<
        TForm & { processing: boolean; errors: Record<string, string>; progress: number | null }
    >({} as any);
    isEdit = $state(false);
    private initialValues: TForm;
    protected toastOptions: { showSuccessToast?: string | boolean; showErrorToast?: boolean } = {};

    constructor(
        initialValues: TForm,
        options?: FormStateOptions
    ) {
        super();
        this.isEdit = options?.isEdit ?? false;
        this.initialValues = initialValues;
        this.toastOptions = {
            showSuccessToast: options?.showSuccessToast ?? false,
            showErrorToast: options?.showErrorToast ?? false,
        };
        this.form = {
            ...initialValues,
            processing: false,
            errors: {},
            progress: null,
        } as any;
    }

    /** Reset form fields back to the initial values passed to the constructor. */
    resetForm() {
        Object.assign(this.form, {
            ...this.initialValues,
            errors: {},
            processing: false,
            progress: null,
        });
    }

    /**
     * Submit form via Inertia router. Uses $state.snapshot() to get a
     * plain serialisable copy before sending.
     */
    protected submitForm(
        method: 'post' | 'put' | 'patch' | 'delete',
        url: string,
        options: FormSubmitOptions = {}
    ) {
        this.form.processing = true;
        this.form.errors = {};

        const {
            forceFormData,
            _method,
            onSuccess,
            onError,
            onFinish,
            showSuccessToast = this.toastOptions.showSuccessToast,
            showErrorToast = this.toastOptions.showErrorToast,
            ...routerOptions
        } = options;

        // $state.snapshot() returns a deep plain-object clone of the reactive proxy
        const snapshot = $state.snapshot(this.form) as Record<string, any>;
        const { processing, errors, progress, ...fields } = snapshot;

        if (_method) fields['_method'] = _method;

        let data: FormData | Record<string, any>;
        if (forceFormData) {
            const fd = new FormData();
            Object.entries(fields).forEach(([key, val]) => {
                if (val !== null && val !== undefined) {
                    fd.append(key, val as any);
                }
            });
            data = fd;
        } else {
            data = fields;
        }

        return router[method](url, data as any, {
            ...routerOptions,
            onError: (errs: Record<string, string>) => {
                this.form.errors = errs;
                this.form.processing = false;
                if (showErrorToast) {
                    const firstError = Object.values(errs)[0];
                    toasts.error(firstError || 'Terjadi kesalahan');
                }
                onError?.(errs);
            },
            onSuccess: () => {
                this.form.processing = false;
                if (showSuccessToast) {
                    toasts.success(
                        typeof showSuccessToast === 'string'
                            ? showSuccessToast
                            : 'Berhasil disimpan'
                    );
                }
                onSuccess?.();
            },
            onFinish: () => {
                this.form.processing = false;
                onFinish?.();
            },
        });
    }

    get processing() {
        return this.form.processing;
    }

    get formErrors() {
        return this.form.errors;
    }
}
