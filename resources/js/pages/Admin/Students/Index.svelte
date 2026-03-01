<script>
    import App from "@/layouts/App.svelte";
    import Button from "@/components/ui/Button.svelte";
    import DataTable from "@/components/shared/DataTable.svelte";
    import ProgressBar from "@/components/ui/ProgressBar.svelte";
    import EmptyState from "@/components/ui/EmptyState.svelte";
    import Pagination from "@/components/ui/Pagination.svelte";
    import UserAvatar from "@/components/ui/UserAvatar.svelte";
    import Modal from "@/components/ui/Modal.svelte";
    import Input from "@/components/ui/Input.svelte";
    import PageHeader from "@/components/shared/PageHeader.svelte";
    import { ROUTES } from "@/utils/route";
    import {
        StudentListState,
        StudentRegisterState,
    } from "@/states/Admin/StudentState.svelte";

    export let students = {}; // paginated object

    let search =
        new URLSearchParams(window.location.search).get("search") || "";
    let openModal = false;

    const state = new StudentListState(students, search);

    const registerState = new StudentRegisterState();
    $: registerForm = registerState.registerForm;

    $: columns = [
        { key: "identity", label: "Identitas Mahasiswa", align: "left" },
        { key: "email", label: "Akses Email", align: "left" },
        { key: "activity", label: "Aktivitas Soal", align: "center" },
        { key: "progress", label: "Integrasi Progres", align: "center" },
        { key: "actions", label: "Aksi", align: "right" },
    ];
</script>

<App title="Data Mahasiswa">
    <div class="space-y-12">
        <PageHeader
            title="Database Mahasiswa"
            subtitle="Pantau progres dan aktivitas belajar seluruh mahasiswa terdaftar."
        >
            {#snippet actions()}
                <Button
                    on:click={() => (openModal = true)}
                    variant="primary"
                    icon={UserPlus}>Daftarkan Mahasiswa</Button
                >
                <Button
                    href={ROUTES.ADMIN.STUDENTS.IMPORT}
                    variant="success"
                    icon={FileSpreadsheet}>Impor Excel</Button
                >
            {/snippet}
        </PageHeader>

        <DataTable
            title="Registri Subjek"
            items={state.students.data || []}
            bind:search
            onSearch={() => {
                state.search = search;
                state.handleSearch();
            }}
            searchPlaceholder="Cari mahasiswa..."
            {columns}
        >
            {#snippet empty()}
                <EmptyState
                    title="Tidak Ada Mahasiswa Terdaftar"
                    description="Silakan daftarkan mahasiswa secara manual atau impor melalui protokol Excel."
                    icon={GraduationCap}
                >
                    <div class="flex justify-center gap-4">
                        <Button
                            on:click={() => (openModal = true)}
                            variant="primary"
                            icon={UserPlus}>Daftar Individu</Button
                        >
                        <Button
                            href={ROUTES.ADMIN.STUDENTS.IMPORT}
                            variant="outline"
                            icon={FileSpreadsheet}>Unggah Dataset</Button
                        >
                    </div>
                </EmptyState>
            {/snippet}

            {#snippet row(student)}
                <td
                    class="px-6 py-6 border-l-4 border-transparent group-hover:border-primary-600"
                >
                    <div class="flex items-center gap-4">
                        <UserAvatar name={student.name} />
                        <div class="font-bold text-slate-900 tracking-widest">
                            {student.name}
                        </div>
                    </div>
                </td>
                <td class="px-6 py-6">
                    <span
                        class="text-xs font-bold text-slate-400 underline decoration-slate-200 underline-offset-4"
                    >
                        {student.email}
                    </span>
                </td>
                <td class="px-6 py-6 text-center">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 bg-slate-100 rounded-full"
                    >
                        <Terminal size={10} class="text-primary-600" />
                        <span class="text-[10px] font-bold text-slate-700"
                            >{student.total_answered_questions ?? 0}</span
                        >
                    </div>
                </td>
                <td class="px-6 py-6">
                    <div class="w-40 mx-auto space-y-2">
                        <div
                            class="flex justify-between items-center text-[10px] font-bold uppercase tracking-widest text-slate-400 px-1"
                        >
                            <span>Sinkronisasi Progres</span>
                            <span>{student.overall_progress}%</span>
                        </div>
                        <ProgressBar
                            value={student.overall_progress}
                            size="xs"
                            color="blue"
                        />
                    </div>
                </td>
                <td class="px-6 py-6">
                    <div class="flex justify-end gap-2">
                        <Button
                            variant="ghost"
                            size="sm"
                            href={ROUTES.ADMIN.STUDENTS.SHOW(student.id)}
                            icon={LineChart}
                        />
                        <Button
                            variant="ghost"
                            size="sm"
                            on:click={() => state.handleDelete(student.id)}
                            icon={UserMinus}
                            class="text-slate-300 hover:text-rose-500"
                        />
                    </div>
                </td>
            {/snippet}
        </DataTable>

        {#if state.students.data && state.students.data.length > 0}
            <div class="mt-6">
                <Pagination links={state.students.links} />
            </div>
        {/if}

        <Modal show={openModal} onclose={() => (openModal = false)}>
            <form
                on:submit|preventDefault={() => registerState.submitRegister()}
                class="space-y-6 p-8"
            >
                <div class="pb-4 border-b border-slate-100">
                    <h3
                        class="text-sm font-bold uppercase tracking-widest text-slate-900"
                    >
                        Registrasi Mahasiswa Baru
                    </h3>
                </div>
                <div class="space-y-2">
                    <label
                        for="reg_name"
                        class="text-[10px] font-bold uppercase text-slate-400 tracking-widest"
                        >Nama Lengkap</label
                    >
                    <Input
                        id="reg_name"
                        bind:value={$registerForm.name}
                        placeholder="Nama mahasiswa"
                        error={$registerForm.errors.name}
                    />
                </div>
                <div class="space-y-2">
                    <label
                        for="reg_email"
                        class="text-[10px] font-bold uppercase text-slate-400 tracking-widest"
                        >Email</label
                    >
                    <Input
                        id="reg_email"
                        type="email"
                        bind:value={$registerForm.email}
                        placeholder="email@mahasiswa.ac.id"
                        error={$registerForm.errors.email}
                    />
                </div>
                <div class="space-y-2">
                    <label
                        for="reg_password"
                        class="text-[10px] font-bold uppercase text-slate-400 tracking-widest"
                        >Password</label
                    >
                    <Input
                        id="reg_password"
                        type="password"
                        bind:value={$registerForm.password}
                        placeholder="••••••••"
                        error={$registerForm.errors.password}
                    />
                </div>
                <div class="space-y-2">
                    <label
                        for="reg_password_confirmation"
                        class="text-[10px] font-bold uppercase text-slate-400 tracking-widest"
                        >Konfirmasi Password</label
                    >
                    <Input
                        id="reg_password_confirmation"
                        type="password"
                        bind:value={$registerForm.password_confirmation}
                        placeholder="••••••••"
                    />
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <Button
                        type="button"
                        variant="ghost"
                        on:click={() => (openModal = false)}>Batal</Button
                    >
                    <Button
                        type="submit"
                        variant="primary"
                        icon={UserPlus}
                        disabled={$registerForm.processing}
                    >
                        {#if $registerForm.processing}<Loader2
                                size={16}
                                class="animate-spin mr-2"
                            />Mendaftarkan...{:else}Daftarkan{/if}
                    </Button>
                </div>
            </form>
        </Modal>
    </div>
</App>
