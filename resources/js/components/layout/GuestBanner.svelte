<script lang="ts">
    import type { Snippet } from 'svelte';
    import Button from '@/components/ui/Button.svelte';
    import Card from '@/components/ui/Card.svelte';
    import { AlertTriangle, LogIn, UserPlus, ShieldAlert } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';

    interface Props {
        show?: boolean;
        title?: string;
        message?: string;
        showActions?: boolean;
        variant?: 'banner' | 'inline';
        icon?: Snippet;
        children?: Snippet;
    }

    let {
        show = false,
        title = 'Mode Tamu Aktif!',
        message = 'Anda hanya dapat melihat sebagian materi. Untuk akses penuh, silakan login atau daftar.',
        showActions = true,
        variant = 'banner',
        icon,
        children,
    }: Props = $props();
</script>

{#if show}
    {#if variant === 'banner'}
        <div
            class="shadow-premium relative overflow-hidden rounded-3xl border border-amber-100 bg-white/70 p-8 backdrop-blur-xl sm:p-10"
        >
            <div class="absolute -right-10 -bottom-10 rotate-12 text-amber-500/5">
                <ShieldAlert size={280} />
            </div>

            <div class="relative z-10">
                <div class="mb-6 flex flex-col items-start gap-4 sm:flex-row sm:items-center">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-500 text-white shadow-lg shadow-amber-200"
                    >
                        <AlertTriangle size={24} />
                    </div>
                    <div>
                        <h2 class="text-2xl font-black tracking-tight text-slate-900 uppercase">
                            {title}
                        </h2>
                        <div class="mt-1 h-1 w-12 rounded-full bg-amber-500"></div>
                    </div>
                </div>

                <p class="mb-10 max-w-2xl text-lg leading-relaxed font-medium text-slate-600">
                    {message}
                </p>

                {#if showActions}
                    <div class="flex flex-wrap gap-4">
                        <Button
                            href={ROUTES.AUTH.LOGIN}
                            variant="primary"
                            size="lg"
                            class="bg-slate-900 px-10 text-white shadow-xl shadow-slate-200 transition-all hover:scale-105"
                        >
                            <LogIn size={20} class="mr-2" /> Login Sekarang
                        </Button>
                        <Button
                            href={ROUTES.AUTH.REGISTER}
                            variant="outline"
                            size="lg"
                            class="border-slate-200 px-10 font-bold text-slate-700 transition-all hover:scale-105 hover:bg-slate-50"
                        >
                            <UserPlus size={20} class="mr-2" /> Daftar Akun
                        </Button>
                    </div>
                {/if}
            </div>
        </div>
    {:else}
        <Card
            variant="none"
            padding="p-6"
            class="group shadow-soft hover:shadow-premium mb-8 rounded-3xl border border-amber-100 bg-amber-50/50 ring-8 ring-amber-50/40 transition-all"
        >
            <div class="flex items-start gap-5">
                <div
                    class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-amber-500 text-white shadow-lg shadow-amber-200 transition-transform group-hover:scale-110 group-hover:rotate-3"
                >
                    {#if icon}
                        {@render icon()}
                    {:else}
                        <AlertTriangle size={28} />
                    {/if}
                </div>
                <div class="pt-1">
                    <strong
                        class="mb-1 block text-xl font-black tracking-tight text-slate-900 uppercase"
                        >{title}</strong
                    >
                    <div class="leading-relaxed font-medium text-slate-600">
                        {#if children}
                            {@render children()}
                        {:else}
                            {message} Silakan
                            <a
                                href={ROUTES.AUTH.LOGIN}
                                class="font-bold text-slate-900 underline decoration-amber-400 decoration-2 underline-offset-4 transition-colors hover:text-amber-600"
                                >login</a
                            >
                            atau
                            <a
                                href={ROUTES.AUTH.REGISTER}
                                class="font-bold text-slate-900 underline decoration-amber-400 decoration-2 underline-offset-4 transition-colors hover:text-amber-600"
                                >daftar</a
                            > sebagai mahasiswa.
                        {/if}
                    </div>
                </div>
            </div>
        </Card>
    {/if}
{/if}
