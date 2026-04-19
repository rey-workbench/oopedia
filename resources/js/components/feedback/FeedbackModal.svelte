<script lang="ts">
    import Button from '@/components/ui/Button.svelte';
    import { DotLottieSvelte } from '@lottiefiles/dotlottie-svelte';
    import { ArrowRight, Star, TrendingUp } from 'lucide-svelte';
    import { fly } from 'svelte/transition';
    import type { QuestionShowState } from '@/states/Mahasiswa/QuizState.svelte';
    import type { FeedbackVariant } from './types';

    interface Props {
        state: QuestionShowState;
    }

    let { state: quizState }: Props = $props();

    const RULE_TO_VARIANT: Record<string, FeedbackVariant> = {
        RULE_01: 'intervention', // RuleFinalProjectVisualPersistentFail
        RULE_02: 'intervention', // RuleFinalProjectTextualPersistentFail
        RULE_03: 'intervention', // RulePersistentVisualSafetyNet
        RULE_04: 'intervention', // RulePersistentTextualSafetyNet
        RULE_05: 'intervention', // RuleVisualCrisisIntervention
        RULE_06: 'intervention', // RuleTextualCrisisIntervention
        RULE_07: 'intervention', // RuleVisualProjectRevision
        RULE_08: 'intervention', // RuleTextualProjectRevision
        RULE_09: 'certificate', // RuleGoldCertificate
        RULE_10: 'certificate', // RuleSilverCertificate
        RULE_11: 'certificate', // RuleBronzeCertificate
        RULE_12: 'intervention', // RuleSyntaxRecovery
        RULE_13: 'intervention', // RuleLogicRecovery
        RULE_14: 'backtrack', // RuleCriticalBacktracking
        RULE_15: 'certificate', // RuleModuleGraduation
        RULE_16: 'result', // RuleMasteryMedium
        RULE_17: 'acceleration', // RuleAcceleratedMaterialPromotion
        RULE_18: 'acceleration', // RuleAcceleratedJump
        RULE_19: 'intervention', // RuleRemedialIndependent
        RULE_20: 'result', // RuleStandardPromotion
    };

    const ACTION_TO_VARIANT: Record<string, FeedbackVariant> = {
        H01: 'intervention',
        H02: 'intervention',
        H03: 'intervention',
        H04: 'intervention',
        H05: 'result',
        H06: 'acceleration',
        H07: 'backtrack',
        H08: 'certificate',
        H09: 'certificate',
        H10: 'certificate',
        H11: 'certificate',
        H12: 'intervention',
        H13: 'intervention',
        H14: 'intervention',
        H15: 'intervention',
        H16: 'acceleration',
    };

    let ruleId = $derived(quizState.feedbackData?.adaptiveResult?.triggered_rule?.id || null);
    let actionCode = $derived(
        quizState.feedbackData?.adaptiveResult?.triggered_rule?.action || null
    );
    let nextAction = $derived(
        quizState.feedbackData?.adaptiveResult?.new_state?.next_action_data?.label ||
            (quizState.feedbackData?.status === 'success' ? 'Soal Berikutnya' : 'Lihat Materi')
    );
    let recommendation = $derived(
        quizState.feedbackData?.adaptiveResult?.new_state?.recommendation || null
    );
    let certification = $derived(
        quizState.feedbackData?.adaptiveResult?.new_state?.certification || null
    );
    let xpEarned = $derived(quizState.feedbackData?.adaptiveResult?.global_xp_earned || 0);
    let streakBonus = $derived(quizState.feedbackData?.adaptiveResult?.streak_bonus || null);
    let interventionType = $derived(
        quizState.feedbackData?.adaptiveResult?.new_state?.intervention_type || null
    );

    const TICK_MS = 50;
    const AUTO_ADVANCE_MS_SUCCESS = 10000000;
    const AUTO_ADVANCE_MS_WRONG = 10000000;
    let progress = $state(100);

    type FeedbackTone = {
        container: string;
        progress: string;
        iconBorder: string;
        title: string;
        body: string;
        chipBorder: string;
        chipText: string;
        primary: string;
        tryAgain: string;
    };

    const WRONG_TONE: FeedbackTone = {
        container:
            'border-t-4 border-rose-500 bg-rose-50 shadow-[0_-20px_50px_-12px_rgba(244,63,94,0.25)]',
        progress: 'bg-rose-400',
        iconBorder: 'border-rose-100',
        title: 'text-rose-800',
        body: 'text-rose-600/80',
        chipBorder: 'border-rose-100',
        chipText: 'text-rose-500',
        primary: 'border-rose-700 bg-rose-500 hover:bg-rose-600',
        tryAgain: 'text-rose-600',
    };

    const SUCCESS_TONE_BY_VARIANT: Record<FeedbackVariant, FeedbackTone> = {
        result: {
            container:
                'border-t-4 border-emerald-500 bg-emerald-50 shadow-[0_-20px_50px_-12px_rgba(16,185,129,0.25)]',
            progress: 'bg-emerald-400',
            iconBorder: 'border-emerald-100',
            title: 'text-emerald-800',
            body: 'text-emerald-600/80',
            chipBorder: 'border-emerald-100',
            chipText: 'text-emerald-600',
            primary: 'border-emerald-700 bg-emerald-500 hover:bg-emerald-600',
            tryAgain: 'text-emerald-600',
        },
        acceleration: {
            container:
                'border-t-4 border-teal-500 bg-teal-50 shadow-[0_-20px_50px_-12px_rgba(20,184,166,0.25)]',
            progress: 'bg-teal-400',
            iconBorder: 'border-teal-100',
            title: 'text-teal-800',
            body: 'text-teal-700/80',
            chipBorder: 'border-teal-100',
            chipText: 'text-teal-600',
            primary: 'border-teal-700 bg-teal-500 hover:bg-teal-600',
            tryAgain: 'text-teal-600',
        },
        certificate: {
            container:
                'border-t-4 border-amber-500 bg-amber-50 shadow-[0_-20px_50px_-12px_rgba(245,158,11,0.25)]',
            progress: 'bg-amber-400',
            iconBorder: 'border-amber-100',
            title: 'text-amber-800',
            body: 'text-amber-700/80',
            chipBorder: 'border-amber-100',
            chipText: 'text-amber-600',
            primary: 'border-amber-700 bg-amber-500 hover:bg-amber-600',
            tryAgain: 'text-amber-600',
        },
        intervention: {
            container:
                'border-t-4 border-indigo-500 bg-indigo-50 shadow-[0_-20px_50px_-12px_rgba(99,102,241,0.25)]',
            progress: 'bg-indigo-400',
            iconBorder: 'border-indigo-100',
            title: 'text-indigo-800',
            body: 'text-indigo-700/80',
            chipBorder: 'border-indigo-100',
            chipText: 'text-indigo-600',
            primary: 'border-indigo-700 bg-indigo-500 hover:bg-indigo-600',
            tryAgain: 'text-indigo-600',
        },
        backtrack: {
            container:
                'border-t-4 border-fuchsia-500 bg-fuchsia-50 shadow-[0_-20px_50px_-12px_rgba(217,70,239,0.25)]',
            progress: 'bg-fuchsia-400',
            iconBorder: 'border-fuchsia-100',
            title: 'text-fuchsia-800',
            body: 'text-fuchsia-700/80',
            chipBorder: 'border-fuchsia-100',
            chipText: 'text-fuchsia-600',
            primary: 'border-fuchsia-700 bg-fuchsia-500 hover:bg-fuchsia-600',
            tryAgain: 'text-fuchsia-600',
        },
    };

    function getFeedbackStatus(): 'success' | 'wrong' {
        return quizState.feedbackData?.status === 'success' ? 'success' : 'wrong';
    }

    function getFeedbackVariant(): FeedbackVariant {
        if (ruleId && RULE_TO_VARIANT[ruleId]) {
            return RULE_TO_VARIANT[ruleId];
        }

        if (actionCode && ACTION_TO_VARIANT[actionCode]) {
            return ACTION_TO_VARIANT[actionCode];
        }

        if (
            interventionType?.includes('crisis') ||
            interventionType?.includes('recovery') ||
            interventionType?.includes('persistent') ||
            interventionType?.includes('project_revision') ||
            interventionType?.includes('safety')
        ) {
            return 'intervention';
        }

        if (certification) {
            return 'certificate';
        }

        return 'result';
    }

    function getFeedbackTone(
        currentVariant: FeedbackVariant,
        status: 'success' | 'wrong'
    ): FeedbackTone {
        if (status === 'wrong') {
            return WRONG_TONE;
        }

        return SUCCESS_TONE_BY_VARIANT[currentVariant] ?? SUCCESS_TONE_BY_VARIANT.result;
    }

    function getFeedbackTitle(
        currentVariant: FeedbackVariant,
        status: 'success' | 'wrong'
    ): string {
        if (status === 'wrong') {
            return 'Perlu Belajar Lagi';
        }

        if (currentVariant === 'certificate' && actionCode === 'H08' && !certification) {
            return 'Kelulusan Modul Tercapai!';
        }

        const titleByVariant: Record<FeedbackVariant, string> = {
            result: 'Luar Biasa!',
            acceleration: 'Percepatan Aktif!',
            certificate: 'Sertifikat Tercapai!',
            intervention: 'Bantuan Adaptif Aktif',
            backtrack: 'Penyesuaian Alur',
        };

        return titleByVariant[currentVariant] ?? 'Luar Biasa!';
    }

    $effect(() => {
        if (!quizState.showFeedback || !quizState.feedbackData || variant !== 'result') {
            progress = 100;

            return;
        }

        const shouldAutoAdvance = feedbackStatus === 'success';
        const autoAdvanceMs = shouldAutoAdvance ? AUTO_ADVANCE_MS_SUCCESS : AUTO_ADVANCE_MS_WRONG;
        const startTime = Date.now();

        progress = 100;

        const timer = setInterval(() => {
            const elapsed = Date.now() - startTime;
            progress = Math.max(0, 100 - (elapsed / autoAdvanceMs) * 100);

            if (progress <= 0) {
                clearInterval(timer);

                if (shouldAutoAdvance) {
                    quizState.handleNext();
                }
            }
        }, TICK_MS);

        return () => {
            clearInterval(timer);
        };
    });

    let variant = $derived(getFeedbackVariant());
    let feedbackStatus = $derived(getFeedbackStatus());
    let isSuccess = $derived(feedbackStatus === 'success');
    let feedbackTone = $derived(getFeedbackTone(variant, feedbackStatus));
    let feedbackTitle = $derived(getFeedbackTitle(variant, feedbackStatus));
