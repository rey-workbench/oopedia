<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import DataForm from "@/components/ui/DataForm.svelte";
    import Input from "@/components/ui/Input.svelte";
    import QuillEditor from "@/components/ui/QuillEditor.svelte";
    import { ArrowLeft, RefreshCw } from "lucide-svelte";
    import { ROUTES } from "@/utils/route";
    import { SubmaterialFormState } from "@/states/Admin/MaterialState.svelte";

    export let material;
    export let submaterial;

    const state = new SubmaterialFormState(material, submaterial);
    const form = state.form;
</script>

<App title={`Edit Sub-Materi: ${submaterial.title}`}>
    <div class="space-y-12">
        <PageHeader
            title="Pembaruan Unit"
            subtitle={`Modifikasi konten pembelajaran untuk unit: ${submaterial.title}`}
        >
            <div slot="actions">
                <Button
                    href={ROUTES.ADMIN.MATERIALS.SUBMATERIALS.INDEX(
                        material.id,
                    )}
                    variant="ghost"
                    icon={ArrowLeft}>KEMBALI KE HIERARKI</Button
                >
            </div>
        </PageHeader>

        <div class="max-w-4xl mx-auto">
            <DataForm
                title="Update Konten Unit"
                onSubmit={() => state.submit()}
                isEdit={true}
                processing={$form.processing}
                submitLabel="SIMPAN PERUBAHAN UNIT"
                submitIcon={RefreshCw}
                cancelHref={ROUTES.ADMIN.MATERIALS.SUBMATERIALS.INDEX(
                    material.id,
                )}
            >
                <!-- SubMaterial Form Fields -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2 space-y-2">
                        <label
                            for="title"
                            class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                        >
                            Judul Sub-Materi
                        </label>
                        <Input
                            id="title"
                            bind:value={$form.title}
                            placeholder="Contoh: Pengenalan Class & Object"
                            required
                            error={$form.errors.title}
                        />
                    </div>

                    <div class="space-y-2">
                        <label
                            for="order"
                            class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                        >
                            Urutan Tampil
                        </label>
                        <Input
                            id="order"
                            type="number"
                            bind:value={$form.order}
                            required
                            error={$form.errors.order}
                        />
                    </div>
                </div>

                <div class="space-y-2">
                    <span
                        class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                        >Jenis Konten Utama</span
                    >
                    <div class="grid grid-cols-3 gap-4">
                        {#each ["teori", "sintaks", "mixed"] as type}
                            <button
                                type="button"
                                on:click={() => state.setJenisKonten(type)}
                                class={`py-3 px-4 rounded-2xl border-2 font-bold uppercase tracking-widest text-[10px] transition-all
                                    ${$form.jenis_konten === type ? "border-primary-600 bg-primary-50 text-primary-600" : "border-slate-100 bg-slate-50 text-slate-400"}`}
                            >
                                {type}
                            </button>
                        {/each}
                    </div>
                </div>

                <div class="space-y-2">
                    <span
                        class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                    >
                        Materi Pembelajaran (Rich Text)
                    </span>
                    <QuillEditor
                        bind:value={$form.content}
                        placeholder="Tuliskan materi pembelajaran secara detail di sini..."
                    />
                    {#if $form.errors.content}
                        <p
                            class="text-[10px] font-bold text-rose-500 uppercase tracking-widest"
                        >
                            {$form.errors.content}
                        </p>
                    {/if}
                </div>
            </DataForm>
        </div>
    </div>
</App>
