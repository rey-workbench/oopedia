<script lang="ts">
    import Button from '@/components/ui/Button.svelte';
    import { DotLottieSvelte } from '@lottiefiles/dotlottie-svelte';
    import { TrendingUp, Star, ArrowRight } from 'lucide-svelte';
    import { onMount, onDestroy } from 'svelte';
    import { fly } from 'svelte/transition';

    interface Props {
        status: 'success' | 'wrong';
        message: string;
        nextAction: string;
        xpEarned: number;
        streakBonus: string | null;
        recommendation: string | null;
        onContinue: () => void;
        onTryAgain?: () => void;
    }

    let {
        status,
        message,
        nextAction,
        xpEarned,
        streakBonus,
        recommendation,
        onContinue,
        onTryAgain,
    }: Props = $props();

    const isSuccess = $derived(status === 'success');

    // Auto-advance logic
    let progress = $state(100);
    let timer: ReturnType<typeof setInterval>;
    const AUTO_ADVANCE_MS = $derived(isSuccess ? 3000 : 5000);
    const TICK_MS = 50;

    onMount(() => {
        const shouldAutoAdvance = status === 'success';
        const startTime = Date.now();
        timer = setInterval(() => {
            const elapsed = Date.now() - startTime;
            progress = Math.max(0, 100 - (elapsed / AUTO_ADVANCE_MS) * 100);

            if (progress <= 0) {
                clearInterval(timer);
                if (shouldAutoAdvance) {
                    onContinue();
                }
            }
        }, TICK_MS);
    });

    onDestroy(() => {
        if (timer) clearInterval(timer);
    });
</script>

<style>
    :global(.animate-spin-slow) {
        animation: spin 8s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    :global(.overflow-hidden canvas) {
        transform: scale(1.2);
        transform-origin: center;
    }
</style>

<!-- Main fixed container for the bottom feedback bar -->
<div
    in:fly={{ y: 100, duration: 500 }}
    class={`fixed inset-x-0 bottom-0 z-1000 transform transition-all duration-500 ease-out 
    ${isSuccess ? 'border-t-4 border-emerald-500 bg-emerald-50 shadow-[0_-20px_50px_-12px_rgba(16,185,129,0.25)]' : 'border-t-4 border-rose-500 bg-rose-50 shadow-[0_-20px_50px_-12px_rgba(244,63,94,0.25)]'}`}
>
    <!-- Auto-advance progress bar -->
    <div
        class={`absolute top-[-4px] left-0 h-1 transition-all duration-75 ease-linear
        ${isSuccess ? 'bg-emerald-400' : 'bg-rose-400'}`}
        style="width: {progress}%"
    ></div>

    <div class="mx-auto max-w-5xl px-6 py-4 md:py-6">
        <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
            <!-- Left: Hero Icon & Status -->
            <div class="flex items-center gap-5">
                <div
                    class={`hidden h-16 w-16 shrink-0 items-center justify-center rounded-2xl border-2 bg-white shadow-md md:flex ${isSuccess ? 'border-emerald-100' : 'border-rose-100'}`}
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
                        class={`text-xl font-black tracking-tight ${isSuccess ? 'text-emerald-800' : 'text-rose-800'}`}
                    >
                        {isSuccess ? 'Luar Biasa!' : 'Perlu Belajar Lagi'}
                    </h2>
                    <p
                        class={`mt-0.5 text-sm font-bold ${isSuccess ? 'text-emerald-600/80' : 'text-rose-600/80'}`}
                    >
                        {message}
                    </p>

                    {#if recommendation}
                        <p
                            class="mx-auto mt-1.5 flex w-fit items-center gap-2 rounded-full border border-rose-100 bg-white/80 px-3 py-1 text-[10px] font-black text-rose-500 shadow-sm md:mx-0"
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
                {#if !isSuccess && onTryAgain}
                    <Button
                        variant="outline"
                        onclick={onTryAgain}
                        class="flex-1 border-2 border-white bg-white/50 px-6 py-3 text-xs font-black tracking-widest text-rose-600 uppercase shadow-sm transition-all hover:bg-white active:translate-y-1 md:flex-none"
                    >
                        COBA LAGI
                    </Button>
                {/if}

                <Button
                    variant="primary"
                    onclick={onContinue}
                    class={`group relative flex-1 overflow-hidden px-10 py-3.5 font-black tracking-widest uppercase shadow-lg active:translate-y-1 md:w-56
                    ${isSuccess ? 'border-emerald-700 bg-emerald-500 hover:bg-emerald-600' : 'border-rose-700 bg-rose-500 hover:bg-rose-600'}`}
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
