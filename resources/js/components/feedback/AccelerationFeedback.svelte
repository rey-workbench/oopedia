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

<div class="bg-primary-600 relative overflow-hidden p-12 text-center text-white">
    <div
        class="absolute inset-0 bg-[radial-gradient(circle_at_center,var(--tw-gradient-stops))] from-white/10 to-transparent"
    ></div>
    <div class="relative z-10">
        <div class="mx-auto mb-6 flex h-40 w-40 items-center justify-center">
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
            class="mb-4 border-white/30 bg-white/30 font-black tracking-widest text-white"
        >
            <Zap size={14} class="mr-2" /> TINGKATAN DIVALIDASI
        </Badge>
        <h2 class="mb-2 text-4xl font-black tracking-widest drop-shadow-lg">PERCEPATAN!</h2>
        <p class="text-primary-50 mx-auto text-base font-medium">
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
                class="border-2 border-emerald-400 flex items-center gap-4 bg-slate-900 border-b-6 active:translate-y-[2px] active:border-b-4 transition-all"
            >
                <Star size={24} class="fill-current text-white" />
                <span class="text-2xl font-black tracking-tighter text-white">+{xpEarned} XP</span>
            </Panel>
        </div>
    {/if}
    <Button
        variant="primary"
        onclick={onContinue}
        class="w-full border-b-4 border-black bg-slate-900 py-4 text-sm font-black tracking-widest uppercase hover:bg-slate-800 active:translate-y-[2px] active:border-b-2"
    >
        {nextAction}
        <ArrowRight size={18} class="ml-2" />
    </Button>
</div>
