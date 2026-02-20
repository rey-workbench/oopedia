<script>
    import Card from "@/ui/Card.svelte";
    import Button from "@/ui/Button.svelte";
    import ProgressBar from "@/ui/ProgressBar.svelte";
    import { ArrowRight, Search } from "lucide-svelte";

    export let studentProgress;
</script>

<div class="lg:col-span-2">
    <Card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
        <div
            class="flex items-center justify-between w-full px-8 py-6 border-b border-slate-50"
        >
            <p
                class="text-[10px] font-bold uppercase tracking-widest text-slate-400"
            >
                Matriks Performa Utama
            </p>
            <Button
                variant="ghost"
                size="sm"
                href="/admin/students"
                icon={ArrowRight}>DATA GLOBAL</Button
            >
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr>
                        <th
                            class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                            >Identitas Subjek</th
                        >
                        <th
                            class="p-6 text-center text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                            >Jumlah Evaluasi</th
                        >
                        <th
                            class="p-6 text-center text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                            >Progres Sinkronisasi</th
                        >
                        <th
                            class="p-6 text-right text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                            >Aksi</th
                        >
                    </tr>
                </thead>
                <tbody>
                    {#each studentProgress as student (student.id)}
                        <tr
                            class="group hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0"
                        >
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold shadow-lg shadow-slate-200 uppercase text-xs"
                                    >
                                        {student.name.charAt(0)}
                                    </div>
                                    <div>
                                        <div
                                            class="font-bold text-slate-900 tracking-widest leading-none mb-1"
                                        >
                                            {student.name}
                                        </div>
                                        <div
                                            class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"
                                        >
                                            {student.email}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-bold text-slate-900"
                                    >{student.completed_questions}</span
                                >
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-2 w-32 mx-auto">
                                    <div
                                        class="flex justify-between text-[8px] font-bold uppercase tracking-widest text-slate-400"
                                    >
                                        <span>Progres</span>
                                        <span
                                            >{student.materials_progress}%</span
                                        >
                                    </div>
                                    <ProgressBar
                                        value={student.materials_progress}
                                        size="xs"
                                        color="bg-primary-600"
                                    />
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    href={`/admin/students/${student.id}`}
                                    icon={Search}
                                />
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        </div>
    </Card>
</div>
