<script lang="ts">
    import { scrollReveal } from '@/utils/animations';
    import { Play } from 'lucide-svelte';
</script>

<section class="mx-auto mb-40 w-full max-w-6xl px-6">
    <div
        use:scrollReveal={{ y: 60, scale: 0.95 }}
        class="group relative aspect-video w-full overflow-hidden rounded-3xl border-2 border-slate-950/5 bg-[#E1E6DC] shadow-2xl md:rounded-[3rem]"
    >
        <video
            src="/videos/landing.mp4"
            autoplay
            muted
            loop
            playsinline
            class="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-105"
        ></video>

        <div class="absolute inset-0 bg-slate-900/10 transition-colors group-hover:bg-slate-900/20"></div>
        <div class="absolute bottom-4 left-4 right-4 flex items-center gap-3 rounded-2xl bg-slate-900/40 p-2 backdrop-blur-2xl border border-white/10 shadow-2xl md:bottom-8 md:left-8 md:right-auto md:gap-5 md:rounded-[2rem] md:p-2.5 md:pr-8">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-slate-900 shadow-xl transition-transform duration-500 group-hover:scale-110 group-hover:rotate-12 md:h-12 md:w-12 md:rounded-[1.25rem]">
                <Play size={16} class="fill-current translate-x-0.5 md:size-5" />
            </div>
            <div class="flex flex-col gap-0.5">
                <div class="flex items-center gap-2">
                    <span class="text-[8px] font-black tracking-[0.2em] text-white/40 uppercase md:text-[10px]">OOPedia v2</span>
                    <span class="rounded-full bg-brand-yellow px-1.5 py-0.5 text-[7px] font-black text-slate-900 uppercase md:px-2 md:text-[8px]">Kini Hadir</span>
                </div>
                <span class="text-xs font-bold text-white tracking-tight md:text-sm">Era Baru Pembelajaran Interaktif</span>
            </div>
        </div>
    </div>
</section>
