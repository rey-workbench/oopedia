<script lang="ts">
    import Badge from '@/components/ui/Badge.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Modal from '@/components/ui/Modal.svelte';
    import Panel from '@/components/ui/Panel.svelte';
    import { DotLottieSvelte } from '@lottiefiles/dotlottie-svelte';
    import type { QuestionShowState } from '@/states/Mahasiswa/QuizState.svelte';
    import {
        ArrowRight,
        Star,
        TrendingUp,
        Zap,
        AlertTriangle,
        BookOpen,
        ArrowDown,
        RotateCcw
    } from 'lucide-svelte';

    interface Props {
        state: QuestionShowState;
    }

    let { state }: Props = $props();

    let actionCode = $derived(state.feedbackData?.adaptiveResult?.triggered_rule?.action || null);
    let nextAction = $derived(
        state.feedbackData?.adaptiveResult?.new_state?.next_action_data?.label ||
            (state.feedbackData?.status === 'success' ? 'Soal Berikutnya' : 'Lihat Materi')
    );
    let nextActionType = $derived(
        state.feedbackData?.adaptiveResult?.new_state?.next_action_data?.type || 'question'
    );
    let recommendation = $derived(
        state.feedbackData?.adaptiveResult?.new_state?.recommendation || null
    );
    let certification = $derived(
        state.feedbackData?.adaptiveResult?.new_state?.certification || null
    );
    let xpEarned = $derived(state.feedbackData?.adaptiveResult?.global_xp_earned || 0);
    let streakBonus = $derived(state.feedbackData?.adaptiveResult?.streak_bonus || null);
    let interventionType = $derived(
        state.feedbackData?.adaptiveResult?.new_state?.intervention_type || null
    );

    function getModalVariant() {
        if (certification) return 'certificate';
        if (
            interventionType?.includes('crisis') ||
            interventionType?.includes('recovery') ||
            interventionType?.includes('persistent') ||
            interventionType?.includes('project_revision') ||
            interventionType?.includes('safety')
        )
            return 'intervention';

        if (['H01', 'H02', 'H03', 'H04'].includes(actionCode || '')) return 'intervention';
        if (actionCode === 'H06') return 'acceleration';
        if (actionCode === 'H07') return 'backtrack';
        if (actionCode === 'H08') return 'graduation';
        if (state.feedbackData?.status === 'success') return 'success';
        return 'error';
    }

    function getCertificateDetails() {
        if (!certification) return null;

        switch (certification) {
            case 'gold':
                return {
                    color: 'bg-gradient-to-br from-amber-400 via-amber-500 to-amber-600',
                    title: 'SERTIFIKAT EMAS',
                    badge: '🥇',
                    subtitle: 'Master Of Object-Oriented Programming',
                };
            case 'silver':
                return {
                    color: 'bg-gradient-to-br from-slate-300 via-slate-400 to-slate-500',
                    title: 'SERTIFIKAT PERAK',
                    badge: '🥈',
                    subtitle: 'Senior Object-Oriented Programmer',
                };
            case 'bronze':
                return {
                    color: 'bg-gradient-to-br from-orange-300 via-orange-400 to-orange-500',
                    title: 'SERTIFIKAT PERUNGGU',
                    badge: '🥉',
                    subtitle: 'Junior Object-Oriented Programmer',
                };
            default:
                return null;
        }
    }

    let variant = $derived(state.showFeedback ? getModalVariant() : null);
    let certDetails = $derived(state.showFeedback ? getCertificateDetails() : null);
</script>

