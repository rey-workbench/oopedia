<script lang="ts">
    import { Navigation } from 'lucide-svelte';
</script>

<footer class="relative flex w-full flex-col overflow-hidden pt-12">
    <div
        class="flex w-full flex-col items-center justify-between gap-8 px-6 pb-[2vh] text-[9px] font-black tracking-[0.2em] text-black/40 uppercase md:flex-row md:gap-0"
    >
        <div class="flex gap-6">
            <button type="button" class="transition-colors hover:text-black focus:outline-none"
                >Instagram</button
            >
            <button type="button" class="transition-colors hover:text-black focus:outline-none"
                >Twitter</button
            >
            <button type="button" class="transition-colors hover:text-black focus:outline-none"
                >YouTube</button
            >
        </div>

        <div
            class="group cursor-pointer text-black transition-transform duration-700 hover:rotate-90"
        >
            <Navigation size={20} class="fill-current transition-transform group-hover:scale-110" />
        </div>

        <div class="flex gap-6">
            <button type="button" class="transition-colors hover:text-black focus:outline-none"
                >Kontak</button
            >
            <button type="button" class="transition-colors hover:text-black focus:outline-none"
                >Ketentuan</button
            >
            <button type="button" class="transition-colors hover:text-black focus:outline-none"
                >Privasi</button
            >
        </div>
    </div>

    <div class="pointer-events-none flex w-full translate-y-[15%] justify-center overflow-hidden">
        <span class="text-[26vw] leading-[0.7] font-black tracking-tighter text-black select-none">
            OOPEDIA
        </span>
    </div>
</footer>
