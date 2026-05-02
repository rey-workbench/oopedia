import { router } from '@inertiajs/svelte';
import { confirmDelete } from '@/utils/confirmDelete';
import { BaseState } from '@/states/BaseState.svelte';
import { FormState } from '@/states/FormState.svelte';
import { ROUTES } from '@/utils/route';
import { handleImagePreview } from '@/utils/imagePreview';
import type { Material } from '@/types';

/**
 * Material List State
 */
export class MaterialListState extends BaseState {
    materials = $state<Material[]>([]);
    search = $state('');

    constructor(materials: Material[], search: string) {
        super();
        this.hydrate({ materials, search });
    }

    public handleSearch() {
        router.get(
            ROUTES.ADMIN.MATERIALS.INDEX,
            { search: this.search },
            { preserveState: true, replace: true }
        );
    }

    public handleDelete(id: number) {
        confirmDelete(
            ROUTES.ADMIN.MATERIALS.DELETE(id),
            'Hapus materi ini secara permanen dari basis data?'
        );
    }
}

/**
 * Material Form State (Create/Edit)
 */
export class MaterialFormState extends FormState<{
    title: string;
    content: string;
    module_id: string | null;
    is_final_project: boolean;
    cover_image: File | null;
}> {
    material = $state<Material | null>(null);
    coverPreview = $state<string | null>(null);

    constructor(material: Material | null) {
        super(MaterialFormState.prepareInitialValues(material), {
            isEdit: !!material,
            showSuccessToast: 'Materi berhasil disimpan!',
            showErrorToast: true,
        });

        this.material = material;
        this.initializeCoverPreview();
    }

    private static prepareInitialValues(material: Material | null) {
        return {
            title: material?.title ?? '',
            content: material?.content ?? '',
            module_id: material?.module_id ?? null,
            is_final_project: !!material?.is_final_project,
            cover_image: null,
        };
    }

    private initializeCoverPreview() {
        const coverMedia = this.material?.media?.find((m) => (m as any).media_type === 'image');
        if (coverMedia) {
            this.coverPreview = (coverMedia as any).media_url;
        }
    }

    public onImageChange(e: Event) {
        handleImagePreview(e, this.form, 'cover_image', (url: string) => {
            this.coverPreview = url;
        });
    }

    public async submit() {
        const url =
            this.isEdit && this.material
                ? ROUTES.ADMIN.MATERIALS.UPDATE(this.material.id)
                : ROUTES.ADMIN.MATERIALS.INDEX;

        await this.submitForm('post', url, {
            forceFormData: true,
            ...(this.isEdit ? { _method: 'put' } : {}),
        });
    }
}