<Modal show={state.showFeedback} maxWidth="2xl" closeable={false}>
    <div class="bg-white/95 backdrop-blur-xl">
        {#if variant === 'certificate' && certDetails}
            <div class={`${certDetails.color} relative overflow-hidden p-16 text-center text-white`}>
                <div class="absolute -top-10 -right-10 text-[10rem] opacity-20 rotate-12">
                    {certDetails.badge}
                </div>
                <div class="relative z-10">
                    <div
                        class="mx-auto mb-6 flex h-48 w-48 items-center justify-center animate-in zoom-in-50 duration-700"
                    >
                        <div class="h-full w-full overflow-hidden rounded-full">
                            <DotLottieSvelte
                                src="/assets/lottie/quiz/graduation.json"
                                loop={true}
                                autoplay={true}
                                backgroundColor="transparent"
                                renderConfig={{ devicePixelRatio: window?.devicePixelRatio || 1 }}
                            />
                        </div>
                    </div>
                    <h2 class="mb-3 text-4xl font-black tracking-widest drop-shadow-xl uppercase">
                        {certDetails.title}
                    </h2>
                    <div
                        class="mb-4 inline-block rounded-full bg-white/30 px-6 py-2 text-[10px] font-black tracking-widest backdrop-blur-xl ring-1 ring-white/50"
                    >
                        {certDetails.subtitle}
                    </div>
                    <p class="mt-2 text-lg font-medium text-white/95 leading-relaxed drop-shadow">
                        {state.feedbackData.message}
                    </p>
                </div>
            </div>
            <div class="p-10">
                {#if xpEarned > 0}
                    <div class="mb-8 flex justify-center">
                        <Panel variant="none" rounded="2xl" padding="px-10 py-5" class="bg-slate-900 border border-amber-500/30 flex items-center gap-4 shadow-2xl">
                            <Star size={24} class="fill-current text-white" />
                            <span class="text-2xl font-black text-white tracking-tighter">+{xpEarned} XP</span>
                        </Panel>
                    </div>
                {/if}
                <Button
                    variant="primary"
                    onclick={() => state.handleNext()}
                    class="w-full bg-slate-900 hover:bg-slate-800 border-none py-4 text-sm font-black tracking-widest uppercase shadow-xl"
                >
                    Lanjutkan <ArrowRight size={18} class="ml-2" />
                </Button>
            </div>
        {:else if variant === 'acceleration'}
            <div class="bg-primary-600 relative overflow-hidden p-12 text-center text-white">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,var(--tw-gradient-stops))] from-white/10 to-transparent"></div>
                <div class="relative z-10">
                    <div
                        class="mx-auto mb-6 flex h-40 w-40 items-center justify-center"
                    >
                        <!-- ANIMASI PERCEPATAN (Ganti dengan file JSON yang sesuai) -->
                        <div class="h-full w-full overflow-hidden rounded-full">
                            <DotLottieSvelte
                                src="/assets/lottie/quiz/graduation.json"
                                loop={true}
                                autoplay={true}
                                backgroundColor="transparent"
                                renderConfig={{ devicePixelRatio: window?.devicePixelRatio || 1 }}
                            />
                        </div>
                    </div>
                    <Badge
                        variant="secondary"
                        size="sm"
                        class="mb-4 border-white/30 bg-white/30 text-white font-black tracking-widest"
                    >
                        <Zap size={14} class="mr-2" /> TINGKATAN DIVALIDASI
                    </Badge>
                    <h2 class="mb-2 text-4xl font-black tracking-widest drop-shadow-lg">PERCEPATAN!</h2>
                    <p class="text-primary-50 text-base font-medium mx-auto">
                        {state.feedbackData.message}
                    </p>
                </div>
            </div>
            <div class="p-10">
                {#if xpEarned > 0}
                    <div class="mb-8 flex justify-center">
                        <Panel variant="none" rounded="2xl" padding="px-10 py-5" class="bg-slate-900 border border-primary-500/30 flex items-center gap-4 shadow-2xl">
                            <Star size={24} class="fill-current text-white" />
                            <span class="text-2xl font-black text-white tracking-tighter">+{xpEarned} XP</span>
                        </Panel>
                    </div>
                {/if}
                <Button
                    variant="primary"
                    onclick={() => state.handleNext()}
                    class="w-full bg-slate-900 hover:bg-slate-800 border-none py-4 text-sm font-black tracking-widest uppercase shadow-xl"
                >
                    {nextAction} <ArrowRight size={18} class="ml-2" />
                </Button>
            </div>
        {:else if variant === 'intervention'}
            <div class="p-12 text-center">
                <div
                    class="mx-auto mb-6 flex h-40 w-40 items-center justify-center transition-transform hover:scale-110 duration-500"
                >
                        <!-- ANIMASI INTERVENSI (Ganti dengan file JSON yang sesuai) -->
                        <div class="h-full w-full overflow-hidden rounded-3xl">
                            <DotLottieSvelte
                                src="/assets/lottie/quiz/graduation.json"
                                loop={true}
                                autoplay={true}
                                backgroundColor="transparent"
                                renderConfig={{ devicePixelRatio: window?.devicePixelRatio || 1 }}
                            />
                        </div>
                </div>
                <Badge
                    variant="warning"
                    size="sm"
                    class="mb-4 font-black tracking-widest uppercase border-2"
                >
                    <AlertTriangle size={14} class="mr-2" /> Rekomendasi Adaptif
                </Badge>
                <h2 class="mb-2 text-3xl font-black tracking-tight text-slate-900 uppercase">
                    {state.feedbackData.status === 'success' ? 'BAGUS SEKALI!' : 'KAMI ADA UNTUKMU'}
                </h2>
                <p class="mb-8 text-lg font-medium text-slate-500 mx-auto px-4">
                    {state.feedbackData.message}
                </p>

                {#if recommendation}
                    <div class="mb-8 text-left group">
                        <div class="relative overflow-hidden rounded-2xl border-2 border-slate-100 bg-white p-5 shadow-sm transition-all hover:border-primary-200 hover:shadow-md">
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-50 text-primary-600 shadow-inner">
                                    <BookOpen size={24} />
                                </div>
                                <div class="flex-1">
                                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-primary-500">Rekomendasi Materi</span>
                                    <p class="text-lg font-black leading-tight text-slate-800">{recommendation}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}

                <Button
                    variant="primary"
                    onclick={() => state.handleNext()}
                    class="w-full bg-slate-900 hover:bg-slate-800 border-none py-4 text-sm font-black tracking-widest uppercase shadow-xl"
                >
                    {nextAction} <ArrowRight size={18} class="ml-2" />
                </Button>
            </div>
        {:else if variant === 'backtrack'}
            <div class="p-12 text-center">
                <div
                    class="mx-auto mb-8 flex h-40 w-40 items-center justify-center animate-in slide-in-from-top-4 duration-500"
                >
                        <!-- ANIMASI KEMBALI MUNDUR (Ganti dengan file JSON yang sesuai) -->
                        <div class="h-full w-full overflow-hidden rounded-3xl">
                            <DotLottieSvelte
                                src="/assets/lottie/quiz/graduation.json"
                                loop={true}
                                autoplay={true}
                                backgroundColor="transparent"
                                renderConfig={{ devicePixelRatio: window?.devicePixelRatio || 1 }}
                            />
                        </div>
                </div>
                <Badge variant="danger" size="sm" class="mb-4 font-black tracking-widest uppercase">
                    <ArrowDown size={14} class="mr-2" /> PENYESUAIAN ALUR
                </Badge>
                <h2 class="mb-2 text-3xl font-black tracking-tight text-slate-900 uppercase">
                    MARI KEMBALI KE DASAR
                </h2>
                <p class="mb-8 text-lg font-medium text-slate-500 mx-auto px-4">
                    {state.feedbackData.message}
                </p>

                {#if recommendation}
                    <div class="mb-8 group">
                        <div class="relative overflow-hidden rounded-2xl border-2 border-rose-100 bg-rose-50/30 p-4 transition-all hover:shadow-sm">
                            <div class="flex items-center justify-center gap-3">
                                <BookOpen size={20} class="text-rose-500" />
                                <span class="text-base font-black tracking-tight text-slate-800">{recommendation}</span>
                            </div>
                        </div>
                    </div>
                {/if}

                <Button
                    variant="primary"
                    onclick={() => state.handleNext()}
                    class="w-full bg-slate-900 hover:bg-slate-800 border-none py-4 text-sm font-black tracking-widest uppercase shadow-xl shadow-rose-100"
                >
                    {nextAction} <ArrowRight size={18} class="ml-2" />
                </Button>
            </div>
        {:else}
            <!-- Success/Error Variant matching the reference screenshot -->
            <div class="p-10 pt-16 text-center">
                <div class="mb-8 flex justify-center">
                    <div class="relative flex h-44 w-44 items-center justify-center transition-transform hover:scale-110 duration-500">
                        <!-- ANIMASI BENAR/SALAH (Ganti dengan file JSON yang sesuai) -->
                        <div class="h-full w-full overflow-hidden rounded-full z-10">
                            <DotLottieSvelte
                                src="/assets/lottie/quiz/graduation.json"
                                loop={true}
                                autoplay={true}
                                backgroundColor="transparent"
                                renderConfig={{ devicePixelRatio: window?.devicePixelRatio || 1 }}
                            />
                        </div>
                        <div class={`absolute -inset-2 rounded-full border-4 border-dashed animate-spin-slow opacity-20 ${state.feedbackData.status === 'success' ? 'border-emerald-500' : 'border-rose-500'}`}></div>
                    </div>
                </div>

                <div class="mb-2">
                    <span class={`text-xs font-black tracking-[0.3em] uppercase ${state.feedbackData.status === 'success' ? 'text-emerald-500' : 'text-rose-500'}`}>
                        {state.feedbackData.status === 'success' ? 'OK!' : 'WRONG'}
                    </span>
                </div>

                <h2 class={`mb-4 text-5xl font-black tracking-tighter uppercase ${state.feedbackData.status === 'success' ? 'text-emerald-600' : 'text-rose-600'}`}>
                    {state.feedbackData.status === 'success' ? 'BENAR!' : 'KURANG TEPAT'}
                </h2>

                <p class="mb-12 text-lg font-semibold text-slate-400 mx-auto leading-relaxed px-4">
                    {state.feedbackData.message}
                </p>

                <div class="flex flex-col gap-8">
                    <div class="flex flex-wrap items-center justify-center gap-4">
                        {#if xpEarned > 0}
                            <div class="group relative">
                                <Panel variant="none" rounded="2xl" padding="px-8 py-4" class="bg-slate-900 border border-white/10 flex items-center gap-3 shadow-xl transition-all hover:scale-105 active:scale-95">
                                    <Star size={20} class="text-white fill-current" />
                                    <span class="text-xl font-black text-white tracking-tighter">+{xpEarned} XP</span>
                                </Panel>
                            </div>
                        {/if}
                        {#if streakBonus}
                            <div class="group relative">
                                <Panel variant="none" rounded="2xl" padding="px-8 py-4" class="bg-slate-900 border border-amber-500/50 flex items-center gap-3 shadow-xl transition-all hover:scale-105 active:scale-95">
                                    <TrendingUp size={20} class="text-orange-500" />
                                    <div class="text-left">
                                        <p class="text-[14px] font-black text-orange-500 tracking-tight leading-none">{streakBonus}</p>
                                    </div>
                                </Panel>
                            </div>
                        {/if}
                    </div>

                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        {#if state.feedbackData.status === 'error' && nextActionType !== 'material' && !recommendation}
                            <Button
                                variant="outline"
                                onclick={() => state.handleTryAgain()}
                                class="px-10 py-4 text-xs font-black tracking-widest uppercase border-2 border-slate-200 hover:bg-slate-50"
                            >
                                <RotateCcw size={16} class="mr-2" /> COBA LAGI
                            </Button>
                        {/if}
                        <Button
                            variant="primary"
                            onclick={() => state.handleNext()}
                            class="w-full sm:w-auto px-16 py-4 bg-slate-900 hover:bg-slate-800 border-none text-xs font-black tracking-widest uppercase shadow-2xl shadow-slate-200 ring-4 ring-slate-100 transition-all active:scale-95"
                        >
                            {nextAction}
                            <ArrowRight size={16} class="ml-2" />
                        </Button>
                    </div>
                    
                    <p class="text-[9px] font-bold tracking-[0.2em] text-slate-300 uppercase">
                        Sistem Adaptif oopedia • v2.1
                    </p>
                </div>
            </div>
        {/if}
    </div>
</Modal>

<style>
    :global(.animate-spin-slow) {
        animation: spin 8s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    :global(.overflow-hidden canvas) {
        transform: scale(1.6);
        transform-origin: center;
        filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1));
    }
</style>
