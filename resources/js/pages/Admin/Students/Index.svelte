<script>
    import App from "../../../layouts/App.svelte";
    import PageHeader from "../../../components/ui/PageHeader.svelte";
    import Card from "../../../components/ui/Card.svelte";
    import Button from "../../../components/ui/Button.svelte";
    import Input from "../../../components/ui/Input.svelte";
    import ProgressBar from "../../../components/ui/ProgressBar.svelte";
    import Modal from "../../../components/ui/Modal.svelte";
    import { router, useForm } from "@inertiajs/svelte";
    import { confirmDelete } from "../../../utils/confirmDelete";
    import {
        UserPlus,
        FileSpreadsheet,
        Search,
        GraduationCap,
        Terminal,
        LineChart,
        UserMinus,
        X,
    } from "lucide-svelte";

    export let students = {}; // paginated object

    let search =
        new URLSearchParams(window.location.search).get("search") || "";
    let openModal = false;

    const form = useForm({
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
    });

    function handleSearch() {
        router.get(
            "/admin/students",
            { search },
            { preserveState: true, replace: true },
        );
    }

    function handleDelete(id) {
        confirmDelete(`/admin/students/${id}`, "Hapus data mahasiswa ini?");
    }

    function handleSubmit() {
        $form.post("/admin/students", {
            onSuccess: () => {
                openModal = false;
                form.reset();
            },
        });
    }
</script>

