<script lang="ts">
    import Button from '@/components/ui/Button.svelte';
    import Panel from '@/components/ui/Panel.svelte';
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

<div class={`${details.color} relative overflow-hidden p-16 text-center text-white`}>
    <div class="absolute -top-10 -right-10 rotate-12 text-[10rem] opacity-20">
        {details.badge}
    </div>
    <div class="relative z-10">
        <div
            class="animate-in zoom-in-50 mx-auto mb-6 flex h-48 w-48 items-center justify-center duration-700"
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
        <h2 class="mb-3 text-4xl font-black tracking-widest uppercase drop-shadow-xl">
            {details.title}
        </h2>
        <div
            class="mb-4 inline-block rounded-full bg-white/30 px-6 py-2 text-[10px] font-black tracking-widest ring-1 ring-white/50 backdrop-blur-xl"
        >
            {details.subtitle}
        </div>
        <p class="mt-2 text-lg leading-relaxed font-medium text-white/95 drop-shadow">
            {message}
        </p>
    </div>
</div>
<div class="p-10">
    {#if xpEarned > 0}
        <div class="mb-8 flex justify-center">
            <Panel
                variant="none"
                rounded="2xl"
                padding="px-10 py-5"
                class="flex items-center gap-4 border border-amber-500/30 bg-slate-900 shadow-2xl"
            >
                <Star size={24} class="fill-current text-white" />
                <span class="text-2xl font-black tracking-tighter text-white">+{xpEarned} XP</span>
            </Panel>
        </div>
    {/if}
    <Button
        variant="primary"
        onclick={onContinue}
        class="w-full border-none bg-slate-900 py-4 text-sm font-black tracking-widest uppercase shadow-xl hover:bg-slate-800"
    >
        Lanjutkan <ArrowRight size={18} class="ml-2" />
    </Button>
</div>
