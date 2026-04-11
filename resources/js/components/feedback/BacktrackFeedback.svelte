<script lang="ts">
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

<div class="p-8 text-center sm:p-12">
    <!-- Illustration -->
    <div class="mx-auto mb-8 flex h-40 w-40 items-center justify-center">
        <div class="h-full w-full overflow-hidden rounded-3xl border-2 border-b-6 border-slate-100 bg-white shadow-sm">
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
    <div class="inline-flex items-center gap-2 rounded-2xl border-2 border-b-4 border-rose-200 bg-rose-50 px-5 py-2 font-black tracking-widest text-rose-700 uppercase">
        <ArrowDown size={18} />
        Penyesuaian Alur
    </div>

    <!-- Title & Message -->
    <div class="mt-8 mb-10">
        <h2 id="backtrack-feedback-header" class="text-3xl font-black tracking-tight text-slate-800 uppercase sm:text-4xl">
            MARI KEMBALI KE DASAR
        </h2>
        <p class="mx-auto mt-4 max-w-sm text-lg font-bold text-slate-500 leading-relaxed">
            {message}
        </p>
    </div>

    <!-- Recommendation Card -->
    {#if recommendation}
        <div id="backtrack-recommendation-card" class="mb-10 text-left transition-transform active:scale-[0.98]">
            <div class="rounded-3xl border-2 border-b-6 border-rose-200 bg-rose-50 p-6">
                <div class="flex items-center gap-5">
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl border-2 border-b-4 border-rose-300 bg-white text-rose-500">
                        <BookOpen size={32} />
                    </div>
                    <div>
                        <span class="text-[11px] font-black tracking-[0.2em] text-rose-400 uppercase">Materi Remedial</span>
                        <p class="text-xl font-black text-slate-800">
                            {recommendation}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    {/if}

    <!-- Primary Action -->
    <Button
        id="backtrack-continue-btn"
        variant="primary"
        onclick={onContinue}
        class="group w-full border-b-8 border-slate-950 bg-slate-900 py-6 text-lg font-black tracking-widest uppercase hover:bg-slate-800 active:translate-y-1 active:border-b-4"
    >
        <span class="flex items-center justify-center gap-3">
            {nextAction}
            <ArrowRight size={24} class="transition-transform group-hover:translate-x-1" />
        </span>
    </Button>

    <p class="mt-8 text-[11px] font-black tracking-[0.3em] text-slate-300 uppercase">
        Sistem Adaptif • Membantumu Berkembang
    </p>
</div>
