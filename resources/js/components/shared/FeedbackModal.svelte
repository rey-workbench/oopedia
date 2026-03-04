<script lang="ts">
    import Badge from '@/components/ui/Badge.svelte';
    import Button from '@/components/ui/Button.svelte';
    import type { QuestionShowState } from '@/states/Mahasiswa/QuizState.svelte';
    import {
        Video,
        FileText,
        Code,
        Brain,
        CheckCircle2,
        Trophy,
        XCircle,
        Target,
        RotateCcw,
        ArrowRight,
        Zap,
        ArrowDown,
        Medal,
        BookOpen,
        AlertTriangle,
        TrendingUp,
        Star,
    } from 'lucide-svelte';

    interface Props {
        state: QuestionShowState;
    }

    let { state }: Props = $props();

    let actionCode = $derived(state.feedbackData?.adaptiveResult?.triggered_rule?.action || null);
    let nextAction = $derived(
        state.feedbackData?.adaptiveResult?.new_state?.next_action_data?.label ||
            (state.feedbackData?.status === 'success' ? 'Lanjut' : 'Lihat Materi')
    );
    let nextActionType = $derived(
        state.feedbackData?.adaptiveResult?.new_state?.next_action_data?.type || 'question'
    );
    let recommendation = $derived(
        state.feedbackData?.adaptiveResult?.new_state?.recommendation || null
    );
    let modalTriggeredRule = $derived(state.feedbackData?.adaptiveResult?.triggered_rule || null);
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
                return state.feedbackData?.status === 'success' ? CheckCircle2 : XCircle;
        }
    }

    function getIconColor() {
        switch (actionCode) {
            case 'H01':
            case 'H02':
                return 'text-amber-500';
            case 'H03':
            case 'H04':
                return 'text-purple-500';
            case 'H06':
                return 'text-primary-500';
            case 'H07':
                return 'text-rose-500';
            case 'H08':
                return 'text-emerald-500';
            case 'H09':
                return 'text-yellow-500';
            case 'H10':
                return 'text-slate-400';
            case 'H11':
                return 'text-orange-600';
            default:
                return state.feedbackData?.status === 'success'
                    ? 'text-emerald-500'
                    : 'text-rose-500';
        }
    }

    function getCertificateDetails() {
        switch (certification) {
            case 'gold':
                return {
                    color: 'bg-amber-400',
                    title: 'SERTIFIKAT EMAS',
                    badge: '🥇',
                    subtitle: 'Object-Oriented Architect',
                };
            case 'silver':
                return {
                    color: 'bg-slate-300',
                    title: 'SERTIFIKAT PERAK',
                    badge: '🥈',
                    subtitle: 'Object-Oriented Developer',
                };
            case 'bronze':
                return {
                    color: 'bg-orange-400',
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

{#if state.showFeedback}
    <div
        class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm transition-all duration-300"
        role="dialog"
        aria-modal="true"
    >
        <div
            class="mx-4 w-full max-w-2xl scale-100 transform overflow-hidden rounded-3xl bg-white shadow-2xl transition-all"
        >
            {#if variant === 'certificate' && certDetails}
                <div
                    class={`${certDetails.color} relative overflow-hidden p-16 text-center text-white`}
                >
                    <div class="absolute -top-10 -right-10 text-9xl opacity-10">
                        {certDetails.badge}
                    </div>
                    <div class="relative z-10">
                        <div
                            class="mx-auto mb-6 flex h-32 w-32 items-center justify-center rounded-full border-4 border-white/30 bg-white/20 shadow-2xl backdrop-blur-md"
                        >
                            {#if IconComponent}
                                {@const Icon = IconComponent as any}
                                <Icon size={64} class="text-white" />
                            {/if}
                        </div>
                        <h2 class="mb-3 text-5xl font-bold tracking-widest drop-shadow-lg">
                            {certDetails.title}
                        </h2>
                        <div
                            class="mb-4 inline-block rounded-full bg-white/20 px-6 py-2 text-sm font-bold tracking-widest backdrop-blur-md"
                        >
                            {certDetails.subtitle}
                        </div>
                        <p class="mt-4 text-lg font-medium text-white/90">
                            {state.feedbackData.message}
                        </p>
                    </div>
                </div>
                <div class="bg-white p-10">
                    {#if xpEarned > 0}
                        <div
                            class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-center"
                        >
                            <div class="flex items-center justify-center gap-2">
                                <Star size={20} class="fill-current text-amber-500" />
                                <span class="text-lg font-bold text-amber-700">+{xpEarned} XP</span>
                            </div>
                        </div>
                    {/if}
                    <Button
                        variant="primary"
                        onclick={() => state.handleNext()}
                        class="w-full py-4 text-base font-bold tracking-widest uppercase"
                    >
                        Lanjutkan <ArrowRight size={20} class="ml-2" />
                    </Button>
                </div>
            {:else if variant === 'acceleration'}
                <div class="bg-primary-600 relative overflow-hidden p-12 text-center text-white">
                    <div class="absolute top-0 left-0 h-full w-full opacity-10"></div>
                    <div class="relative z-10">
                        <div
                            class="mx-auto mb-6 flex h-24 w-24 animate-pulse items-center justify-center rounded-full border-4 border-white/30 bg-white/20 shadow-xl backdrop-blur-md"
                        >
                            {#if IconComponent}
                                {@const Icon = IconComponent as any}
                                <Icon size={48} class="text-white" />
                            {/if}
                        </div>
                        <Badge
                            variant="secondary"
                            size="lg"
                            class="mb-4 border-white/30 bg-white/20 text-white"
                        >
                            <Zap size={16} class="mr-1" /> LOMPATAN KESULITAN
                        </Badge>
                        <h2 class="mb-3 text-4xl font-bold tracking-widest">PERCEPATAN!</h2>
                        <p class="text-primary-50 text-lg font-medium">
                            {state.feedbackData.message}
                        </p>
                    </div>
                </div>
                <div class="bg-white p-10">
                    {#if xpEarned > 0}
                        <div class="bg-primary-50 border-primary-200 mb-6 rounded-xl border p-4">
                            <div class="flex items-center justify-center gap-3">
                                <Star size={20} class="fill-current text-blue-500" />
                                <span class="text-primary-700 text-lg font-bold"
                                    >Bonus: +{xpEarned} XP</span
                                >
                            </div>
                        </div>
                    {/if}
                    {#if modalTriggeredRule}
                        <div class="mb-6 rounded-xl bg-slate-50 p-4 text-sm text-slate-600">
                            <div class="mb-1 font-bold text-slate-700">
                                Adaptive Rule Triggered:
                            </div>
                            <Badge variant="outline" size="sm"
                                >{modalTriggeredRule.id}: {modalTriggeredRule.name}</Badge
                            >
                        </div>
                    {/if}
                    <Button
                        variant="primary"
                        onclick={() => state.handleNext()}
                        class="bg-primary-600 hover:bg-primary-700 w-full py-4 text-base font-bold tracking-widest uppercase"
                    >
                        {nextAction}
                        <ArrowRight size={20} class="ml-2" />
                    </Button>
                </div>
            {:else if variant === 'intervention'}
                <div class="p-12">
                    <div class="mb-8 text-center">
                        <div
                            class={`h-24 w-24 ${actionCode === 'H01' || actionCode === 'H02' ? 'bg-amber-100' : 'bg-purple-100'} mx-auto mb-6 flex items-center justify-center rounded-full shadow-lg`}
                        >
                            {#if IconComponent}
                                {@const Icon = IconComponent as any}
                                <Icon size={48} class={iconColor} />
                            {/if}
                        </div>
                        <Badge
                            variant="outline"
                            size="lg"
                            class="mb-4 border-amber-300 text-amber-700"
                        >
                            <AlertTriangle size={16} class="mr-1" />
                            {interventionType?.includes('crisis')
                                ? 'Intervensi Krisis'
                                : interventionType?.includes('recovery')
                                  ? 'Pemulihan'
                                  : interventionType?.includes('persistent')
                                    ? 'Safety Net'
                                    : 'Rekomendasi Adaptif'}
                        </Badge>
                        <h2 class="mb-3 text-3xl font-bold tracking-wide text-slate-800 uppercase">
                            {state.feedbackData.status === 'success' ? 'Bagus!' : 'Perlu Perbaikan'}
                        </h2>
                        <p class="mb-6 text-lg text-slate-600">
                            {state.feedbackData.message}
                        </p>
                    </div>

                    {#if recommendation}
                        <div class="bg-primary-50 border-primary-200 mb-8 rounded-2xl border-2 p-6">
                            <div
                                class="border-primary-200 mb-4 flex items-start gap-4 border-b pb-4"
                            >
                                <div
                                    class="bg-primary-100 flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl"
                                >
                                    <BookOpen size={20} class="text-primary-600" />
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-primary-900 mb-1 text-lg font-bold">
                                        Rekomendasi Pembelajaran
                                    </h3>
                                    <p class="text-primary-700 text-sm">
                                        Sistem merekomendasikan metode berikut untuk meningkatkan
                                        pemahaman Anda
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                {#if actionCode === 'H01'}
                                    <Video size={24} class="text-purple-600" />
                                    <span class="text-base font-bold text-slate-800"
                                        >{recommendation}</span
                                    >
                                {:else if actionCode === 'H02'}
                                    <FileText size={24} class="text-primary-600" />
                                    <span class="text-base font-bold text-slate-800"
                                        >{recommendation}</span
                                    >
                                {:else if actionCode === 'H03'}
                                    <Code size={24} class="text-purple-600" />
                                    <span class="text-base font-bold text-slate-800"
                                        >{recommendation}</span
                                    >
                                {:else if actionCode === 'H04'}
                                    <Brain size={24} class="text-primary-600" />
                                    <span class="text-base font-bold text-slate-800"
                                        >{recommendation}</span
                                    >
                                {:else}
                                    <Target size={24} class="text-primary-600" />
                                    <span class="text-base font-bold text-slate-800"
                                        >{recommendation}</span
                                    >
                                {/if}
                            </div>
                        </div>
                    {/if}

                    {#if modalTriggeredRule}
                        <div class="mb-6 rounded-xl bg-slate-50 p-4 text-sm text-slate-600">
                            <div class="mb-2 font-bold text-slate-700">Sistem Adaptif:</div>
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <Badge variant="outline" size="sm"
                                        >{modalTriggeredRule.id}</Badge
                                    >
                                    <span>{modalTriggeredRule.name}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Badge
                                        variant="secondary"
                                        size="sm"
                                        class="bg-primary-100 text-primary-700"
                                        >{modalTriggeredRule.action}</Badge
                                    >
                                    <span class="text-xs text-slate-500"
                                        >Priority: {modalTriggeredRule.priority}</span
                                    >
                                </div>
                            </div>
                        </div>
                    {/if}

                    <Button
                        variant="primary"
                        onclick={() => state.handleNext()}
                        class="w-full py-4 text-base font-bold tracking-widest uppercase"
                    >
                        {nextAction}
                        <ArrowRight size={20} class="ml-2" />
                    </Button>
                </div>
            {:else if variant === 'backtrack'}
                <div class="p-12 text-center">
                    <div
                        class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-rose-100 shadow-lg"
                    >
                        {#if IconComponent}
                            {@const Icon = IconComponent as any}
                            <Icon size={48} class={iconColor} />
                        {/if}
                    </div>
                    <Badge variant="outline" size="lg" class="mb-4 border-rose-300 text-rose-700">
                        <ArrowDown size={16} class="mr-1" /> Penyesuaian Tingkat
                    </Badge>
                    <h2 class="mb-3 text-3xl font-bold tracking-wide text-slate-800 uppercase">
                        Mari Kembali ke Dasar
                    </h2>
                    <p class="mb-8 text-lg text-slate-600">
                        {state.feedbackData.message}
                    </p>

                    {#if recommendation}
                        <div class="mb-8 rounded-2xl border border-rose-200 bg-rose-50 p-6">
                            <div class="flex items-center justify-center gap-3">
                                <BookOpen size={24} class="text-rose-600" />
                                <span class="text-base font-bold text-slate-800"
                                    >{recommendation}</span
                                >
                            </div>
                        </div>
                    {/if}

                    {#if modalTriggeredRule}
                        <div class="mb-6 rounded-xl bg-slate-50 p-4 text-sm text-slate-600">
                            <Badge variant="outline" size="sm"
                                >{modalTriggeredRule.id}: {modalTriggeredRule.name}</Badge
                            >
                        </div>
                    {/if}

                    <Button
                        variant="primary"
                        onclick={() => state.handleNext()}
                        class="w-full bg-rose-600 py-4 text-base font-bold tracking-widest uppercase hover:bg-rose-700"
                    >
                        {nextAction}
                        <ArrowRight size={20} class="ml-2" />
                    </Button>
                </div>
            {:else if variant === 'graduation'}
                <div class="relative overflow-hidden bg-emerald-600 p-12 text-center text-white">
                    <div class="absolute top-0 right-0 animate-bounce p-8 opacity-10">
                        <Trophy size={120} class="text-white" />
                    </div>
                    <div class="relative z-10">
                        <div
                            class="mx-auto mb-6 flex h-28 w-28 items-center justify-center rounded-full border-4 border-white/30 bg-white/20 shadow-2xl backdrop-blur-md"
                        >
                            {#if IconComponent}
                                {@const Icon = IconComponent as any}
                                <Icon size={56} class="text-white" />
                            {/if}
                        </div>
                        <Badge
                            variant="secondary"
                            size="lg"
                            class="mb-4 border-white/30 bg-white/20 text-white"
                        >
                            <Trophy size={16} class="mr-1" /> MODUL SELESAI
                        </Badge>
                        <h2 class="mb-3 text-5xl font-bold tracking-widest">SELAMAT!</h2>
                        <p class="text-xl font-medium text-emerald-50">
                            {state.feedbackData.message}
                        </p>
                    </div>
                </div>
                <div class="bg-white p-10">
                    {#if xpEarned > 0}
                        <div
                            class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-center"
                        >
                            <div class="flex items-center justify-center gap-2">
                                <Star size={20} class="fill-current text-emerald-500" />
                                <span class="text-lg font-bold text-emerald-700"
                                    >+{xpEarned} XP</span
                                >
                            </div>
                        </div>
                    {/if}
                    <Button
                        variant="primary"
                        onclick={() => state.handleNext()}
                        class="w-full bg-emerald-600 py-4 text-base font-bold tracking-widest uppercase hover:bg-emerald-700"
                    >
                        {nextAction}
                        <ArrowRight size={20} class="ml-2" />
                    </Button>
                </div>
            {:else}
                <div class="p-12 text-center">
                    <div class="mb-6 text-8xl">
                        {#if IconComponent}
                            {@const Icon = IconComponent as any}
                            <Icon size={96} class={iconColor + ' mx-auto'} />
                        {/if}
                    </div>

                    <h2
                        class={`mb-4 text-4xl font-bold tracking-widest uppercase ${state.feedbackData.status === 'success' ? 'text-emerald-600' : 'text-rose-600'}`}
                    >
                        {state.feedbackData.status === 'success' ? 'BENAR!' : 'SALAH!'}
                    </h2>

                    <p class="mb-8 text-lg text-slate-600">
                        {state.feedbackData.message}
                    </p>

                    {#if state.feedbackData.status === 'success'}
                        <div class="mb-8 space-y-3">
                            {#if xpEarned > 0}
                                <div class="bg-primary-50 border-primary-200 rounded-xl border p-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <Star size={20} class="text-primary-500 fill-current" />
                                        <span class="text-primary-700 text-lg font-bold"
                                            >+{xpEarned} XP</span
                                        >
                                    </div>
                                </div>
                            {/if}
                            {#if streakBonus}
                                <div class="rounded-xl border border-orange-200 bg-orange-50 p-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <TrendingUp size={20} class="text-orange-500" />
                                        <span class="text-base font-bold text-orange-700"
                                            >{streakBonus}</span
                                        >
                                    </div>
                                </div>
                            {/if}
                        </div>
                    {/if}

                    {#if modalTriggeredRule && state.feedbackData.status === 'success'}
                        <div class="mb-8 rounded-xl bg-slate-50 p-4 text-sm text-slate-600">
                            <Badge variant="outline" size="sm"
                                >{modalTriggeredRule.id}: {modalTriggeredRule.name}</Badge
                            >
                        </div>
                    {/if}

                    <div class="flex flex-col justify-center gap-4 sm:flex-row">
                        {#if state.feedbackData.status === 'error' && nextActionType !== 'material' && !recommendation}
                            <Button
                                variant="outline"
                                onclick={() => state.handleTryAgain()}
                                class="px-8 py-3 text-sm font-bold tracking-widest uppercase"
                            >
                                <RotateCcw size={18} class="mr-2" /> Coba Lagi
                            </Button>
                        {/if}
                        <Button
                            variant="primary"
                            onclick={() => state.handleNext()}
                            class="px-8 py-3 text-sm font-bold tracking-widest uppercase"
                        >
                            {nextAction}
                            <ArrowRight size={18} class="ml-2" />
                        </Button>
                    </div>
                </div>
            {/if}
        </div>
    </div>
{/if}
