<script lang="ts">
    import Panel from '@/components/ui/Panel.svelte';
    import Button from '@/components/ui/Button.svelte';
    import { DotLottieSvelte } from '@lottiefiles/dotlottie-svelte';
    import { ArrowRight, Star, TrendingUp, RotateCcw } from 'lucide-svelte';

    interface Props {
        status: 'success' | 'wrong';
        message: string;
        nextAction: string;
        nextActionType: string;
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
        nextActionType,
        xpEarned,
        streakBonus,
        recommendation,
        onContinue,
        onTryAgain,
    }: Props = $props();

    const isSuccess = $derived(status === 'success');
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
        transform: scale(1.6);
        transform-origin: center;
        filter: drop-shadow(0 10px 15px rgba(0, 0, 0, 0.1));
    }
</style>

<div class="p-10 pt-16 text-center">
    <div class="mb-8 flex justify-center">
        <div
            class="relative flex h-44 w-44 items-center justify-center transition-transform duration-500 hover:scale-110"
        >
            <div class="z-10 h-full w-full overflow-hidden rounded-full">
                <DotLottieSvelte
                    src="/assets/lottie/quiz/graduation.json"
                    loop={true}
                    autoplay={true}
                    backgroundColor="transparent"
                    renderConfig={{ devicePixelRatio: window?.devicePixelRatio || 1 }}
                />
            </div>
            <div
                class={`animate-spin-slow absolute -inset-2 rounded-full border-4 border-dashed opacity-20 ${isSuccess ? 'border-emerald-500' : 'border-rose-500'}`}
            ></div>
        </div>
    </div>

    <div class="mb-2">
        <span
            class={`text-xs font-black tracking-[0.3em] uppercase ${isSuccess ? 'text-emerald-500' : 'text-rose-500'}`}
        >
            {isSuccess ? 'OK!' : 'WRONG'}
        </span>
    </div>

    <h2
        class={`mb-4 text-5xl font-black tracking-tighter uppercase ${isSuccess ? 'text-emerald-600' : 'text-rose-600'}`}
    >
        {isSuccess ? 'BENAR!' : 'KURANG TEPAT'}
    </h2>

    <p class="mx-auto mb-12 px-4 text-lg leading-relaxed font-semibold text-slate-400">
        {message}
    </p>

    <div class="flex flex-col gap-8">
        <div class="flex flex-wrap items-center justify-center gap-4">
            {#if xpEarned > 0}
                <div class="group relative">
                    <Panel
                        variant="none"
                        rounded="2xl"
                        padding="px-8 py-4"
                        class="flex items-center gap-3 border border-white/10 bg-slate-900 shadow-xl transition-all hover:scale-105 active:scale-95"
                    >
                        <Star size={20} class="fill-current text-white" />
                        <span class="text-xl font-black tracking-tighter text-white"
                            >+{xpEarned} XP</span
                        >
                    </Panel>
                </div>
            {/if}
            {#if streakBonus}
                <div class="group relative">
                    <Panel
                        variant="none"
                        rounded="2xl"
                        padding="px-8 py-4"
                        class="flex items-center gap-3 border border-amber-500/50 bg-slate-900 shadow-xl transition-all hover:scale-105 active:scale-95"
                    >
                        <TrendingUp size={20} class="text-orange-500" />
                        <div class="text-left">
                            <p
                                class="text-[14px] leading-none font-black tracking-tight text-orange-500"
                            >
                                {streakBonus}
                            </p>
                        </div>
                    </Panel>
                </div>
            {/if}
        </div>

        <div class="flex flex-col justify-center gap-4 sm:flex-row">
            {#if !isSuccess && nextActionType !== 'material' && !recommendation && onTryAgain}
                <Button
                    variant="outline"
                    onclick={onTryAgain}
                    class="border-2 border-slate-200 px-10 py-4 text-xs font-black tracking-widest uppercase hover:bg-slate-50"
                >
                    <RotateCcw size={16} class="mr-2" /> COBA LAGI
                </Button>
            {/if}
            <Button
                variant="primary"
                onclick={onContinue}
                class="w-full border-none bg-slate-900 px-16 py-4 text-xs font-black tracking-widest uppercase shadow-2xl ring-4 shadow-slate-200 ring-slate-100 transition-all hover:bg-slate-800 active:scale-95 sm:w-auto"
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
