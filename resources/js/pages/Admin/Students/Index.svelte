<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Card from "@/components/ui/Card.svelte";
    import Button from "@/components/ui/Button.svelte";
    import StudentList from "@/components/Admin/Students/StudentList.svelte";
    import StudentRegisterModal from "@/components/Admin/Students/StudentRegisterModal.svelte";
    import { UserPlus, FileSpreadsheet, Search } from "lucide-svelte";
    import { StudentListState } from "@/states/Admin/StudentState.svelte";
    import { ROUTES } from "@/utils/route";

    export let students = {}; // paginated object

    let search =
        new URLSearchParams(window.location.search).get("search") || "";
    let openModal = false;

    const state = new StudentListState(students, search);
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
                    href={ROUTES.ADMIN.STUDENTS.IMPORT}
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
                            class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary-600 transition-colors"
                        />
                        <input
                            type="text"
                            bind:value={state.search}
                            on:input={state.handleSearch}
                            placeholder="Cari mahasiswa..."
                            class="w-full md:w-64 pl-12 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-primary-100 focus:border-primary-600 transition-all outline-none"
                        />
                    </div>
                </div>
            </div>

            <StudentList {state} on:open-modal={() => (openModal = true)} />
        </Card>

        <StudentRegisterModal
            bind:show={openModal}
            on:close={() => (openModal = false)}
        />
    </div>
</App>
