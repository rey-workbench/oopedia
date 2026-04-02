<script lang="ts">
    import { page } from '@inertiajs/svelte';
    import { onMount } from 'svelte';
    import { Sidebar, Navbar } from '@/components/navigation';
    import Toast from '@/components/ui/Toast.svelte';
    import { toasts } from '@/stores/toast';
    import { sidebarState, initSidebarResponsive } from '@/states/ui';
    import type { SharedProps } from '@/types';

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

    $effect(() => {
        if (flash.success) toasts.success(flash.success);
        if (flash.error) toasts.error(flash.error);
        if (flash.info) toasts.info(flash.info);
        if (flash.warning) toasts.warning(flash.warning);
        if ((flash as any).status) toasts.success((flash as any).status);
    });
</script>

<svelte:head>
    <title>{title}</title>
</svelte:head>

{#if variant === 'auth'}
    <div
        class="font-poppins relative min-h-screen overflow-hidden bg-[#FDFDFB] text-slate-600 antialiased"
    >
        <!-- Flash Messages -->
        {#if flash.success || flash.error || flash.info || flash.warning || (flash as any).status}
            <div class="fixed top-6 right-6 z-50">
                <Toast toasts={$toasts} position="top-right" onremove={(id) => toasts.remove(id)} />
            </div>
        {/if}

        <div class="relative min-h-screen w-full">
            {@render children?.()}
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
            <Toast toasts={$toasts} position="top-right" onremove={(id) => toasts.remove(id)} />

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