<App title="Data Mahasiswa">
    <div class="space-y-12">
        <PageHeader
            title="Database Mahasiswa"
            subtitle="Pantau progres dan aktivitas belajar seluruh mahasiswa terdaftar."
        >
            <div slot="actions" class="flex flex-wrap items-center gap-4">
                <Button
                    on:click={() => (openModal = true)}
                    variant="primary"
                    icon={UserPlus}>Daftarkan Mahasiswa</Button
                >
                <Button
                    href="/admin/students/import"
                    variant="success"
                    icon={FileSpreadsheet}>Impor Excel</Button
                >
            </div>
        </PageHeader>

        <Card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
            <div
                class="flex flex-col md:flex-row justify-between items-center gap-6 w-full px-6 py-4 border-b border-slate-50"
            >
                <p
                    class="text-[10px] font-bold uppercase tracking-widest text-slate-400"
                >
                    Registri Subjek
                </p>
                <div class="w-full md:w-auto">
                    <div class="relative group">
                        <Search
                            size={18}
                            class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition-colors"
                        />
                        <input
                            type="text"
                            bind:value={search}
                            on:input={handleSearch}
                            placeholder="Cari mahasiswa..."
                            class="w-full md:w-64 pl-12 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-100 focus:border-blue-600 transition-all outline-none"
                        />
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr>
                            <th
                                class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                                >Identitas Mahasiswa</th
                            >
                            <th
                                class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                                >Akses Email</th
                            >
                            <th
                                class="p-6 text-center text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                                >Aktivitas Soal</th
                            >
                            <th
                                class="p-6 text-center text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                                >Integrasi Progres</th
                            >
                            <th
                                class="p-6 text-right text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                                >Aksi</th
                            >
                        </tr>
                    </thead>
                    <tbody>
                        {#if !students.data || students.data.length === 0}
                            <tr>
                                <td colspan="5" class="p-20 text-center">
                                    <div
                                        class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6"
                                    >
                                        <GraduationCap
                                            size={32}
                                            strokeWidth={1.5}
                                            class="text-slate-200"
                                        />
                                    </div>
                                    <h3
                                        class="text-xl font-bold uppercase tracking-widest text-slate-900 mb-2"
                                    >
                                        Tidak Ada Mahasiswa Terdaftar
                                    </h3>
                                    <p
                                        class="text-slate-400 text-sm max-w-xs mx-auto mb-8"
                                    >
                                        Silakan daftarkan mahasiswa secara
                                        manual atau impor melalui protokol
                                        Excel.
                                    </p>
                                    <div class="flex justify-center gap-4">
                                        <Button
                                            on:click={() => (openModal = true)}
                                            variant="primary"
                                            icon={UserPlus}
                                            >Daftar Individu</Button
                                        >
                                        <Button
                                            href="/admin/students/import"
                                            variant="outline"
                                            icon={FileSpreadsheet}
                                            >Unggah Dataset</Button
                                        >
                                    </div>
                                </td>
                            </tr>
                        {:else}
                            {#each students.data as student (student.id)}
                                <tr
                                    class="group hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0"
                                >
                                    <td
                                        class="px-6 py-6 border-l-4 border-transparent group-hover:border-blue-600"
                                    >
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold shadow-lg shadow-slate-200 uppercase text-xs"
                                            >
                                                {student.name.charAt(0)}
                                            </div>
                                            <div
                                                class="font-bold text-slate-900 tracking-widest"
                                            >
                                                {student.name}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6">
                                        <span
                                            class="text-xs font-bold text-slate-400 underline decoration-slate-200 underline-offset-4"
                                            >{student.email}</span
                                        >
                                    </td>
                                    <td class="px-6 py-6 text-center">
                                        <div
                                            class="inline-flex items-center gap-2 px-3 py-1 bg-slate-100 rounded-full"
                                        >
                                            <Terminal
                                                size={10}
                                                class="text-blue-500"
                                            />
                                            <span
                                                class="text-[10px] font-bold text-slate-700"
                                                >{student.total_answered_questions ??
                                                    0}</span
                                            >
                                        </div>
                                    </td>
                                    <td class="px-6 py-6">
                                        <div class="w-40 mx-auto space-y-2">
                                            <div
                                                class="flex justify-between items-center text-[10px] font-bold uppercase tracking-widest text-slate-400 px-1"
                                            >
                                                <span>Sinkronisasi Progres</span
                                                >
                                                <span
                                                    >{student.overall_progress}%</span
                                                >
                                            </div>
                                            <ProgressBar
                                                value={student.overall_progress}
                                                size="xs"
                                                color="bg-blue-600"
                                            />
                                        </div>
                                    </td>
                                    <td class="px-6 py-6">
                                        <div class="flex justify-end gap-2">
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                href={`/admin/students/${student.id}/progress`}
                                                icon={LineChart}
                                            />
                                            <button
                                                on:click={() =>
                                                    handleDelete(student.id)}
                                                class="p-2 rounded-xl font-bold uppercase tracking-widest transition-all duration-300 flex items-center justify-center gap-2 hover:bg-slate-100 text-slate-300 hover:text-rose-500 text-xs"
                                            >
                                                <UserMinus
                                                    size={18}
                                                    strokeWidth={2.5}
                                                />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            {/each}
                        {/if}
                    </tbody>
                </table>

                <!-- Simple Pagination -->
                {#if students.links && students.links.length > 3}
                    <div
                        class="p-6 border-t border-slate-100 flex justify-center bg-slate-50/30"
                    >
                        <div class="flex gap-1">
                            {#each students.links as link}
                                {#if link.url}
                                    <Button
                                        href={link.url}
                                        variant={link.active
                                            ? "primary"
                                            : "ghost"}
                                        size="sm"
                                        class={!link.active && !link.url
                                            ? "opacity-50 cursor-not-allowed"
                                            : ""}
                                    >
                                        {@html link.label}
                                    </Button>
                                {:else}
                                    <span
                                        class="px-3 py-2 text-slate-400 text-xs font-bold"
                                        >{@html link.label}</span
                                    >
                                {/if}
                            {/each}
                        </div>
                    </div>
                {/if}
            </div>
        </Card>

        <!-- Modal manually implemented or use reusable Modal component -->
        <Modal bind:show={openModal} on:close={() => (openModal = false)}>
            <div class="bg-blue-600 px-8 py-10 text-white relative">
                <div class="absolute right-8 top-10">
                    <button
                        on:click={() => (openModal = false)}
                        class="text-blue-200 hover:text-white"
                    >
                        <X size={20} />
                    </button>
                </div>
                <h6 class="text-xl font-bold tracking-widest mb-1 uppercase">
                    Inisialisasi Otentikasi
                </h6>
                <p
                    class="text-[10px] font-bold text-blue-100/60 uppercase tracking-widest"
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
                        class="flex-1 py-4 shadow-xl shadow-blue-500/20"
                        icon={UserPlus}
                        disabled={$form.processing}
                    >
                        {#if $form.processing}Mengotorisasi...{:else}Otorisasi
                            Mahasiswa{/if}
                    </Button>
                </div>
            </form>
        </Modal>
    </div>
</App>
