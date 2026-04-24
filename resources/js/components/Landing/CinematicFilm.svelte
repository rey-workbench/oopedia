<script lang="ts">
    import { scrollReveal } from '@/utils/animations';
    import { Play } from 'lucide-svelte';
</script>

<section class="mx-auto mb-40 w-full max-w-6xl px-6">
    <div
        use:scrollReveal={{ y: 60, scale: 0.95 }}
        class="group relative aspect-video w-full overflow-hidden rounded-[3rem] border-2 border-slate-950/5 bg-[#E1E6DC] shadow-2xl"
    >
        <video
            src="/videos/landing.mp4"
            autoplay
            muted
            loop
            playsinline
            class="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-105"
        ></video>

        <!-- Overlay -->
        <div class="absolute inset-0 bg-slate-900/10 transition-colors group-hover:bg-slate-900/20"></div>

        <!-- Floating Info -->
        <div class="absolute bottom-8 left-8 flex items-center gap-5 rounded-[2rem] bg-slate-900/40 p-2.5 pr-8 backdrop-blur-2xl border border-white/10 shadow-2xl">
            <div class="flex h-12 w-12 items-center justify-center rounded-[1.25rem] bg-white text-slate-900 shadow-xl transition-transform duration-500 group-hover:scale-110 group-hover:rotate-12">
                <Play size={20} class="fill-current translate-x-0.5" />
            </div>
            <div class="flex flex-col gap-0.5">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black tracking-[0.2em] text-white/40 uppercase">OOPedia v2</span>
                    <span class="rounded-full bg-brand-yellow px-2 py-0.5 text-[8px] font-black text-slate-900 uppercase">Kini Hadir</span>
                </div>
                <span class="text-sm font-bold text-white tracking-tight">Era Baru Pembelajaran Interaktif</span>
            </div>
        </div>
    </div>
</section>
