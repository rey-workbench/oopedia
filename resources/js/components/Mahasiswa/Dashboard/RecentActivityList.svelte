<script>
    import Card from "@/components/ui/Card.svelte";
    import Button from "@/components/ui/Button.svelte";
    import EmptyState from "@/components/ui/EmptyState.svelte";
    import { Trophy, Star, ClipboardList, Ghost } from "lucide-svelte";

    export let recentActivities = [];
</script>

<div class="space-y-8">
    <div class="flex items-center justify-between">
        <h3 class="text-xl font-bold tracking-widest text-slate-900 uppercase">
            Aktivitas Terbaru
        </h3>
        <Button variant="ghost" size="sm">Lihat Semua</Button>
    </div>

    <div class="space-y-6">
        {#each recentActivities as activity}
            <Card
                padding="p-0"
                class="hover:border-primary-400 transition-all border-slate-100 shadow-xl overflow-hidden group"
            >
                <div class="p-8 flex gap-8 items-center">
                    <div
                        class={`w-16 h-16 rounded-2xl flex items-center justify-center shrink-0 border border-black/5 shadow-inner transition-colors
              ${
                  activity.type === "achievement"
                      ? "bg-emerald-50 text-emerald-500"
                      : activity.type === "milestone"
                        ? "bg-amber-50 text-amber-500"
                        : "bg-primary-50 text-primary-500"
              }`}
                    >
                        <svelte:component
                            this={activity.type === "achievement"
                                ? Trophy
                                : activity.type === "milestone"
                                  ? Star
                                  : ClipboardList}
                            size={24}
                            strokeWidth={2.5}
                        />
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4
                                    class="font-bold text-slate-900 tracking-widest text-sm uppercase"
                                >
                                    {activity.type === "achievement"
                                        ? "Pencapaian Baru!"
                                        : activity.type === "milestone"
                                          ? "Milestone Tercapai!"
                                          : "Progres Belajar"}
                                </h4>
                                <p
                                    class="text-slate-500 text-sm font-medium mt-1 leading-relaxed"
                                >
                                    {#if activity.type === "achievement"}
                                        Menyelesaikan <span
                                            class="text-emerald-500 font-bold"
                                            >{activity.total_correct}
                                            soal</span
                                        >
                                        di materi
                                        <span
                                            class="text-slate-900 font-bold uppercase"
                                            >{activity.material_title}</span
                                        >
                                    {:else if activity.type === "milestone"}
                                        Berhasil menyelesaikan soal <span
                                            class="text-amber-500 font-bold"
                                            >level hard</span
                                        >
                                        di materi
                                        <span
                                            class="text-slate-900 font-bold uppercase"
                                            >{activity.material_title}</span
                                        >
                                    {:else}
                                        Mengerjakan soal <span
                                            class="capitalize font-bold text-primary-500"
                                            >{activity.difficulty}</span
                                        >
                                        di materi
                                        <span
                                            class="text-slate-900 font-bold uppercase"
                                            >{activity.material_title}</span
                                        >
                                    {/if}
                                </p>
                            </div>
                            <span
                                class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                                >{activity.time_ago || "Baru saja"}</span
                            >
                        </div>
                    </div>
                </div>
            </Card>
        {:else}
            <EmptyState
                title="Aktivitas Kosong"
                description="Belum ada aktivitas tercatat untuk akun ini."
                icon={Ghost}
            />
        {/each}
    </div>
</div>
