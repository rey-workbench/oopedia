<script lang="ts">
    import { scrollReveal } from '@/utils/animations';
    import { Brain, Zap, Trophy, Users, ShieldCheck } from '@lucide/svelte';
    import { Motion } from 'svelte-motion';

    const features = [
        {
            title: 'Adaptive Engine',
            desc: 'Algoritma forward chaining menyesuaikan materi dengan kecepatan belajar Anda.',
            icon: Brain,
            size: 'md:col-span-2 md:row-span-2',
            bg: 'bg-[#AEC0AD]',
            img: '/images/landing/abstract1.png',
            delay: 0,
        },
        {
            title: 'Real-time Analytics',
            desc: 'Pantau progres belajar Anda dengan visualisasi data yang mendalam.',
            icon: Zap,
            size: 'md:col-span-1 md:row-span-1',
            bg: 'bg-[#D26D3F]',
            img: '/images/landing/abstract2.png',
            delay: 200,
        },
        {
            title: 'Gamification',
            desc: 'Dapatkan XP, badge, dan naiki leaderboard sambil belajar.',
            icon: Trophy,
            size: 'md:col-span-1 md:row-span-1',
            bg: 'bg-[#EECA9D]',
            img: '/images/landing/abstract3.png',
            delay: 400,
        },
        {
            title: 'Komunitas',
            desc: 'Belajar bersama ribuan mahasiswa lainnya di semua angkatan.',
            icon: Users,
            size: 'md:col-span-1 md:row-span-2',
            bg: 'bg-slate-900',
            dark: true,
            img: '/images/landing/abstract4.png',
            delay: 600,
        },
        {
            title: 'Sertifikasi',
            desc: 'Dapatkan sertifikat resmi setelah menyelesaikan modul pembelajaran.',
            icon: ShieldCheck,
            size: 'md:col-span-2 md:row-span-1',
            bg: 'bg-[#FDFDFB]',
            img: '/images/landing/abstract2.png',
            delay: 800,
        },
    ];
</script>

<section id="fitur" class="mx-auto max-w-7xl px-6 py-32">
    <div class="mb-20 text-center" use:scrollReveal>
        <h2 class="font-display mb-6 text-4xl font-black tracking-tight text-slate-900 md:text-6xl">
            Belajar lebih cerdas,<br />bukan lebih keras.
        </h2>
        <p class="mx-auto max-w-2xl text-lg font-medium text-slate-900/40">
            OOPedia menggabungkan teknologi AI dengan kurikulum yang terstruktur untuk memberikan
            pengalaman belajar yang paling efektif bagi Anda.
        </p>
    </div>

    <div class="grid h-auto grid-cols-1 gap-6 md:h-[800px] md:grid-cols-3 md:grid-rows-3">
        {#each features as f}
            {@const Icon = f.icon}
            <Motion whileHover={{ y: -8, scale: 1.01 }} transition={{ duration: 0.3 }} let:motion>
                <div
                    use:motion
                    use:scrollReveal={{ delay: f.delay, y: 50 }}
                    class="relative overflow-hidden rounded-3xl p-8 {f.size} {f.bg} group border-2 border-slate-950/5 shadow-sm"
                >
                    <div class="relative z-10 flex h-full flex-col justify-between">
                        <div>
                            <div
                                class="mb-6 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-md"
                            >
                                <Icon size={24} class={f.dark ? 'text-white' : 'text-slate-900'} />
                            </div>
                            <h3
                                class="mb-4 text-3xl leading-tight font-black tracking-tight {f.dark
                                    ? 'text-white'
                                    : 'text-slate-900'}"
                            >
                                {f.title}
                            </h3>
                            <p
                                class="max-w-[280px] text-sm leading-relaxed font-medium {f.dark
                                    ? 'text-white/60'
                                    : 'text-slate-900/50'}"
                            >
                                {f.desc}
                            </p>
                        </div>

                        <div class="mt-8">
                            <button
                                class="inline-flex items-center gap-2 text-xs font-black tracking-widest uppercase {f.dark
                                    ? 'text-white'
                                    : 'text-slate-900'} opacity-0 transition-all group-hover:translate-x-2 group-hover:opacity-100"
                            >
                                Pelajari lanjut →
                            </button>
                        </div>
                    </div>

                    <!-- Abstract Image Decorations -->
                    <img
                        src={f.img}
                        alt=""
                        class="absolute -right-12 -bottom-12 w-64 opacity-20 mix-blend-multiply grayscale transition-all duration-700 group-hover:scale-110 group-hover:rotate-6 group-hover:opacity-40"
                    />
                </div>
            </Motion>
        {/each}
    </div>
</section>
