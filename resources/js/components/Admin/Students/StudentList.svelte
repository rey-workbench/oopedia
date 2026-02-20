<script>
    import Button from "@/ui/Button.svelte";
    import ProgressBar from "@/ui/ProgressBar.svelte";
    import {
        Terminal,
        LineChart,
        UserMinus,
        GraduationCap,
        UserPlus,
        FileSpreadsheet,
    } from "lucide-svelte";
    import { createEventDispatcher } from "svelte";
    const dispatch = createEventDispatcher();

    export let state;
</script>

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
            {#if !state.students.data || state.students.data.length === 0}
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
                        <p class="text-slate-400 text-sm max-w-xs mx-auto mb-8">
                            Silakan daftarkan mahasiswa secara manual atau impor
                            melalui protokol Excel.
                        </p>
                        <div class="flex justify-center gap-4">
                            <Button
                                on:click={() => dispatch("open-modal")}
                                variant="primary"
                                icon={UserPlus}>Daftar Individu</Button
                            >
                            <Button
                                href="/admin/students/import"
                                variant="outline"
                                icon={FileSpreadsheet}>Unggah Dataset</Button
                            >
                        </div>
                    </td>
                </tr>
            {:else}
                {#each state.students.data as student (student.id)}
                    <tr
                        class="group hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0"
                    >
                        <td
                            class="px-6 py-6 border-l-4 border-transparent group-hover:border-primary-600"
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
                                <Terminal size={10} class="text-primary-600" />
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
                                    <span>Sinkronisasi Progres</span>
                                    <span>{student.overall_progress}%</span>
                                </div>
                                <ProgressBar
                                    value={student.overall_progress}
                                    size="xs"
                                    color="bg-primary-600"
                                />
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            <div class="flex justify-end gap-2">
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    href={`/admin/students/${student.id}`}
                                    icon={LineChart}
                                />
                                <button
                                    on:click={() =>
                                        state.handleDelete(student.id)}
                                    class="p-2 rounded-xl font-bold uppercase tracking-widest transition-all duration-300 flex items-center justify-center gap-2 hover:bg-slate-100 text-slate-300 hover:text-rose-500 text-xs"
                                >
                                    <UserMinus size={18} strokeWidth={2.5} />
                                </button>
                            </div>
                        </td>
                    </tr>
                {/each}
            {/if}
        </tbody>
    </table>

    <!-- Simple Pagination -->
    {#if state.students.links && state.students.links.length > 3}
        <div
            class="p-6 border-t border-slate-100 flex justify-center bg-slate-50/30"
        >
            <div class="flex gap-1">
                {#each state.students.links as link}
                    {#if link.url}
                        <Button
                            href={link.url}
                            variant={link.active ? "primary" : "ghost"}
                            size="sm"
                            class={!link.active && !link.url
                                ? "opacity-50 cursor-not-allowed"
                                : ""}
                        >
                            {@html link.label}
                        </Button>
                    {:else}
                        <span class="px-3 py-2 text-slate-400 text-xs font-bold"
                            >{@html link.label}</span
                        >
                    {/if}
                {/each}
            </div>
        </div>
    {/if}
</div>
