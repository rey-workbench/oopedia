<script lang="ts">
    import Button from '@/components/ui/Button.svelte';
    import { Compass, ShieldAlert, Server, Wrench, AlertTriangle, Home } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';

    let { status }: { status: number } = $props();

    const title = $derived(
        (
            {
                503: '503: Service Unavailable',
                500: '500: Server Error',
                404: '404: Page Not Found',
                403: '403: Forbidden',
            } as Record<number, string>
        )[status] ?? 'Error'
    );

    const description = $derived(
        (
            {
                503: 'Sorry, we are doing some maintenance on the site. Please check back soon.',
                500: 'Whoops, something went wrong on our servers.',
                404: 'Sorry, the page you are looking for could not be found.',
                403: 'Sorry, you are forbidden from accessing this page.',
            } as Record<number, string>
        )[status] ?? 'An unexpected error occurred.'
    );

    const illustration = $derived(
        ({ 404: Compass, 403: ShieldAlert, 500: Server, 503: Wrench } as Record<number, any>)[
            status
        ] ?? AlertTriangle
    );
</script>

<svelte:head>
    <title>{title}</title>
</svelte:head>

<div
    class="font-poppins relative flex min-h-screen items-center justify-center overflow-hidden bg-slate-50 p-6"
>
    <!-- Decorative Background -->
    <div class="absolute top-0 left-0 h-full w-full">
        <div
            class="bg-primary-600/5 absolute top-[-10%] left-[-10%] h-[40%] w-[40%] rounded-full blur-[120px]"
        ></div>
        <div
            class="bg-primary-600/5 absolute right-[-10%] bottom-[-10%] h-[40%] w-[40%] rounded-full blur-[120px]"
        ></div>
    </div>

    <div class="animate-in fade-in zoom-in relative w-full max-w-lg text-center duration-700">
        <div
            class="relative z-10 mx-auto mb-8 flex h-32 w-32 items-center justify-center rounded-[2.5rem] bg-white shadow-2xl shadow-slate-200"
        >
            {#if illustration}
                {@const IllComp = illustration}
                <IllComp size={64} class="text-slate-300" />
            {/if}
        </div>

        <h1 class="mb-2 text-6xl font-black tracking-widest text-slate-900">
            {status}
        </h1>
        <h2 class="mb-4 text-xl font-bold tracking-widest text-slate-700 uppercase">
            {title.split(':')[1] || 'Error'}
        </h2>
        <p
            class="mx-auto mb-10 max-w-sm text-xs leading-relaxed font-bold tracking-widest text-slate-400 uppercase"
        >
            {description}
        </p>

        <div class="flex justify-center">
            <Button
                href={ROUTES.HOME}
                variant="primary"
                size="lg"
                icon={Home}
                class="shadow-primary-900/20 shadow-xl"
            >
                KEMBALI KE BERANDA
            </Button>
        </div>

        <p class="mt-12 text-center text-[10px] font-bold tracking-widest text-slate-300 uppercase">
            &copy; {new Date().getFullYear()} OOPEDIA TEAM.
        </p>
    </div>
</div>
