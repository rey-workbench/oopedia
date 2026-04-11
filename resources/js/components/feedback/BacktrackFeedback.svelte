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

<div class="relative overflow-hidden">
    <!-- Background Decor -->
    <div class="absolute -top-24 -right-24 h-64 w-64 rounded-full bg-rose-500/10 blur-3xl"></div>
    <div class="absolute -bottom-24 -left-24 h-64 w-64 rounded-full bg-slate-500/10 blur-3xl"></div>

    <div class="relative z-10 px-8 pt-12 pb-10 text-center sm:px-12">
        <!-- Illustration -->
        <div class="mx-auto mb-8 flex h-44 w-44 items-center justify-center">
            <div
                class="relative h-full w-full overflow-hidden rounded-[2.5rem] border-4 border-white bg-white shadow-2xl shadow-rose-100"
            >
                <div class="absolute inset-0 bg-linear-to-b from-rose-50/50 to-transparent"></div>
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

        <div class="mb-8">
            <Badge
                variant="danger"
                size="sm"
                class="mb-4 transform border-2 border-rose-200 bg-rose-50 px-4 py-1.5 font-black tracking-[0.2em] text-rose-700 uppercase transition-transform hover:scale-105"
            >
                <ArrowDown size={14} class="mr-2" /> Penyesuaian Alur
            </Badge>
            <h2
                id="backtrack-feedback-header"
                class="mx-auto max-w-sm text-3xl leading-tight font-black tracking-tight text-slate-900 uppercase sm:text-4xl"
            >
                MARI KEMBALI KE DASAR
            </h2>
            <div class="mx-auto mt-3 h-1.5 w-20 rounded-full bg-linear-to-r from-rose-500 to-slate-500"></div>
            <p class="mx-auto mt-6 max-w-md text-lg font-medium leading-relaxed text-slate-600">
                {message}
            </p>
        </div>

        {#if recommendation}
            <div id="backtrack-recommendation-card" class="group relative mb-10 text-left">
                <!-- Card Shine Effect -->
                <div
                    class="absolute -inset-0.5 rounded-3xl bg-linear-to-r from-rose-500 to-slate-500 opacity-20 blur-sm transition-all group-hover:opacity-40"
                ></div>
                <div
                    class="relative overflow-hidden rounded-2xl border border-white bg-white/80 p-6 shadow-xl backdrop-blur-xl transition-all group-hover:translate-y-[-2px]"
                >
                    <div class="flex items-center gap-5">
                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-linear-to-br from-rose-500 to-rose-600 text-white shadow-lg shadow-rose-200"
                        >
                            <BookOpen size={28} />
                        </div>
                        <div class="flex-1">
                            <span class="text-[11px] font-black tracking-[0.2em] text-rose-500 uppercase"
                                >Materi Remedial</span
                            >
                            <p class="text-xl leading-tight font-bold text-slate-800">
                                {recommendation}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        {/if}

        <Button
            id="backtrack-continue-btn"
            variant="primary"
            onclick={onContinue}
            class="group w-full border-b-6 border-slate-950 bg-slate-900 py-5 text-base font-black tracking-widest uppercase shadow-2xl transition-all hover:bg-slate-800 hover:shadow-rose-200 active:translate-y-[4px] active:border-b-0"
        >
            <span class="flex items-center justify-center gap-3">
                {nextAction}
                <ArrowRight
                    size={20}
                    class="transition-transform duration-300 group-hover:translate-x-1"
                />
            </span>
        </Button>

        <p class="mt-6 text-[11px] font-black tracking-[0.2em] text-slate-400 uppercase">
            Sistem Adaptif • Membantumu Berkembang
        </p>
    </div>
</div>
