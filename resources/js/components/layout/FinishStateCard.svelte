<script lang="ts">
    import { Trophy, Check, Target, Star, Flame, BarChart3, Home } from '@lucide/svelte';
    import Button from '@/components/ui/Button.svelte';
    import Panel from '@/components/ui/Panel.svelte';
    import { ROUTES } from '@/utils/route';

    interface Props {
        state: any;
        material?: any;
        answered_count?: number;
    }

    let { state, material = {}, answered_count = 0 }: Props = $props();
</script>

<div class="mx-auto max-w-3xl">
    <Panel
        variant="none"
        rounded="3xl"
        padding="p-0"
        class="overflow-hidden border border-slate-100 bg-white shadow-2xl"
    >
        <div class="relative bg-emerald-600 p-16 text-center text-white">
            <div class="absolute -top-10 -right-10 rotate-12 opacity-10">
                <Trophy size={180} class="text-white" />
            </div>
            <div class="relative z-10">
                <div
                    class="animate-in zoom-in-50 mx-auto mb-8 flex h-32 w-32 items-center justify-center rounded-full border-4 border-white/30 bg-white/20 shadow-2xl backdrop-blur-md duration-500"
                >
                    <Check size={64} class="text-white" />
                </div>
                <h2 class="mb-4 text-5xl font-black tracking-widest drop-shadow-lg">HEBAT!</h2>
                <p class="mx-auto max-w-md text-xl leading-relaxed font-medium text-emerald-50">
                    {#if answered_count > 0}
                        Kamu sudah menjawab semua soal di materi ini dengan baik.
                    {:else}
                        Materi ini belum memiliki instrumen evaluasi yang tersedia.
                    {/if}
                </p>
            </div>
        </div>

        {#if !state.isGuest}
            <div
                class="grid grid-cols-2 gap-0 border-b border-slate-100 bg-slate-50/50 sm:grid-cols-4"
            >
                <div
                    class="group border-r border-slate-100 p-8 text-center transition-colors duration-300 hover:bg-white"
                >
                    <div
                        class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 shadow-sm transition-transform group-hover:scale-110"
                    >
                        <Target size={24} class="text-blue-600" />
                    </div>
                    <div class="text-3xl font-black tracking-tighter text-slate-800">
                        {answered_count}
                    </div>
                    <div class="mt-1 text-xs font-bold tracking-widest text-slate-400 uppercase">
                        Soal Dijawab
                    </div>
                </div>
                <div
                    class="group border-r border-slate-100 p-8 text-center transition-colors duration-300 hover:bg-white"
                >
                    <div
                        class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 shadow-sm transition-transform group-hover:scale-110"
                    >
                        <Star size={24} class="fill-current text-amber-500" />
                    </div>
                    <div class="text-3xl font-black tracking-tighter text-slate-800">
                        {state.xp}
                    </div>
                    <div class="mt-1 text-xs font-bold tracking-widest text-slate-400 uppercase">
                        Total XP
                    </div>
                </div>
                <div
                    class="group border-r border-slate-100 p-8 text-center transition-colors duration-300 hover:bg-white"
                >
                    <div
                        class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-100 shadow-sm transition-transform group-hover:scale-110"
                    >
                        <Flame size={24} class="fill-current text-orange-600" />
                    </div>
                    <div class="text-3xl font-black tracking-tighter text-slate-800">
                        {state.streak}
                    </div>
                    <div class="mt-1 text-xs font-bold tracking-widest text-slate-400 uppercase">
                        Streak
                    </div>
                </div>
                <div class="group p-8 text-center transition-colors duration-300 hover:bg-white">
                    <div
                        class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-purple-100 shadow-sm transition-transform group-hover:scale-110"
                    >
                        <Trophy size={24} class="text-purple-600" />
                    </div>
                    <div class="text-xl font-black tracking-tighter text-slate-800">
                        {state.level}
                    </div>
                    <div class="mt-1 text-xs font-bold tracking-widest text-slate-400 uppercase">
                        Level
                    </div>
                </div>
            </div>
        {/if}

        <!-- R15: Certification Badge -->
        {#if state.adaptive_state?.certifications?.length}
            <div
                class="border-b border-amber-100 bg-linear-to-r from-amber-50 to-yellow-50 p-8 text-center"
            >
                <div
                    class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full border-4 border-amber-300 bg-linear-to-br from-amber-400 to-yellow-500 shadow-lg"
                >
                    <Trophy size={40} class="text-white" />
                </div>
                <h3 class="text-2xl font-black tracking-wider text-amber-700">
                    🏅 Sertifikat Diraih!
                </h3>
                <p class="mt-2 text-sm font-medium text-amber-600">
                    Selamat! Kamu telah menunjukkan penguasaan materi yang konsisten dan mendapatkan
                    sertifikat <span class="font-black"
                        >{state.adaptive_state.certifications[
                            state.adaptive_state.certifications.length - 1
                        ]}</span
                    >.
                </p>
            </div>
        {/if}

        <div class="flex flex-col items-center justify-center gap-4 bg-white p-12 sm:flex-row">
            <Button
                href={ROUTES.MAHASISWA.MATERIALS.QUESTIONS.REVIEW(material.id)}
                variant="secondary"
                size="lg"
                class="w-full px-10 py-4 font-bold tracking-widest uppercase sm:w-auto"
            >
                <BarChart3 size={20} class="mr-2" /> Review Jawaban
            </Button>
            <Button
                href={ROUTES.MAHASISWA.MATERIALS.SHOW(material.id)}
                variant="primary"
                size="lg"
                class="hover:shadow-primary-200 w-full px-10 py-4 font-bold tracking-widest uppercase shadow-xl sm:w-auto"
            >
                Kembali ke Materi <Home size={20} class="ml-2" />
            </Button>
        </div>
    </Panel>
</div>
