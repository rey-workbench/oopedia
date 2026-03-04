<script lang="ts">
    import { Trophy, Check, Target, Star, Flame, BarChart3, Home } from 'lucide-svelte';
    import Button from '@/components/ui/Button.svelte';
    import { ROUTES } from '@/utils/route';

    interface Props {
        state: any;
        material?: any;
        answeredCount?: number;
    }

    let { state, material = {}, answeredCount = 0 }: Props = $props();
</script>

<div class="mx-auto max-w-3xl">
    <div class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-xl">
        <div class="relative bg-emerald-600 p-12 text-center text-white">
            <div class="absolute top-0 right-0 p-8 opacity-10">
                <Trophy size={96} class="text-white" />
            </div>
            <div class="relative z-10">
                <div
                    class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full border-4 border-white/30 bg-white/20 shadow-xl backdrop-blur-md"
                >
                    <Check size={48} class="text-white" />
                </div>
                <h2 class="mb-3 text-4xl font-bold tracking-widest">HEBAT!</h2>
                <p class="text-xl font-medium text-emerald-50">
                    Kamu sudah menjawab semua soal di materi ini dengan baik.
                </p>
            </div>
        </div>

        {#if !state.isGuest}
            <div class="grid grid-cols-3 gap-0 border-b border-slate-100">
                <div class="border-r border-slate-100 p-6 text-center">
                    <div
                        class="bg-primary-50 mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-xl"
                    >
                        <Target size={20} class="text-primary-600" />
                    </div>
                    <div class="text-2xl font-bold text-slate-800">
                        {answeredCount}
                    </div>
                    <div class="text-[10px] font-bold tracking-wider text-slate-400 uppercase">
                        Soal Dijawab
                    </div>
                </div>
                <div class="border-r border-slate-100 p-6 text-center">
                    <div
                        class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50"
                    >
                        <Star size={20} class="text-amber-500" />
                    </div>
                    <div class="text-2xl font-bold text-slate-800">
                        {state.xp}
                    </div>
                    <div class="text-[10px] font-bold tracking-wider text-slate-400 uppercase">
                        Total XP
                    </div>
                </div>
                <div class="p-6 text-center">
                    <div
                        class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-orange-50"
                    >
                        <Flame size={20} class="text-orange-500" />
                    </div>
                    <div class="text-2xl font-bold text-slate-800">
                        {state.streak}
                    </div>
                    <div class="text-[10px] font-bold tracking-wider text-slate-400 uppercase">
                        Streak
                    </div>
                </div>
            </div>
        {/if}

        <div class="space-y-4 bg-white p-10 text-center">
            <Button
                href={ROUTES.MAHASISWA.MATERIALS.QUESTIONS.REVIEW(material.id)}
                variant="secondary"
                icon={BarChart3}
                class="mr-3"
            >
                Review Jawaban
            </Button>
            <Button
                href={ROUTES.MAHASISWA.MATERIALS.SHOW(material.id)}
                variant="primary"
                icon={Home}
                iconPosition="right"
            >
                Kembali ke Materi
            </Button>
        </div>
    </div>
</div>
