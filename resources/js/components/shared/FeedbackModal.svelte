<script lang="ts">
    import Badge from '@/components/ui/Badge.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Modal from '@/components/ui/Modal.svelte';
    import Panel from '@/components/ui/Panel.svelte';
    import type { QuestionShowState } from '@/states/Mahasiswa/QuizState.svelte';
    import {
        Video,
        FileText,
        Code,
        Brain,
        CheckCircle2,
        Trophy,
        XCircle,
        RotateCcw,
        ArrowRight,
        Zap,
        ArrowDown,
        Medal,
        BookOpen,
        AlertTriangle,
        TrendingUp,
        Star,
        Check
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

    function getActionIcon() {
        switch (actionCode) {
            case 'H01':
                return Video;
            case 'H02':
                return FileText;
            case 'H03':
                return Code;
            case 'H04':
                return Brain;
            case 'H05':
                return CheckCircle2;
            case 'H06':
                return Zap;
            case 'H07':
                return ArrowDown;
            case 'H08':
                return Trophy;
            case 'H09':
            case 'H10':
            case 'H11':
                return Medal;
            default:
                return state.feedbackData?.status === 'success' ? Check : XCircle;
        }
    }

    function getIconColor() {
        switch (actionCode) {
            case 'H01':
                return 'text-purple-600';
            case 'H02':
                return 'text-primary-600';
            case 'H03':
                return 'text-indigo-600';
            case 'H04':
                return 'text-amber-600';
            case 'H05':
                return 'text-emerald-500';
            case 'H06':
                return 'text-blue-600';
            case 'H07':
                return 'text-rose-600';
            case 'H08':
                return 'text-emerald-600';
            default:
                return state.feedbackData?.status === 'success' ? 'text-emerald-500' : 'text-rose-500';
        }
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
    let IconComponent = $derived(state.showFeedback ? getActionIcon() : null);
    let iconColor = $derived(state.showFeedback ? getIconColor() : null);
    let certDetails = $derived(state.showFeedback ? getCertificateDetails() : null);
</script>

<Modal show={state.showFeedback} maxWidth="xl" closeable={false}>
    <div class="bg-white/95 backdrop-blur-xl">
        {#if variant === 'certificate' && certDetails}
            <div class={`${certDetails.color} relative overflow-hidden p-16 text-center text-white`}>
                <div class="absolute -top-10 -right-10 text-[10rem] opacity-20 rotate-12">
                    {certDetails.badge}
                </div>
                <div class="relative z-10">
                    <div
                        class="mx-auto mb-6 flex h-32 w-32 items-center justify-center rounded-full border-4 border-white/40 bg-white/20 shadow-2xl backdrop-blur-md animate-in zoom-in-50 duration-700"
                    >
                        {#if IconComponent}
                            {@const Icon = IconComponent as any}
                            <Icon size={64} class="text-white drop-shadow-md" />
                        {/if}
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
                        class="mx-auto mb-6 flex h-24 w-24 animate-pulse items-center justify-center rounded-full border-4 border-white/30 bg-white/20 shadow-2xl backdrop-blur-md"
                    >
                        {#if IconComponent}
                            {@const Icon = IconComponent as any}
                            <Icon size={48} class="text-white" />
                        {/if}
                    </div>
                    <Badge
                        variant="secondary"
                        size="sm"
                        class="mb-4 border-white/30 bg-white/30 text-white font-black tracking-widest"
                    >
                        <Zap size={14} class="mr-2" /> TINGKATAN DIVALIDASI
                    </Badge>
                    <h2 class="mb-2 text-4xl font-black tracking-widest drop-shadow-lg">PERCEPATAN!</h2>
                    <p class="text-primary-50 text-base font-medium max-w-xs mx-auto">
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
                    class={`h-24 w-24 ${actionCode === 'H01' || actionCode === 'H02' ? 'bg-amber-100 shadow-amber-100' : 'bg-purple-100 shadow-purple-100'} mx-auto mb-6 flex items-center justify-center rounded-3xl shadow-xl transition-transform hover:scale-110 duration-500`}
                >
                    {#if IconComponent}
                        {@const Icon = IconComponent as any}
                        <Icon size={48} class={iconColor} />
                    {/if}
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
                <p class="mb-8 text-base font-medium text-slate-500 max-w-xs mx-auto">
                    {state.feedbackData.message}
                </p>

                {#if recommendation}
                    <Panel variant="none" rounded="2xl" padding="p-6" class="mb-8 border-2 border-primary-100 bg-primary-50/50 flex items-center gap-4">
                        <div class="bg-primary-600 p-3 rounded-xl shadow-lg shadow-primary-200">
                             <BookOpen size={24} class="text-white" />
                        </div>
                        <div class="text-left">
                            <span class="text-[9px] font-black text-primary-600 tracking-widest uppercase">Pelajari Lagi</span>
                            <p class="text-lg font-black text-slate-800 tracking-tight leading-tight">{recommendation}</p>
                        </div>
                    </Panel>
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
                    class="mx-auto mb-8 flex h-24 w-24 items-center justify-center rounded-3xl bg-rose-100 shadow-xl shadow-rose-100 animate-in slide-in-from-top-4 duration-500"
                >
                    {#if IconComponent}
                        {@const Icon = IconComponent as any}
                        <Icon size={48} class={iconColor} />
                    {/if}
                </div>
                <Badge variant="danger" size="sm" class="mb-4 font-black tracking-widest uppercase">
                    <ArrowDown size={14} class="mr-2" /> PENYESUAIAN ALUR
                </Badge>
                <h2 class="mb-2 text-3xl font-black tracking-tight text-slate-900 uppercase">
                    MARI KEMBALI KE DASAR
                </h2>
                <p class="mb-8 text-base font-medium text-slate-500 max-w-xs mx-auto">
                    {state.feedbackData.message}
                </p>

                {#if recommendation}
                    <Panel variant="none" rounded="2xl" padding="p-4" class="mb-8 bg-rose-50 border border-rose-100">
                        <div class="flex items-center justify-center gap-3">
                            <BookOpen size={20} class="text-rose-600" />
                            <span class="text-sm font-black text-slate-800 tracking-tight">{recommendation}</span>
                        </div>
                    </Panel>
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
                    <div class={`relative flex h-28 w-28 items-center justify-center rounded-full border-4 ${state.feedbackData.status === 'success' ? 'border-emerald-500 bg-emerald-500' : 'border-rose-500 bg-rose-500'} shadow-2xl transition-transform hover:scale-110 duration-500`}>
                        {#if IconComponent}
                            {@const Icon = IconComponent as any}
                            <Icon size={56} class="text-white" strokeWidth={4} />
                        {/if}
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

                <p class="mb-12 text-base font-semibold text-slate-400 max-w-xs mx-auto leading-relaxed">
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
</style>
