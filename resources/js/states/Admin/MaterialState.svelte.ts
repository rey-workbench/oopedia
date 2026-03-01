import { router } from '@inertiajs/svelte';
import { confirmDelete } from '@/utils/confirmDelete';
import { BaseState } from '@/states/BaseState.svelte';
import { FormState } from '@/states/FormState.svelte';
import { ROUTES } from '@/utils/route';
import { handleImagePreview } from '@/utils/imagePreview';
import type { Material, SubMaterial, JenisKonten } from '@/types';

/**
 * Material List State
 */
export class MaterialListState extends BaseState {
    materials = $state<Material[]>([]);
    search = $state('');

    constructor(materials: Material[], search: string) {
        super();
        this.materials = materials;
        this.search = search;
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
    description: string | null;
    content: string;
    level: string;
    cover_image: File | null;
    status: string;
}> {
    material = $state<Material | null>(null);
    coverPreview = $state<string | null>(null);

    constructor(material: Material | null) {
        super(
            {
                title: material ? material.title : '',
                description: material ? material.description : '',
                content: material ? material.content || '' : '',
                level: material ? material.level : 'beginner',
                cover_image: null,
                status: material ? material.status : 'draft',
            },
            !!material
        );

        this.material = material;

        if (this.material && this.material.cover_image) {
            this.coverPreview = `/storage/${this.material.cover_image}`;
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
        this.material = material;
        this.subMaterials = subMaterials;
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
    jenis_konten: JenisKonten | string;
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
            !!submaterial
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

    setJenisKonten(jenis_konten: JenisKonten | string) {
        this.form.jenis_konten = jenis_konten;
    }
}
