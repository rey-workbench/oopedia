<script lang="ts">
    import { Navigation } from 'lucide-svelte';
</script>

<footer class="relative flex w-full flex-col overflow-hidden pt-12">
    <div
        class="flex w-full flex-col items-center justify-between gap-8 px-6 pb-[2vh] text-[9px] font-black tracking-[0.2em] text-slate-900/40 uppercase md:flex-row md:gap-0"
    >
        <div class="flex gap-6">
            <a href="https://instagram.com" class="transition-colors hover:text-slate-900">Instagram</a>
            <a href="https://twitter.com" class="transition-colors hover:text-slate-900">Twitter</a>
            <a href="https://youtube.com" class="transition-colors hover:text-slate-900">YouTube</a>
        </div>

        <div
            class="group cursor-pointer text-slate-900 transition-transform duration-700 hover:rotate-90"
        >
            <Navigation size={20} class="fill-current transition-transform group-hover:scale-110" />
        </div>

        <div class="flex gap-6">
            <a href="/kontak" class="transition-colors hover:text-slate-900">Kontak</a>
            <a href="/ketentuan" class="transition-colors hover:text-slate-900">Ketentuan</a>
            <a href="/privasi" class="transition-colors hover:text-slate-900">Privasi</a>
        </div>
    </div>

    <div class="pointer-events-none flex w-full translate-y-[15%] justify-center overflow-hidden">
        <span class="text-[26vw] leading-[0.7] font-black tracking-tighter text-slate-900 select-none">
            OOPEDIA
        </span>
    </div>
</footer>
