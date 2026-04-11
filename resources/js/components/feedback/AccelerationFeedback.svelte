<script lang="ts">
    import Badge from '@/components/ui/Badge.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Panel from '@/components/ui/Panel.svelte';
    import { DotLottieSvelte } from '@lottiefiles/dotlottie-svelte';
    import { ArrowRight, Star, Zap } from 'lucide-svelte';

    interface Props {
        message: string;
        nextAction: string;
        xpEarned: number;
        onContinue: () => void;
    }

    let { message, nextAction, xpEarned, onContinue }: Props = $props();
</script>

<div class="relative overflow-hidden">
    <!-- Animated background patterns -->
    <div
        class="bg-linear-to-br from-indigo-600 via-primary-600 to-emerald-500 relative px-8 pt-16 pb-12 text-center text-white sm:px-12"
    >
        <div
            class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(255,255,255,0.2)_0%,transparent_70%)]"
        ></div>
        <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 20px 20px;"></div>

        <div class="relative z-10">
            <!-- Illustration -->
            <div class="mx-auto mb-8 flex h-48 w-48 items-center justify-center">
                <div
                    class="relative h-full w-full overflow-hidden rounded-full border-4 border-white/50 bg-white/10 p-2 shadow-2xl backdrop-blur-sm"
                >
                    <DotLottieSvelte
                        src="/assets/lottie/quiz/graduation.json"
                        loop={true}
                        autoplay={true}
                        backgroundColor="transparent"
                        renderConfig={{
                            devicePixelRatio:
                                typeof window !== 'undefined' ? window.devicePixelRatio : 1,
                        }}
                    />
                </div>
            </div>

            <Badge
                variant="secondary"
                size="sm"
                class="mb-6 border-white/40 bg-white/20 px-5 py-2 font-black tracking-[0.2em] text-white uppercase shadow-lg backdrop-blur-md"
            >
                <Zap size={14} class="mr-2 fill-current" /> Tingkatan Divalidasi
            </Badge>

            <h2
                id="acceleration-feedback-header"
                class="mb-3 text-5xl font-black tracking-widest uppercase drop-shadow-[0_4px_4px_rgba(0,0,0,0.25)] sm:text-6xl"
            >
                PERCEPATAN!
            </h2>
            <div class="mx-auto h-2 w-32 rounded-full bg-white/30 shadow-inner">
                <div class="h-full w-1/2 rounded-full bg-white animate-pulse"></div>
            </div>
            <p class="mt-8 mx-auto max-w-sm text-lg font-bold leading-relaxed text-indigo-50">
                {message}
            </p>
        </div>
    </div>

    <div class="p-10 text-center sm:p-12">
        {#if xpEarned > 0}
            <div class="mb-10 flex justify-center">
                <div class="group relative">
                    <div class="absolute -inset-1 rounded-3xl bg-linear-to-r from-amber-400 to-rose-400 opacity-30 blur-md transition-all group-hover:opacity-50"></div>
                    <Panel
                        variant="none"
                        rounded="2xl"
                        padding="px-10 py-5"
                        class="relative flex items-center gap-5 border-2 border-b-6 border-amber-400 bg-slate-900 shadow-xl transition-all active:translate-y-[2px] active:border-b-4"
                    >
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-400 text-white shadow-lg shadow-amber-500/30">
                            <Star size={28} class="fill-current" />
                        </div>
                        <div class="text-left leading-none">
                            <span class="block text-[11px] font-black tracking-[0.2em] text-amber-500 uppercase">Poin Bonus</span>
                            <span class="text-3xl font-black text-white">+{xpEarned} XP</span>
                        </div>
                    </Panel>
                </div>
            </div>
        {/if}

        <Button
            id="acceleration-continue-btn"
            variant="primary"
            onclick={onContinue}
            class="group w-full border-b-6 border-slate-950 bg-slate-900 py-6 text-lg font-black tracking-widest uppercase shadow-2xl transition-all hover:bg-slate-800 hover:shadow-emerald-200 active:translate-y-[4px] active:border-b-0"
        >
            <span class="flex items-center justify-center gap-4">
                {nextAction}
                <ArrowRight size={24} class="transition-transform duration-300 group-hover:translate-x-2" />
            </span>
        </Button>

        <p class="mt-8 text-[11px] font-black tracking-[0.3em] text-slate-400 uppercase">
            Prestasi Luar Biasa • Oopedia
        </p>
    </div>
</div>
