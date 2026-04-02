<script lang="ts">
    import { Play } from 'lucide-svelte';
</script>

<style>
    @keyframes spin-slow {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
    .animate-spin-slow {
        animation: spin-slow 15s linear infinite;
    }
</style>

<section class="mb-40 h-auto w-full px-4 sm:px-8">
    <div
        class="group relative flex aspect-[21/9] w-full cursor-pointer items-center justify-center overflow-hidden rounded-xl bg-[#E1E6DC] shadow-2xl"
    >
        <img
            src="/images/landing/abstract1.png"
            alt="film blur"
            class="absolute inset-0 h-full w-full scale-125 object-cover opacity-40 blur-[40px] transition-all duration-[2s] group-hover:scale-[1.3] group-hover:blur-[50px]"
        />

        <div
            class="relative z-10 flex h-24 w-24 items-center justify-center opacity-90 drop-shadow-2xl transition-transform duration-700 group-hover:scale-110"
        >
            <div
                class="animate-spin-slow absolute inset-0 rounded-full border-[4px] border-dotted border-white/90"
            ></div>
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white pl-1">
                <Play size={16} class="fill-black text-black" />
            </div>
        </div>
    </div>
</section>
