import { router } from '@inertiajs/svelte';
import { confirmDelete } from '@/utils/confirmDelete';
import { BaseState } from '@/states/BaseState.svelte';
import { FormState } from '@/states/FormState.svelte';
import { ROUTES } from '@/utils/route';
import { handleImagePreview } from '@/utils/imagePreview';
import type { Material, SubMaterial, ContentCategory } from '@/types';

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

    handleSearch() {
        router.get(
            ROUTES.ADMIN.MATERIALS.INDEX,
            { search: this.search },
            { preserveState: true, replace: true }
        );
    }

    handleDelete(id: number) {
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
        super(
            {
                title: material ? material.title : '',
                content: material ? material.content || '' : '',
                module_id: material ? material.module_id : null,
                is_final_project: material ? !!material.is_final_project : false,
                cover_image: null,
            },
            {
                isEdit: !!material,
                showSuccessToast: 'Materi berhasil disimpan!',
                showErrorToast: true,
            }
        );

        this.material = material;

        const coverMedia = this.material?.media?.find((m) => m.media_type === 'image');
        if (coverMedia) {
            this.coverPreview = coverMedia.media_url;
        }
    }

    onImageChange(e: Event) {
        handleImagePreview(e, this.form, 'cover_image', (url: string) => {
            this.coverPreview = url;
        });
    }

    async submit() {
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

/**
 * Submaterial List State
 */
export class SubmaterialListState extends BaseState {
    material = $state<Material | null>(null);
    subMaterials = $state<SubMaterial[]>([]);

    constructor(material: Material, subMaterials: SubMaterial[]) {
        super();
        this.hydrate({ material, subMaterials });
    }

    handleDelete(id: number) {
        confirmDelete(
            ROUTES.ADMIN.MATERIALS.SUBMATERIALS.EDIT(this.material!.id, id).replace('/edit', ''),
            'Hapus sub-materi ini?'
        );
    }
}

/**
 * Submaterial Form State (Create/Edit)
 */
export class SubmaterialFormState extends FormState<{
    title: string;
    content: string;
    jenis_konten: ContentCategory | string;
    order: number;
    material_id: number | string;
}> {
    material = $state<Material | null>(null);
    submaterial = $state<SubMaterial | null>(null);

    constructor(material: Material, submaterial: SubMaterial | null) {
        super(
            {
                title: submaterial ? submaterial.title : '',
                content: submaterial ? submaterial.content : '',
                jenis_konten: submaterial ? submaterial.jenis_konten : 'teori',
                order: submaterial
                    ? submaterial.order
                    : material?.sub_materials
                      ? material.sub_materials.length + 1
                      : 1,
                material_id: material ? material.id : '',
            },
            {
                isEdit: !!submaterial,
                showSuccessToast: 'Sub materi berhasil disimpan!',
                showErrorToast: true,
            }
        );

        this.material = material;
        this.submaterial = submaterial;
    }

    async submit() {
        const url = this.isEdit
            ? ROUTES.ADMIN.MATERIALS.SUBMATERIALS.EDIT(
                  this.material!.id,
                  this.submaterial!.id
              ).replace('/edit', '')
            : ROUTES.ADMIN.MATERIALS.SUBMATERIALS.INDEX(this.material!.id);

        await this.submitForm(this.isEdit ? 'put' : 'post', url);
    }

    setJenisKonten(jenis_konten: ContentCategory | string) {
        this.form.jenis_konten = jenis_konten;
    }
}
