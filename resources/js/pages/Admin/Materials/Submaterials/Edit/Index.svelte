<script>
    import App from "../../../../../layouts/App.svelte";
    import PageHeader from "../../../../../components/ui/PageHeader.svelte";
    import Card from "../../../../../components/ui/Card.svelte";
    import Button from "../../../../../components/ui/Button.svelte";
    import Input from "../../../../../components/ui/Input.svelte";
    import QuillEditor from "../../../../../components/ui/QuillEditor.svelte";
    import { useForm } from "@inertiajs/svelte";
    import { ArrowLeft, RefreshCw } from "lucide-svelte";

    export let material;
    export let submaterial;

    const form = useForm({
        title: submaterial.title,
        content: submaterial.content,
        jenis_konten: submaterial.jenis_konten,
        order: submaterial.order,
    });

    function handleSubmit() {
        $form.put(
            `/admin/materials/${material.id}/submaterials/${submaterial.id}`,
        );
    }
</script>

<App title={`Edit Sub-Materi: ${submaterial.title}`}>
    <div class="space-y-12">
        <PageHeader
            title="Modifikasi Unit"
            subtitle={`Memperbarui konten pembelajaran untuk unit sub-materi: ${submaterial.title}`}
        >
            <div slot="actions">
                <Button
                    href={`/admin/materials/${material.id}/submaterials`}
                    variant="ghost"
                    icon={ArrowLeft}>KEMBALI KE DAFTAR</Button
                >
            </div>
        </PageHeader>

        <div class="max-w-4xl mx-auto">
            <Card
                padding="p-0"
                class="overflow-hidden border-slate-100 shadow-2xl"
            >
                <div
                    slot="header"
                    class="bg-blue-600 px-8 py-6 text-white text-center"
                >
                    <h6
                        class="text-lg font-bold tracking-widest uppercase mb-0"
                    >
                        Update Konten Unit
                    </h6>
                </div>

                <form
                    on:submit|preventDefault={handleSubmit}
                    class="p-8 space-y-8"
                >
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2 space-y-2">
                            <label
                                for="title"
                                class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                                >Judul Sub-Materi</label
                            >
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
                                >Urutan Tampil</label
                            >
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
                                    on:click={() => ($form.jenis_konten = type)}
                                    class={`py-3 px-4 rounded-2xl border-2 font-bold uppercase tracking-widest text-[10px] transition-all
                    ${$form.jenis_konten === type ? "border-blue-600 bg-blue-50 text-blue-600" : "border-slate-100 bg-slate-50 text-slate-400"}`}
                                >
                                    {type}
                                </button>
                            {/each}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <span
                            class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                            >Materi Pembelajaran (Rich Text)</span
                        >

                        <QuillEditor
                            bind:content={$form.content}
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

                    <div class="pt-6">
                        <Button
                            type="submit"
                            variant="primary"
                            class="w-full py-4 shadow-xl shadow-blue-500/20"
                            icon={RefreshCw}
                            disabled={$form.processing}
                        >
                            {#if $form.processing}MEMPERBARUI...{:else}SIMPAN
                                PERUBAHAN UNIT{/if}
                        </Button>
                    </div>
                </form>
            </Card>
        </div>
    </div>
</App>
