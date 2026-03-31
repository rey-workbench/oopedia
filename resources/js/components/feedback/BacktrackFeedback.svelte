<script lang="ts">
    import Badge from '@/components/ui/Badge.svelte';
    import Button from '@/components/ui/Button.svelte';
    import { DotLottieSvelte } from '@lottiefiles/dotlottie-svelte';
    import { ArrowRight, ArrowDown, BookOpen } from 'lucide-svelte';

    interface Props {
        message: string;
        nextAction: string;
        recommendation: string | null;
        onContinue: () => void;
    }

    let { message, nextAction, recommendation, onContinue }: Props = $props();
</script>

<div class="p-12 text-center">
    <div
        class="animate-in slide-in-from-top-4 mx-auto mb-8 flex h-40 w-40 items-center justify-center duration-500"
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
    <Badge variant="danger" size="sm" class="mb-4 font-black tracking-widest uppercase">
        <ArrowDown size={14} class="mr-2" /> PENYESUAIAN ALUR
    </Badge>
    <h2 class="mb-2 text-3xl font-black tracking-tight text-slate-900 uppercase">
        MARI KEMBALI KE DASAR
    </h2>
    <p class="mx-auto mb-8 px-4 text-lg font-medium text-slate-500">
        {message}
    </p>

    {#if recommendation}
        <div class="group mb-8">
            <div
                class="relative overflow-hidden rounded-2xl border-2 border-rose-100 bg-rose-50/30 p-4 transition-all hover:shadow-sm"
            >
                <div class="flex items-center justify-center gap-3">
                    <BookOpen size={20} class="text-rose-500" />
                    <span class="text-base font-black tracking-tight text-slate-800"
                        >{recommendation}</span
                    >
                </div>
            </div>
        </div>
    {/if}

    <Button
        variant="primary"
        onclick={onContinue}
        class="w-full border-none bg-slate-900 py-4 text-sm font-black tracking-widest uppercase shadow-xl shadow-rose-100 hover:bg-slate-800"
    >
        {nextAction}
        <ArrowRight size={18} class="ml-2" />
    </Button>
</div>
