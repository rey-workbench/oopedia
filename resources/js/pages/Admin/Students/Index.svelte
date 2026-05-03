<script lang="ts">
    import App from '@/layouts/App.svelte';
    import {
        UserPlus,
        FileSpreadsheet,
        Terminal,
        UserMinus,
        LineChart,
        GraduationCap,
        Loader2,
    } from 'lucide-svelte';
    import Button from '@/components/ui/Button.svelte';
    import DataTable from '@/components/ui/DataTable.svelte';
    import ProgressBar from '@/components/ui/ProgressBar.svelte';
    import EmptyState from '@/components/ui/EmptyState.svelte';
    import UserAvatar from '@/components/ui/UserAvatar.svelte';
    import Modal from '@/components/ui/Modal.svelte';
    import Input from '@/components/ui/Input.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import { ROUTES } from '@/utils/route';
    import { StudentListState, StudentRegisterState } from '@/states/Admin/StudentState.svelte';
    import { untrack } from 'svelte';

    let { students = {} }: { students: any } = $props(); // paginated object

    let search: string = $state(new URLSearchParams(window.location.search).get('search') || '');
    let openModal: boolean = $state(false);

    const listState = untrack(() => new StudentListState(students, search));

    const registerState = new StudentRegisterState();

    const columns = $derived([
        { key: 'identity', label: 'Identitas Mahasiswa', align: 'left' },
        { key: 'email', label: 'Akses Email', align: 'left' },
        { key: 'activity', label: 'Aktivitas Soal', align: 'center' },
        { key: 'progress', label: 'Integrasi Progres', align: 'center' },
        { key: 'actions', label: 'Aksi', align: 'right' },
    ]);
</script>

<App title="Data Mahasiswa">
    <div class="space-y-12">
        <PageHeader
            id="page-header"
            title="Database Mahasiswa"
            subtitle="Pantau progres dan aktivitas belajar seluruh mahasiswa terdaftar."
        >
            {#snippet actions()}
                <Button
                    id="add-student-btn"
                    onclick={() => (openModal = true)}
                    variant="primary"
                    icon={UserPlus}>Daftarkan Mahasiswa</Button
                >
                <Button
                    id="import-student-btn"
                    href={ROUTES.ADMIN.STUDENTS.IMPORT}
                    variant="success"
                    icon={FileSpreadsheet}>Impor Excel</Button
                >
            {/snippet}
        </PageHeader>

        <div id="student-table">
            <DataTable
                title="Registri Subjek"
                items={listState.students.data || []}
                bind:search
                onsearch={() => {
                    listState.search = search;
                    listState.handleSearch();
                }}
                searchPlaceholder="Cari mahasiswa..."
                {columns}
                links={listState.students.links}
            >
                {#snippet empty()}
                    <EmptyState
                        title="Tidak Ada Mahasiswa Terdaftar"
                        description="Silakan daftarkan mahasiswa secara manual atau impor melalui protokol Excel."
                        icon={GraduationCap}
                    >
                        <div class="flex justify-center gap-4">
                            <Button
                                onclick={() => (openModal = true)}
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

                {#snippet row(student, index)}
                    <td
                        class="group-hover:border-primary-600 border-l-4 border-transparent px-6 py-6"
                    >
                        <div class="flex items-center gap-4">
                            <UserAvatar name={student.name} />
                            <div class="font-bold tracking-widest text-slate-900">
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
                            class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1"
                        >
                            <Terminal size={10} class="text-primary-600" />
                            <span class="text-[10px] font-bold text-slate-700"
                                >{student.total_answered ?? 0}</span
                            >
                        </div>
                    </td>
                    <td class="px-6 py-6">
                        <div class="mx-auto w-40 space-y-2">
                            <div
                                class="flex items-center justify-between px-1 text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                            >
                                <span>Sinkronisasi Progres</span>
                                <span>{student.overall_progress}%</span>
                            </div>
                            <ProgressBar
                                value={student.overall_progress}
                                height="h-2"
                                color="blue"
                            />
                        </div>
                    </td>
                    <td class="px-6 py-6">
                        <div
                            id={index === 0 ? 'student-actions' : undefined}
                            class="flex justify-end gap-2"
                        >
                            <Button
                                id={index === 0 ? 'btn-progress-student' : undefined}
                                variant="ghost"
                                size="sm"
                                href={ROUTES.ADMIN.STUDENTS.SHOW(student.id)}
                                icon={LineChart}
                            />
                            <Button
                                id={index === 0 ? 'btn-delete-student' : undefined}
                                variant="ghost"
                                size="sm"
                                onclick={() => listState.handleDelete(student.id)}
                                icon={UserMinus}
                                class="text-slate-300 hover:text-rose-500"
                            />
                        </div>
                    </td>
                {/snippet}
            </DataTable>
        </div>


        <Modal show={openModal} onclose={() => (openModal = false)}>
            <form
                onsubmit={(e) => {
                    e.preventDefault();
                    registerState.submit(() => (openModal = false));
                }}
                class="space-y-6 p-8"
            >
                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-sm font-bold tracking-widest text-slate-900 uppercase">
                        Registrasi Mahasiswa Baru
                    </h3>
                </div>
                <div class="space-y-2">
                    <label
                        for="reg_name"
                        class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                        >Nama Lengkap</label
                    >
                    <Input
                        id="reg_name"
                        bind:value={registerState.form.name}
                        placeholder="Nama mahasiswa"
                        error={registerState.form.errors['name']}
                    />
                </div>
                <div class="space-y-2">
                    <label
                        for="reg_email"
                        class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                        >Email</label
                    >
                    <Input
                        id="reg_email"
                        type="email"
                        bind:value={registerState.form.email}
                        placeholder="email@mahasiswa.ac.id"
                        error={registerState.form.errors['email']}
                    />
                </div>
                <div class="space-y-2">
                    <label
                        for="reg_password"
                        class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                        >Password</label
                    >
                    <Input
                        id="reg_password"
                        type="password"
                        bind:value={registerState.form.password}
                        placeholder="••••••••"
                        error={registerState.form.errors['password']}
                    />
                </div>
                <div class="space-y-2">
                    <label
                        for="reg_password_confirmation"
                        class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                        >Konfirmasi Password</label
                    >
                    <Input
                        id="reg_password_confirmation"
                        type="password"
                        bind:value={registerState.form.password_confirmation}
                        placeholder="••••••••"
                    />
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <Button type="button" variant="ghost" onclick={() => (openModal = false)}
                        >Batal</Button
                    >
                    <Button
                        type="submit"
                        variant="primary"
                        icon={UserPlus}
                        disabled={registerState.form.processing}
                    >
                        {#if registerState.form.processing}<Loader2
                                size={16}
                                class="mr-2 animate-spin"
                            />Mendaftarkan...{:else}Daftarkan{/if}
                    </Button>
                </div>
            </form>
        </Modal>
    </div>
</App>
