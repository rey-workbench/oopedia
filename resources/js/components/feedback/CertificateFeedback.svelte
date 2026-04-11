<script lang="ts">
    import Button from '@/components/ui/Button.svelte';
    import { DotLottieSvelte } from '@lottiefiles/dotlottie-svelte';
    import { ArrowRight, Star } from 'lucide-svelte';
    import type { CertificateDetails } from './types';

    interface Props {
        details: CertificateDetails;
        message: string;
        xpEarned: number;
        onContinue: () => void;
    }

    let { details, message, xpEarned, onContinue }: Props = $props();
</script>

<div class={`${details.color} p-10 text-center text-white sm:p-14`}>
    <!-- Illustration -->
    <div class="mx-auto mb-8 flex h-48 w-48 items-center justify-center">
        <div class="h-full w-full overflow-hidden rounded-full border-4 border-white/50 bg-white/10 p-2 shadow-inner">
            <DotLottieSvelte
                src="/assets/lottie/quiz/graduation.json"
                loop={true}
                autoplay={true}
                backgroundColor="transparent"
                renderConfig={{
                    devicePixelRatio: typeof window !== 'undefined' ? window.devicePixelRatio : 1,
                }}
            />
        </div>
    </div>

    <!-- Status Badge -->
    <div class="inline-flex items-center gap-2 rounded-2xl border-2 border-white/30 bg-white/20 px-6 py-2.5 font-black tracking-widest text-white uppercase shadow-lg">
        <span class="text-xl">{details.badge}</span>
        {details.subtitle}
    </div>

    <!-- Title -->
    <div class="mt-8">
        <h2 id="certificate-feedback-header" class="text-4xl font-black tracking-widest text-white uppercase drop-shadow-md sm:text-5xl">
            {details.title}
        </h2>
        <p class="mx-auto mt-4 max-w-sm text-lg font-bold text-amber-50/90 leading-relaxed">
            {message}
        </p>
    </div>
</div>

<div class="bg-white p-10 text-center sm:p-12">
    {#if xpEarned > 0}
        <div class="mb-10 flex justify-center">
            <div class="rounded-3xl border-2 border-b-6 border-amber-400 bg-amber-50 px-10 py-5">
                <div class="flex items-center gap-5 text-left">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl border-2 border-b-4 border-amber-300 bg-white text-amber-500">
                        <Star size={32} class="fill-current" />
                    </div>
                    <div>
                        <span class="block text-[11px] font-black tracking-[0.2em] text-amber-500 uppercase">Mastery Bonus</span>
                        <span class="text-3xl font-black text-slate-800">+{xpEarned} XP</span>
                    </div>
                </div>
            </div>
        </div>
    {/if}

    <!-- Primary Action -->
    <Button
        id="certificate-continue-btn"
        variant="primary"
        onclick={onContinue}
        class="group w-full border-b-8 border-slate-950 bg-slate-900 py-6 text-lg font-black tracking-widest uppercase hover:bg-slate-800 active:translate-y-1 active:border-b-4"
    >
        <span class="flex items-center justify-center gap-3">
            Selesaikan Modul
            <ArrowRight size={24} class="transition-transform group-hover:translate-x-1" />
        </span>
    </Button>

    <p class="mt-8 text-[11px] font-black tracking-[0.3em] text-slate-300 uppercase">
        Bagikan Prestasimu • Oopedia
    </p>
</div>
