<script lang="ts">
    import Badge from '@/components/ui/Badge.svelte';
    import Button from '@/components/ui/Button.svelte';
    import { DotLottieSvelte } from '@lottiefiles/dotlottie-svelte';
    import { ArrowRight, AlertTriangle, BookOpen } from 'lucide-svelte';

    interface Props {
        message: string;
        status: 'success' | 'wrong';
        nextAction: string;
        recommendation: string | null;
        onContinue: () => void;
    }

    let { message, status, nextAction, recommendation, onContinue }: Props = $props();
</script>

<div class="p-12 text-center">
    <div
        class="mx-auto mb-6 flex h-40 w-40 items-center justify-center transition-transform duration-500 hover:scale-110"
    >
        <div class="h-full w-full overflow-hidden rounded-3xl">
            <DotLottieSvelte
                src="/assets/lottie/quiz/graduation.json"
                loop={true}
                autoplay={true}
                backgroundColor="transparent"
                renderConfig={{ devicePixelRatio: window?.devicePixelRatio || 1 }}
            />
        </div>
    </div>
    <Badge variant="warning" size="sm" class="mb-4 border-2 font-black tracking-widest uppercase">
        <AlertTriangle size={14} class="mr-2" /> Rekomendasi Adaptif
    </Badge>
    <h2 class="mb-2 text-3xl font-black tracking-tight text-slate-900 uppercase">
        {status === 'success' ? 'BAGUS SEKALI!' : 'KAMI ADA UNTUKMU'}
    </h2>
    <p class="mx-auto mb-8 px-4 text-lg font-medium text-slate-500">
        {message}
    </p>

    {#if recommendation}
        <div class="group mb-8 text-left">
            <div
                class="hover:border-primary-200 relative overflow-hidden rounded-2xl border-2 border-slate-100 bg-white p-5 shadow-sm transition-all hover:shadow-md"
            >
                <div class="flex items-center gap-4">
                    <div
                        class="bg-primary-50 text-primary-600 flex h-12 w-12 items-center justify-center rounded-xl shadow-inner"
                    >
                        <BookOpen size={24} />
                    </div>
                    <div class="flex-1">
                        <span
                            class="text-primary-500 text-[10px] font-black tracking-[0.2em] uppercase"
                            >Rekomendasi Materi</span
                        >
                        <p class="text-lg leading-tight font-black text-slate-800">
                            {recommendation}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    {/if}

    <Button
        variant="primary"
        onclick={onContinue}
        class="w-full border-none bg-slate-900 py-4 text-sm font-black tracking-widest uppercase shadow-xl hover:bg-slate-800"
    >
        {nextAction}
        <ArrowRight size={18} class="ml-2" />
    </Button>
</div>
