<script lang="ts">
    import Button from '@/components/ui/Button.svelte';
    import { DotLottieSvelte } from '@lottiefiles/dotlottie-svelte';
    import { ArrowRight, Star, TrendingUp, Timer, Hourglass } from 'lucide-svelte';
    import { fly, fade, scale } from 'svelte/transition';
    import { router } from '@inertiajs/svelte';
    import type { QuizState } from '@/states/Mahasiswa/QuizState.svelte';
    import type { FeedbackVariant } from './types';
    import type { HydratedAction } from '@/types';
    import { xpAnimationState } from '@/states/ui/xpAnimation.svelte';

    interface Props {
        state: QuizState;
    }

    let { state: quizState }: Props = $props();

    let nextAction = $derived(
        quizState.feedbackData?.adaptive_result?.triggered_rule?.actions?.[0]?.name || 'Lanjut'
    );
    let diagnosis = $derived(quizState.feedbackData?.adaptive_result?.diagnosis || null);
    let actions = $derived(quizState.feedbackData?.adaptive_result?.triggered_rule?.actions || []);
    let challengeQuestion = $derived(quizState.feedbackData?.challenge_question || null);
    let xpEarned = $derived(quizState.feedbackData?.xp_earned || 0);

    // Interactive Challenge State
    let selectedChallengeAnswer = $state<string | null>(null);
    let challengeStatus = $state<'idle' | 'correct' | 'incorrect'>('idle');

    // Categorize recommendations based on new variants
    let popupActions = $derived(actions.filter((r: HydratedAction) => r.variant === 'popup'));
    let challengeActions = $derived(
        actions.filter((r: HydratedAction) => r.variant === 'challenge')
    );
    let feedbackActions = $derived(actions.filter((r: HydratedAction) => r.variant === 'feedback'));

    // Visibility control for popups to avoid getting stuck
    let showPopups = $state(true);
    let showChallenges = $state(true);

    $effect(() => {
        if (quizState.show_feedback) {
            showPopups = true;
            showChallenges = true;

            // Play streak bonus audio if STREAK_BONUS action is present
            if (actions.some((a: any) => (typeof a === 'object' ? a.id : a) === 'STREAK_BONUS')) {
                const audio = new Audio('/sound/newLevel.mp3');
                audio.play().catch(e => console.error('Failed to play streak bonus audio:', e));
            }

            // Auto-dismiss standard popups after 3 seconds
            if (popupActions.length > 0) {
                const timer = setTimeout(() => {
                    showPopups = false;
                }, 3500);
                return () => clearTimeout(timer);
            }

            // Trigger XP flying stars if earned
            if (xpEarned > 0) {
                setTimeout(() => {
                    const source = document.getElementById('xp-reward-source');
                    if (source) {
                        const rect = source.getBoundingClientRect();
                        xpAnimationState.trigger(
                            rect.left + rect.width / 2,
                            rect.top + rect.height / 2,
                            Math.floor(Math.min(xpEarned / 5 + 3, 12))
                        );
                    }
                }, 800);
            }
        }
        return;
    });

    const TICK_MS = 50;
    const AUTO_ADVANCE_MS_SUCCESS = 10000;
    const AUTO_ADVANCE_MS_WRONG = 10000;
    let progress = $state(100);

    type FeedbackTone = {
        container: string;
        progress: string;
        iconBorder: string;
        title: string;
        body: string;
        chipBorder: string;
        chipText: string;
        button?: string;
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
        button: 'bg-rose-600 text-white hover:bg-rose-700',
    };

    const SUCCESS_TONES: Record<string, FeedbackTone> = {
        feedback: {
            container:
                'border-t-4 border-emerald-500 bg-emerald-50 shadow-[0_-20px_50px_-12px_rgba(16,185,129,0.25)]',
            progress: 'bg-emerald-400',
            iconBorder: 'border-emerald-100',
            title: 'text-emerald-800',
            body: 'text-emerald-600/80',
            chipBorder: 'border-emerald-100',
            chipText: 'text-emerald-600',
            button: 'bg-emerald-600 text-white hover:bg-emerald-700',
        },
        challenge: {
            container:
                'border-t-4 border-amber-600 bg-amber-50 shadow-[0_-20px_50px_-12px_rgba(217,119,6,0.25)]',
            progress: 'bg-amber-500',
            iconBorder: 'border-amber-200',
            title: 'text-amber-900',
            body: 'text-amber-800/80',
            chipBorder: 'border-amber-200',
            chipText: 'text-amber-700',
            button: 'bg-amber-600 text-white hover:bg-amber-700',
        },
        popup: {
            container: 'bg-slate-900 text-white border-slate-700',
            progress: 'bg-indigo-500',
            iconBorder: 'border-slate-700',
            title: 'text-white',
            body: 'text-slate-400',
            chipBorder: 'border-slate-700',
            chipText: 'text-slate-300',
            button: 'bg-indigo-600 text-white hover:bg-indigo-700',
        },
    };

    function getFeedbackStatus(): 'success' | 'wrong' {
        return quizState.feedbackData?.status === 'success' ? 'success' : 'wrong';
    }

    function getFeedbackVariant(): FeedbackVariant {
        const actions = quizState.feedbackData?.adaptive_result?.triggered_rule?.actions || [];
        const primaryAction = actions[0];
        const actionVariant = primaryAction?.variant ?? null;

        return (actionVariant as FeedbackVariant) || 'feedback';
    }

    function getFeedbackTone(status: 'success' | 'wrong'): FeedbackTone {
        if (status === 'wrong') return WRONG_TONE;
        const currentVariant = getFeedbackVariant();
        // Bracket access to satisfy index signature with strict fallback
        return (SUCCESS_TONES[currentVariant as string] ||
            SUCCESS_TONES['feedback']) as FeedbackTone;
    }

    function getFeedbackTitle(status: 'success' | 'wrong'): string {
        const triggeredRule = quizState.feedbackData?.adaptive_result?.triggered_rule;

        if (triggeredRule?.rule?.name) {
            return triggeredRule.rule.name;
        }

        return status === 'success' ? 'Berhasil!' : 'Belum Tepat';
    }

    $effect(() => {
        if (!quizState.show_feedback || !quizState.feedbackData) {
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
    let feedbackTone = $derived(getFeedbackTone(feedbackStatus));
    let feedbackTitle = $derived(getFeedbackTitle(feedbackStatus));
    let displayTitle = $derived(quizState.adaptiveTriggeredRule?.rule?.name || feedbackTitle);
    let displayMessage = $derived.by(() => {
        const remedialMsg = quizState.studentState?.adaptive_engine?.adaptive_state?.['remedial_message'];
        if (isRemedial && remedialMsg) {
            return remedialMsg as string;
        }
        return quizState.feedbackData?.message || '';
    });
    let recommendation = $derived(
        quizState.adaptiveTriggeredRule?.rule?.recommendation !== displayMessage
            ? quizState.adaptiveTriggeredRule?.rule?.recommendation
            : null
    );

    const isRemedial = $derived(
        actions.some(
            (a: any) =>
                (typeof a === 'object' ? a.id : a) === 'REMEDIAL' ||
                (typeof a === 'object' ? a.id : a) === 'REMEDIAL_INTENSIVE'
        )
    );
    const remedialUrl = $derived(quizState.feedbackData?.next_url);

    function handleAction() {
        if (isRemedial && remedialUrl) {
            router.visit(remedialUrl);
            return;
        }

        if (quizState.feedbackData?.status === 'success' || quizState.feedbackData?.next_url) {
            quizState.handleNext();
        } else {
            quizState.handleTryAgain();
        }
    }
</script>

{#if quizState.show_feedback && quizState.feedbackData}
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
                        <div class="mb-1 flex items-center gap-2">
                            <h2
                                id="feedback-status-title"
                                class={`text-xl font-black tracking-tight ${feedbackTone.title}`}
                            >
                                {displayTitle}
                            </h2>
                            {#if diagnosis}
                                <span
                                    class={`rounded-full border bg-white/50 px-2 py-0.5 text-[10px] font-black tracking-widest uppercase ${feedbackTone.chipBorder} ${feedbackTone.chipText}`}
                                >
                                    {diagnosis}
                                </span>
                            {/if}
                        </div>
                        <p class={`mt-0.5 text-sm font-bold leading-relaxed ${feedbackTone.body}`}>
                            {displayMessage}
                        </p>

                        {#if recommendation}
                            <p class={`mt-2 text-xs font-medium italic opacity-80 ${feedbackTone.body}`}>
                                {recommendation}
                            </p>
                        {/if}

                        {#if feedbackActions.length > 0}
                            <div class="mt-2 flex flex-wrap gap-2">
                                {#each feedbackActions as rec, i}
                                    {@const name = typeof rec === 'string' ? rec : rec?.name}
                                    {@const id = typeof rec === 'string' ? rec : rec?.id}
                                    {#if name && id !== 'STREAK_BONUS'}
                                        <p
                                            in:fly={{ y: 10, duration: 500, delay: 1500 + i * 150 }}
                                            class={`flex items-center gap-2 rounded-full bg-white/80 px-3 py-1 text-[10px] font-black tracking-wider uppercase shadow-sm ${feedbackTone.chipBorder} ${feedbackTone.chipText}`}
                                        >
                                            <TrendingUp size={12} />
                                            {name}
                                        </p>
                                    {/if}
                                {/each}
                            </div>
                        {/if}
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    {#if xpEarned > 0}
                        <div
                            id="xp-reward-source"
                            class="flex items-center gap-2 rounded-xl border-2 border-white bg-white/80 px-3 py-1.5 shadow-sm transition-all hover:scale-110"
                        >
                            <Star size={16} class="fill-amber-400 text-amber-400" />
                            <span class="text-base font-black tracking-tight text-slate-700"
                                >+{xpEarned}</span
                            >
                        </div>
                    {/if}

                    {#if actions.some((r) => (typeof r === 'string' ? r : r?.id) === 'STREAK_BONUS')}
                        <div
                            class="flex items-center gap-2 rounded-xl border-2 border-white bg-white/80 px-3 py-1.5 shadow-sm"
                        >
                            <TrendingUp size={16} class="text-orange-500" />
                            <span class="text-xs font-black text-orange-600">Streak Bonus!</span>
                        </div>
                    {/if}
                </div>

                <div class="flex w-full items-center justify-center md:w-auto">
                    <Button
                        id="feedback-continue-btn"
                        variant="primary"
                        onclick={handleAction}
                        disabled={quizState.isNavigating}
                        class="w-full md:w-64"
                    >
                        <span
                            class="flex items-center justify-center gap-2 text-xs font-black tracking-widest uppercase"
                        >
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

<!-- Popup Variants (Stylized fullscreen overlay like Hooray!) -->
{#if quizState.show_feedback && quizState.feedbackData && popupActions.length > 0 && showPopups}
    <div
        class="pointer-events-auto fixed inset-0 z-10001 flex cursor-pointer flex-col items-center justify-center bg-white/60 backdrop-blur-md"
        transition:fade={{ duration: 400 }}
        role="button"
        tabindex="0"
        aria-label="Tutup popup"
        onkeydown={(e) => e.key === 'Enter' && (showPopups = false)}
        onclick={() => (showPopups = false)}
    >
        {#each popupActions as action, i}
            <div in:scale={{ start: 0.5, duration: 800, delay: 200 + i * 200 }}>
                <h1
                    class="text-center text-5xl font-black tracking-tighter uppercase italic drop-shadow-2xl select-none md:text-7xl {action.id ===
                    'GIVE_HINT'
                        ? 'text-emerald-500'
                        : 'text-slate-800'}"
                >
                    {action.name}
                </h1>
                {#if action.description}
                    <p
                        class="mt-4 text-center text-xl font-bold text-slate-600 drop-shadow-md md:text-2xl"
                    >
                        {action.description}
                    </p>
                {/if}
            </div>
        {/each}
    </div>
{/if}

<!-- Challenge Variants (Premium Surprise Overlay) -->
{#if quizState.show_feedback && quizState.feedbackData && challengeActions.length > 0 && challengeQuestion && showChallenges}
    <div
        class="fixed inset-0 z-10001 flex flex-col items-center justify-center overflow-hidden p-4 md:p-10"
        transition:fade={{ duration: 600 }}
    >
        <!-- Animated Background Mesh -->
        <div
            class="absolute inset-0 bg-linear-to-br from-amber-500 via-orange-600 to-amber-700"
        ></div>
        <div
            class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-30"
        ></div>

        <!-- Success State (Full Overlay) -->
        {#if challengeStatus === 'correct'}
            <div
                class="absolute inset-0 z-20 flex flex-col items-center justify-center bg-emerald-600/95 backdrop-blur-2xl"
                in:scale={{ duration: 800, start: 0.8, opacity: 0 }}
            >
                <div class="relative mb-8">
                    <div class="absolute inset-0 animate-ping rounded-full bg-white/20"></div>
                    <div
                        class="relative flex h-32 w-32 items-center justify-center rounded-full bg-white shadow-[0_0_50px_rgba(255,255,255,0.4)]"
                    >
                        <Star size={64} class="animate-bounce fill-emerald-600 text-emerald-600" />
                    </div>
                </div>

                <h2
                    class="mb-2 text-4xl font-black tracking-tighter text-white uppercase italic drop-shadow-2xl md:text-6xl"
                >
                    LUAR BIASA!
                </h2>
                <div class="mb-6 h-1 w-32 rounded-full bg-white/30"></div>

                <p
                    class="max-w-md px-6 text-center text-xl leading-relaxed font-bold text-emerald-50 md:text-2xl"
                >
                    Insting tajam! Bonus <span
                        class="text-white underline decoration-amber-400 decoration-4">100 XP</span
                    >
                    &
                    <span class="text-white underline decoration-amber-400 decoration-4"
                        >1 Hint</span
                    > telah ditambahkan ke profilmu.
                </p>

                <button
                    onclick={() => (showChallenges = false)}
                    class="group pointer-events-auto relative mt-12 flex items-center gap-3 rounded-full bg-white px-10 py-4 font-black tracking-widest text-emerald-700 uppercase shadow-2xl transition-transform hover:scale-105 active:scale-95"
                >
                    Lanjutkan Kuis
                    <ArrowRight size={20} class="transition-transform group-hover:translate-x-1" />
                </button>
            </div>
        {/if}

        <!-- Main Challenge Content -->
        <div
            class="relative z-10 flex w-full max-w-4xl flex-col items-center"
            in:fly={{ y: 50, duration: 800 }}
        >
            <!-- Header Section -->
            <div class="mb-10 flex flex-col items-center">
                <div class="relative mb-4 h-20 w-20">
                    <div class="absolute inset-0 rotate-12 rounded-2xl bg-white/20"></div>
                    <div class="absolute inset-0 -rotate-6 rounded-2xl bg-white/20"></div>
                    <div
                        class="relative flex h-full w-full items-center justify-center rounded-2xl bg-white shadow-xl"
                    >
                        <Hourglass size={32} class="animate-pulse text-orange-600" />
                    </div>
                </div>

                <h1
                    class="text-center text-4xl font-black tracking-tighter text-white uppercase italic drop-shadow-[0_10px_10px_rgba(0,0,0,0.3)] md:text-5xl"
                >
                    Tantangan Kilat
                </h1>

                <div
                    class="mt-4 flex items-center gap-2 rounded-full border border-white/20 bg-black/20 px-5 py-1.5 backdrop-blur-md"
                >
                    <TrendingUp size={16} class="text-amber-300" />
                    <p
                        class="text-xs font-black tracking-[0.2em] text-amber-300 uppercase md:text-sm"
                    >
                        Double Reward Interaction
                    </p>
                </div>
            </div>

            <!-- Question Card (Glassmorphism) -->
            <div
                class="relative w-full overflow-hidden rounded-3xl border-2 border-white/20 bg-white/10 p-6 shadow-[0_30px_100px_rgba(0,0,0,0.4)] backdrop-blur-2xl md:p-10"
            >
                <div class="absolute top-0 right-0 p-8 opacity-10">
                    <Timer size={120} />
                </div>

                <div class="relative">
                    <span
                        class="mb-6 inline-block rounded-md bg-amber-500 px-3 py-1 text-[10px] font-black tracking-widest text-white uppercase shadow-lg"
                    >
                        Materi Berbeda
                    </span>

                    <h3
                        class="mb-8 text-xl leading-[1.2] font-bold text-white drop-shadow-md md:text-2xl"
                    >
                        {@html challengeQuestion.content}
                    </h3>

                    <!-- Options Grid -->
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        {#each challengeQuestion.options || [] as answer}
                            <button
                                onclick={() => {
                                    if (challengeStatus !== 'idle') return;
                                    selectedChallengeAnswer = answer.id;
                                    challengeStatus = answer.is_correct ? 'correct' : 'incorrect';
                                    if (challengeStatus === 'incorrect') {
                                        setTimeout(() => {
                                            challengeStatus = 'idle';
                                            selectedChallengeAnswer = null;
                                        }, 800);
                                    }
                                }}
                                class="group pointer-events-auto relative flex items-center gap-4 overflow-hidden rounded-2xl border-2 p-5 text-left transition-all duration-300
                                    {selectedChallengeAnswer === answer.id
                                    ? answer.is_correct
                                        ? 'border-emerald-400 bg-emerald-500/20'
                                        : 'animate-shake border-rose-500 bg-rose-500/20'
                                    : 'border-white/10 bg-white/5 hover:-translate-y-1 hover:border-white/30 hover:bg-white/15'}"
                            >
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border-2 border-white/20 font-black text-white transition-colors group-hover:border-white/50"
                                >
                                    {selectedChallengeAnswer === answer.id &&
                                    challengeStatus === 'correct'
                                        ? '✓'
                                        : '?'}
                                </div>
                                <span
                                    class="text-sm leading-tight font-bold text-white md:text-base"
                                    >{answer.text}</span
                                >

                                <!-- Hover Shimmer -->
                                <div
                                    class="absolute inset-0 -translate-x-full bg-linear-to-r from-transparent via-white/5 to-transparent transition-transform duration-1000 group-hover:translate-x-full"
                                ></div>
                            </button>
                        {/each}
                    </div>
                </div>
            </div>

            <!-- Skip Button -->
            <button
                onclick={() => (showChallenges = false)}
                class="pointer-events-auto mt-12 flex items-center gap-2 text-xs font-bold tracking-widest text-white/50 uppercase transition-colors hover:text-white"
            >
                Lewati Tantangan Ini
                <ArrowRight size={14} />
            </button>
        </div>
    </div>
{/if}
