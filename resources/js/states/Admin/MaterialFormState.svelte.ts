import { FormState } from "@/states/FormState.svelte";
import { handleImagePreview } from "@/utils/imagePreview";
import { ROUTES } from "@/utils/route";

export class MaterialFormState extends FormState<any> {
    material = $state<any>(null);
    coverPreview = $state<string | null>(null);

    constructor(material: any) {
        super({
            title: material ? material.title : "",
            description: material ? material.description : "",
            level: material ? material.level : "beginner",
            cover_image: null,
            status: material ? material.status : "draft",
        }, !!material);

        this.material = material;

        // Initialize cover preview if editing
        if (this.material && this.material.cover_image) {
            this.coverPreview = `/storage/${this.material.cover_image}`;
        }
    }

    onImageChange(e: any) {
        handleImagePreview(
            e,
            this.form,
            "cover_image",
            (url: any) => {
                this.coverPreview = url;
            }
        );
    }

    async submit() {
        const url = this.isEdit
            ? ROUTES.ADMIN.MATERIALS.UPDATE(this.material.id)
            : ROUTES.ADMIN.MATERIALS.INDEX;

        const method = this.isEdit ? 'post' : 'post'; // Inertia post with _method put for file uploads

        await this.submitForm('post', url, {
            forceFormData: true,
            ...(this.isEdit ? { _method: 'put' } : {})
        });
    }
}
