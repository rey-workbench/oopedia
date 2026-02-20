import { useForm } from "@inertiajs/svelte";

export class SubmaterialFormState {
    form;
    isEdit = $state(false);
    material = $state<any>(null);
    submaterial = $state<any>(null);

    constructor(material: any, submaterial: any) {
        this.material = material;
        this.submaterial = submaterial;
        this.isEdit = !!submaterial;

        this.form = useForm({
            title: submaterial ? submaterial.title : "",
            content: submaterial ? submaterial.content : "",
            type: submaterial ? submaterial.type : "text",
            order: submaterial
                ? submaterial.order
                : material
                    ? material.sub_materials.length + 1
                    : 1,
            material_id: material ? material.id : "",
        });
    }

    submit() {
        if (this.isEdit) {
            this.form.put(
                `/admin/materials/${this.material.id}/sub-materials/${this.submaterial.id}`,
            );
        } else {
            this.form.post(
                `/admin/materials/${this.material.id}/sub-materials`,
            );
        }
    }

    setType(type: any) {
        this.form.type = type;
    }
}
