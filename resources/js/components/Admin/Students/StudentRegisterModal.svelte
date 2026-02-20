<script>
    import Modal from "@/components/ui/Modal.svelte";
    import Button from "@/components/ui/Button.svelte";
    import Input from "@/components/ui/Input.svelte";
    import { X, UserPlus } from "lucide-svelte";
    import { createEventDispatcher } from "svelte";
    import { StudentRegisterState } from "@/states/Admin/StudentState.svelte";

    export let show = false;
    const dispatch = createEventDispatcher();

    const state = new StudentRegisterState();
    const form = state.form;

    function handleSubmit() {
        state.submit(() => {
            dispatch("close");
        });
    }

    function handleClose() {
        dispatch("close");
    }
</script>

<Modal bind:show on:close={handleClose}>
    <div class="px-8 py-10 border-b border-slate-50 relative">
        <div class="absolute right-8 top-10">
            <button
                on:click={handleClose}
                class="text-slate-400 hover:text-slate-900 transition-colors"
            >
                <X size={20} />
            </button>
        </div>
        <h6
            class="text-xl font-bold tracking-widest mb-1 uppercase text-slate-900"
        >
            Inisialisasi Otentikasi
        </h6>
        <p
            class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"
        >
            Daftarkan entitas mahasiswa individu
        </p>
    </div>

    <form on:submit|preventDefault={handleSubmit} class="p-8 space-y-6">
        <div class="space-y-4">
            <div class="space-y-2">
                <label
                    for="name"
                    class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                    >Identitas Lengkap</label
                >
                <Input
                    id="name"
                    bind:value={$form.name}
                    placeholder="Nama lengkap subjek"
                    error={$form.errors.name}
                />
            </div>
            <div class="space-y-2">
                <label
                    for="email"
                    class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                    >Email Elektronik</label
                >
                <Input
                    id="email"
                    type="email"
                    bind:value={$form.email}
                    placeholder="mahasiswa@example.com"
                    error={$form.errors.email}
                />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label
                        for="password"
                        class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                        >Kunci Keamanan</label
                    >
                    <Input
                        id="password"
                        type="password"
                        bind:value={$form.password}
                        placeholder="Minimal 8 karakter"
                        error={$form.errors.password}
                    />
                </div>
                <div class="space-y-2">
                    <label
                        for="password_confirmation"
                        class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                        >Konfirmasi Kunci</label
                    >
                    <Input
                        id="password_confirmation"
                        type="password"
                        bind:value={$form.password_confirmation}
                        placeholder="Verifikasi kunci"
                    />
                </div>
            </div>
        </div>

        <div class="pt-4 flex gap-4">
            <Button
                type="submit"
                variant="primary"
                class="flex-1 py-4 shadow-xl shadow-primary-900/20"
                icon={UserPlus}
                disabled={$form.processing}
            >
                {#if $form.processing}Mengotorisasi...{:else}Otorisasi Mahasiswa{/if}
            </Button>
        </div>
    </form>
</Modal>
