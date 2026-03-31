<script lang="ts">
    import { page } from '@inertiajs/svelte';
    import { onMount } from 'svelte';
    import { Sidebar, Navbar } from '@/components/navigation';
    import { Alert } from '@/components/ui';
    import { sidebarState, initSidebarResponsive } from '@/states/UI';
    import { ROUTES } from '@/utils/route';
    import type { SharedProps } from '@/types/inertia';

    interface Props {
        title?: string;
        showSidebar?: boolean;
        showNavbar?: boolean;
        fullWidth?: boolean;
        variant?: 'app' | 'auth';
        children?: import('svelte').Snippet;
    }

    let {
        title = 'OOPEDIAV2',
        showSidebar = true,
        showNavbar = true,
        fullWidth = false,
        variant = 'app',
        children,
    }: Props = $props();

    const flash = $derived(($page.props as unknown as SharedProps).flash ?? {});
    const showSidebarRender = $derived(variant === 'app' && showSidebar);
    const sidebarOpen = $derived(sidebarState.isOpen);

    onMount(() => {
        return initSidebarResponsive();
    });
</script>

<svelte:head>
    <title>{title}</title>
</svelte:head>

{#if variant === 'auth'}
    <div
        class="font-poppins relative flex min-h-screen items-center justify-center overflow-hidden bg-slate-50 p-6 text-slate-600 antialiased"
    >
        <!-- Flash Messages -->
        {#if flash.success || flash.error || flash.info || flash.warning || (flash as any).status}
            <div
                class="pointer-events-none fixed top-6 right-6 z-100 flex w-full max-w-sm flex-col gap-3"
            >
                {#if flash.success}
                    <Alert variant="success" dismissible={true} class="pointer-events-auto"
                        >{flash.success}</Alert
                    >
                {/if}
                {#if flash.error}
                    <Alert variant="danger" dismissible={true} class="pointer-events-auto"
                        >{flash.error}</Alert
                    >
                {/if}
                {#if flash.info}
                    <Alert variant="info" dismissible={true} class="pointer-events-auto"
                        >{flash.info}</Alert
                    >
                {/if}
                {#if flash.warning}
                    <Alert variant="warning" dismissible={true} class="pointer-events-auto"
                        >{flash.warning}</Alert
                    >
                {/if}
                {#if (flash as any).status}
                    <Alert variant="success" dismissible={true} class="pointer-events-auto"
                        >{(flash as any).status}</Alert
                    >
                {/if}
            </div>
        {/if}

        <!-- Decorative Background -->
        <div class="pointer-events-none absolute top-0 left-0 h-full w-full">
            <div
                class="bg-primary-600/5 absolute top-[-10%] left-[-10%] h-[40%] w-[40%] rounded-full blur-[120px]"
            ></div>
            <div
                class="bg-primary-600/5 absolute right-[-10%] bottom-[-10%] h-[40%] w-[40%] rounded-full blur-[120px]"
            ></div>
        </div>

        <div class="animate-in fade-in zoom-in relative w-full max-w-lg duration-700">
            <!-- Logo -->
            <div class="mb-10 flex flex-col items-center">
                <a href={ROUTES.HOME} class="group flex items-center gap-4">
                    <div
                        class="flex h-16 w-16 items-center justify-center rounded-[2rem] bg-white shadow-2xl shadow-slate-200 transition-transform duration-500 group-hover:rotate-12"
                    >
                        <img src="/images/logo.png" alt="OOPedia" class="h-auto w-10" />
                    </div>
                    <div>
                        <h2 class="text-3xl leading-none font-bold tracking-widest text-slate-900">
                            OOPEDIA
                        </h2>
                        <p
                            class="mt-1 text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                        >
                            Learning System
                        </p>
                    </div>
                </a>
            </div>

            {@render children?.()}

            <p
                class="mt-10 text-center text-[10px] font-bold tracking-widest text-slate-300 uppercase"
            >
                &copy; {new Date().getFullYear()} OOPEDIA TEAM. ALL RIGHTS RESERVED.
            </p>
        </div>
    </div>
{:else}
    <div
        class="relative flex min-h-screen overflow-x-hidden bg-slate-50 font-sans text-slate-900 antialiased"
    >
        {#if showSidebarRender}
            <Sidebar />
            {#if sidebarOpen}
                <div
                    role="button"
                    tabindex="0"
                    aria-label="Tutup sidebar"
                    class="fixed inset-0 z-45 bg-gray-900/50 backdrop-blur-sm transition-opacity duration-300 lg:hidden"
                    onclick={() => sidebarState.close()}
                    onkeydown={(e) => e.key === 'Escape' && sidebarState.close()}
                ></div>
            {/if}
        {/if}

        <div
            class="flex min-w-0 flex-1 flex-col transition-all duration-300 {showSidebarRender
                ? 'lg:ml-64'
                : ''}"
        >
            {#if showNavbar}
                <Navbar />
            {/if}
            <!-- Flash Messages -->
            {#if flash.success || flash.error || flash.info || flash.warning || (flash as any).status}
                <div
                    class="pointer-events-none fixed top-24 right-6 z-100 flex w-full max-w-sm flex-col gap-3"
                >
                    {#if flash.success}
                        <Alert variant="success" dismissible={true} class="pointer-events-auto"
                            >{flash.success}</Alert
                        >
                    {/if}
                    {#if flash.error}
                        <Alert variant="danger" dismissible={true} class="pointer-events-auto"
                            >{flash.error}</Alert
                        >
                    {/if}
                    {#if flash.info}
                        <Alert variant="info" dismissible={true} class="pointer-events-auto"
                            >{flash.info}</Alert
                        >
                    {/if}
                    {#if flash.warning}
                        <Alert variant="warning" dismissible={true} class="pointer-events-auto"
                            >{flash.warning}</Alert
                        >
                    {/if}
                    {#if (flash as any).status}
                        <Alert variant="success" dismissible={true} class="pointer-events-auto"
                            >{(flash as any).status}</Alert
                        >
                    {/if}
                </div>
            {/if}

            <main
                class="w-full flex-1 {fullWidth
                    ? ''
                    : 'mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8'}"
            >
                {@render children?.()}
            </main>
        </div>
    </div>
{/if}
