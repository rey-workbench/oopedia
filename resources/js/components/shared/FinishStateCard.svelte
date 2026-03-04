<script lang="ts">
    import { Trophy, Check, Target, Star, Flame, BarChart3, Home } from 'lucide-svelte';
    import Button from '@/components/ui/Button.svelte';
    import Panel from '@/components/ui/Panel.svelte';
    import { ROUTES } from '@/utils/route';

    interface Props {
        state: any;
        material?: any;
        answeredCount?: number;
    }

    let { state, material = {}, answeredCount = 0 }: Props = $props();
</script>

<div class="mx-auto max-w-3xl">
    <Panel variant="none" rounded="3xl" padding="p-0" class="overflow-hidden border border-slate-100 bg-white shadow-2xl">
        <div class="relative bg-emerald-600 p-16 text-center text-white">
            <div class="absolute -top-10 -right-10 opacity-10 rotate-12">
                <Trophy size={180} class="text-white" />
            </div>
            <div class="relative z-10">
                <div
                    class="mx-auto mb-8 flex h-32 w-32 items-center justify-center rounded-full border-4 border-white/30 bg-white/20 shadow-2xl backdrop-blur-md animate-in zoom-in-50 duration-500"
                >
                    <Check size={64} class="text-white" />
                </div>
                <h2 class="mb-4 text-5xl font-black tracking-widest drop-shadow-lg">HEBAT!</h2>
                <p class="text-xl font-medium text-emerald-50 max-w-md mx-auto leading-relaxed">
                    Kamu sudah menjawab semua soal di materi ini dengan baik.
                </p>
            </div>
        </div>

        {#if !state.isGuest}
            <div class="grid grid-cols-3 gap-0 border-b border-slate-100 bg-slate-50/50">
                <div class="border-r border-slate-100 p-8 text-center group hover:bg-white transition-colors duration-300">
                    <div
                        class="bg-blue-100 mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl shadow-sm group-hover:scale-110 transition-transform"
                    >
                        <Target size={24} class="text-blue-600" />
                    </div>
                    <div class="text-3xl font-black text-slate-800 tracking-tighter">
                        {answeredCount}
                    </div>
                    <div class="text-[10px] font-bold tracking-widest text-slate-400 uppercase mt-1">
                        Soal Dijawab
                    </div>
                </div>
                <div class="border-r border-slate-100 p-8 text-center group hover:bg-white transition-colors duration-300">
                    <div
                        class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 shadow-sm group-hover:scale-110 transition-transform"
                    >
                        <Star size={24} class="text-amber-500 fill-current" />
                    </div>
                    <div class="text-3xl font-black text-slate-800 tracking-tighter">
                        {state.xp}
                    </div>
                    <div class="text-[10px] font-bold tracking-widest text-slate-400 uppercase mt-1">
                        Total XP
                    </div>
                </div>
                <div class="p-8 text-center group hover:bg-white transition-colors duration-300">
                    <div
                        class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-100 shadow-sm group-hover:scale-110 transition-transform"
                    >
                        <Flame size={24} class="text-orange-600 fill-current" />
                    </div>
                    <div class="text-3xl font-black text-slate-800 tracking-tighter">
                        {state.streak}
                    </div>
                    <div class="text-[10px] font-bold tracking-widest text-slate-400 uppercase mt-1">
                        Streak
                    </div>
                </div>
            </div>
        {/if}

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 bg-white p-12">
            <Button
                href={ROUTES.MAHASISWA.MATERIALS.QUESTIONS.REVIEW(material.id)}
                variant="secondary"
                size="lg"
                class="w-full sm:w-auto px-10 py-4 font-bold tracking-widest uppercase"
            >
                <BarChart3 size={20} class="mr-2" /> Review Jawaban
            </Button>
            <Button
                href={ROUTES.MAHASISWA.MATERIALS.SHOW(material.id)}
                variant="primary"
                size="lg"
                class="w-full sm:w-auto px-10 py-4 font-bold tracking-widest uppercase shadow-xl hover:shadow-primary-200"
            >
                Kembali ke Materi <Home size={20} class="ml-2" />
            </Button>
        </div>
    </Panel>
</div>
