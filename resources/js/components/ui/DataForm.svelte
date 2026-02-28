<script>
    import Card from "@/components/ui/Card.svelte";
    import Button from "@/components/ui/Button.svelte";
    import { Save, Plus } from "lucide-svelte";

    export let title = "Form";
    export let onSubmit = () => {};
    export let isEdit = false;
    export let processing = false;
    export let submitLabel = isEdit ? "SIMPAN PERUBAHAN" : "TAMBAHKAN";
    export let submitIcon = isEdit ? Save : Plus;
    export let cancelHref = null;
    export let cancelLabel = "BATAL";
</script>

<form on:submit|preventDefault={onSubmit} class="space-y-12">
    <Card class="border-slate-100 shadow-2xl">
        <div slot="header">
            <h3 class="text-lg font-bold text-slate-800">
                {title}
            </h3>
        </div>

        <div class="space-y-10 p-6">
            <!-- Main Content -->
            <slot />

            <!-- Footer -->
            <div
                class="pt-6 border-t border-slate-100 flex items-center justify-between gap-4"
            >
                <div class="flex items-center gap-3">
                    <slot name="footer-left">
                        <!-- Default empty block -->
                    </slot>
                </div>

                <div class="flex gap-4">
                    {#if cancelHref}
                        <Button
                            href={cancelHref}
                            variant="ghost"
                            class="text-slate-400 font-bold uppercase text-[10px] tracking-widest"
                        >
                            {cancelLabel}
                        </Button>
                    {/if}
                    <Button
                        type="submit"
                        variant="primary"
                        size="lg"
                        class={`shadow-xl shadow-primary-900/20 ${isEdit ? "bg-primary-600 hover:bg-primary-700" : ""}`}
                        icon={submitIcon}
                        disabled={processing}
                    >
                        {#if processing}
                            {isEdit ? "Menyimpan..." : "Memproses..."}
                        {:else}
                            {submitLabel}
                        {/if}
                    </Button>
                </div>
            </div>
        </div>
    </Card>
</form>
