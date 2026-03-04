<script lang="ts">
    import type { Snippet } from 'svelte';
    import Button from '@/components/ui/Button.svelte';
    import Panel from '@/components/ui/Panel.svelte';
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
        <Panel variant="none" rounded="2xl" padding="p-8" class="border-2 border-amber-200 bg-amber-50/50 relative overflow-hidden shadow-xl">
            <div class="absolute -top-10 -right-10 text-amber-200/30 rotate-12">
                <ShieldAlert size={160} />
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-amber-200 rounded-lg text-amber-800">
                        <AlertTriangle size={20} />
                    </div>
                    <span class="text-xl font-black tracking-tight text-amber-950 uppercase">{title}</span>
                </div>
                
                <p class="text-base font-semibold text-amber-800/90 mb-8 max-w-2xl leading-relaxed">
                    {message}
                </p>
                
                {#if showActions}
                    <div class="flex flex-wrap gap-4">
                        <Button href={ROUTES.AUTH.LOGIN} variant="primary" size="lg" class="px-8 shadow-lg shadow-amber-200">
                            <LogIn size={18} class="mr-2" /> Login Sekarang
                        </Button>
                        <Button href={ROUTES.AUTH.REGISTER} variant="outline" size="lg" class="px-8 border-amber-300 text-amber-900 hover:bg-amber-100">
                            <UserPlus size={18} class="mr-2" /> Daftar Akun
                        </Button>
                    </div>
                {/if}
            </div>
        </Panel>
    {:else}
        <Card
            variant="none"
            padding="p-6"
            class="mb-8 border-2 border-amber-100 bg-amber-50/30 shadow-sm hover:shadow-md transition-shadow group rounded-3xl"
        >
            <div class="flex items-start gap-5">
                <div
                    class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-600 group-hover:scale-110 transition-transform shadow-sm"
                >
                    {#if icon}
                        {@render icon()}
                    {:else}
                        <AlertTriangle size={28} />
                    {/if}
                </div>
                <div class="pt-1">
                    <strong class="mb-2 block text-xl font-black tracking-tight text-amber-950 uppercase">{title}</strong>
                    <div class="text-amber-800/80 font-medium leading-relaxed">
                        {#if children}
                            {@render children()}
                        {:else}
                            {message} Silakan
                            <a
                                href={ROUTES.AUTH.LOGIN}
                                class="font-bold text-amber-900 underline decoration-amber-300 underline-offset-4 hover:text-amber-950 transition-colors"
                                >login</a
                            >
                            atau
                            <a
                                href={ROUTES.AUTH.REGISTER}
                                class="font-bold text-amber-900 underline decoration-amber-300 underline-offset-4 hover:text-amber-950 transition-colors"
                                >daftar</a
                            > sebagai mahasiswa.
                        {/if}
                    </div>
                </div>
            </div>
        </Card>
    {/if}
{/if}