</script>

{#if quizState.showFeedback && quizState.feedbackData}
    <div
        id="feedback-result-container"
        data-variant={variant}
        in:fly={{ y: 100, duration: 500 }}
        class={`fixed inset-x-0 bottom-0 z-1000 transform transition-all duration-500 ease-out ${feedbackTone.container}`}
    >
        <div
            class={`absolute -top-1 left-0 h-1 transition-all duration-75 ease-linear ${feedbackTone.progress}`}
            style="width: {progress}%"
        ></div>

        <div class="mx-auto max-w-5xl px-6 py-4 md:py-6">
            <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
                <div class="flex items-center gap-5">
                    <div
                        class={`hidden h-16 w-16 shrink-0 items-center justify-center rounded-2xl border-2 bg-white shadow-md md:flex ${feedbackTone.iconBorder}`}
                    >
                        <div class="h-10 w-10 overflow-hidden">
                            <DotLottieSvelte
                                src="/assets/lottie/quiz/graduation.json"
                                loop={false}
                                autoplay={true}
                            />
                        </div>
                    </div>

                    <div class="text-center md:text-left">
                        <h2
                            id="feedback-status-title"
                            class={`text-xl font-black tracking-tight ${feedbackTone.title}`}
                        >
                            {feedbackTitle}
                        </h2>
                        <p class={`mt-0.5 text-sm font-bold ${feedbackTone.body}`}>
                            {quizState.feedbackData.message}
                        </p>

                        {#if recommendation}
                            <p
                                class={`mx-auto mt-1.5 flex w-fit items-center gap-2 rounded-full bg-white/80 px-3 py-1 text-xs font-black shadow-sm md:mx-0 ${feedbackTone.chipBorder} ${feedbackTone.chipText}`}
                            >
                                <TrendingUp size={12} />
                                {recommendation}
                            </p>
                        {/if}
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    {#if xpEarned > 0}
                        <div
                            class="flex items-center gap-2 rounded-xl border-2 border-white bg-white/80 px-3 py-1.5 shadow-sm"
                        >
                            <Star size={16} class="fill-amber-400 text-amber-400" />
                            <span class="text-base font-black tracking-tight text-slate-700"
                                >+{xpEarned}</span
                            >
                        </div>
                    {/if}

                    {#if streakBonus}
                        <div
                            class="flex items-center gap-2 rounded-xl border-2 border-white bg-white/80 px-3 py-1.5 shadow-sm"
                        >
                            <TrendingUp size={16} class="text-orange-500" />
                            <span class="text-xs font-black text-orange-600">{streakBonus}</span>
                        </div>
                    {/if}
                </div>

                <div class="flex w-full items-center gap-3 md:w-auto">
                    {#if !isSuccess}
                        <Button
                            variant="outline"
                            onclick={() => quizState.handleTryAgain()}
                            class={`flex-1 border-2 border-white bg-white/50 px-6 py-3 text-xs font-black tracking-widest uppercase shadow-sm transition-all hover:bg-white active:translate-y-1 md:flex-none ${feedbackTone.tryAgain}`}
                        >
                            COBA LAGI
                        </Button>
                    {/if}

                    <Button
                        id="feedback-continue-btn"
                        variant="primary"
                        onclick={() => quizState.handleNext()}
                        class={`group relative flex-1 overflow-hidden px-10 py-3.5 font-black tracking-widest uppercase shadow-lg active:translate-y-1 md:w-56 ${feedbackTone.primary}`}
                    >
                        <span class="relative z-10 flex items-center justify-center gap-2">
                            {nextAction}
                            <ArrowRight
                                size={18}
                                class="transition-transform group-hover:translate-x-1"
                            />
                        </span>
                    </Button>
                </div>
            </div>
        </div>
    </div>
{/if}
