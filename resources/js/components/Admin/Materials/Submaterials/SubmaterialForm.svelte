<script>
    import Card from "@/components/ui/Card.svelte";
    import Button from "@/components/ui/Button.svelte";
    import Input from "@/components/ui/Input.svelte";
    import QuillEditor from "@/components/ui/QuillEditor.svelte";
    import { Save, RefreshCw } from "lucide-svelte";
    import { SubmaterialFormState } from "@/states/Admin/SubmaterialFormState.svelte";

    export let material;
    export let submaterial = null; // If provided, it's Edit mode

    const state = new SubmaterialFormState(material, submaterial);
    const form = state.form;
</script>

<div class="max-w-4xl mx-auto">
    <Card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
        <div
            slot="header"
            class="bg-primary-600 px-8 py-6 text-white text-center"
        >
            <h6 class="text-lg font-bold tracking-widest uppercase mb-0">
                {state.isEdit
                    ? "Update Konten Unit"
                    : "Konfigurasi Konten Unit"}
            </h6>
        </div>

        <form
            on:submit|preventDefault={() => state.submit()}
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
                            on:click={() => state.setJenisKonten(type)}
                            class={`py-3 px-4 rounded-2xl border-2 font-bold uppercase tracking-widest text-[10px] transition-all
            {$form.jenis_konten === type ? "border-primary-600 bg-primary-50 text-primary-600" : "border-slate-100 bg-slate-50 text-slate-400"}`}
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

            <div class="pt-6">
                <Button
                    type="submit"
                    variant="primary"
                    class="w-full py-4 shadow-xl shadow-primary-900/20"
                    icon={state.isEdit ? RefreshCw : Save}
                    disabled={$form.processing}
                >
                    {#if $form.processing}
                        {state.isEdit ? "MEMPERBARUI..." : "MENYIMPAN..."}
                    {:else}
                        {state.isEdit
                            ? "SIMPAN PERUBAHAN UNIT"
                            : "PUBLIKASIKAN SUB-MATERI"}
                    {/if}
                </Button>
            </div>
        </form>
    </Card>
</div>
