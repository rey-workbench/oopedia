<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import { onMount, onDestroy } from "svelte";
    import Navbar from "@/components/navigation/Navbar.svelte";
    import Sidebar from "@/components/navigation/Sidebar.svelte";
    import Alert from "@/components/ui/Alert.svelte";
    import { sidebarOpen } from "@/stores/sidebar";
    import { isAdmin, isStudent } from "@/utils/roles";
    import type { SharedProps } from "@/types/inertia";

    let {
        title = "OOPEDIAV2",
        showNavbar = true,
        showSidebar = true,
        fullWidth = false,
    } = $props();

    const flash = $derived(($page.props as unknown as SharedProps).flash ?? {});
    const auth = $derived(($page.props as unknown as SharedProps).auth);
    const user = $derived(auth?.user ?? null);
    const isAuthenticated = $derived(!!user);
    const isAdminRole = $derived(isAuthenticated && isAdmin(user?.role_id));
    const isStudentRole = $derived(isAuthenticated && isStudent(user?.role_id));
    const showSidebarRender = $derived(
        showSidebar && (isAdminRole || isStudentRole),
    );

    function handleResize() {
        if (window.innerWidth >= 1024) {
            sidebarOpen.set(false);
        }
    }

    onMount(() => window.addEventListener("resize", handleResize));
    onDestroy(() => window.removeEventListener("resize", handleResize));
</script>

<svelte:head>
    <title>{title}</title>
</svelte:head>

<div
    class="relative flex min-h-screen bg-slate-50 font-sans text-slate-900 antialiased overflow-x-hidden"
>
    {#if showSidebarRender}
        <Sidebar />
        {#if $sidebarOpen}
            <div
                role="button"
                tabindex="0"
                aria-label="Tutup sidebar"
                class="fixed inset-0 z-[45] bg-gray-900/50 backdrop-blur-sm lg:hidden transition-opacity duration-300"
                on:click={() => sidebarOpen.set(false)}
                on:keydown={(e) => e.key === "Escape" && sidebarOpen.set(false)}
            ></div>
        {/if}
    {/if}

    <div
        class="flex-1 flex flex-col min-w-0 transition-all duration-300 {showSidebarRender
            ? 'lg:ml-64'
            : ''}"
    >
        {#if showNavbar}
            <Navbar titlePage={title} />
        {/if}

        <!-- Flash Messages -->
        {#if flash.success || flash.error || flash.info || flash.warning || (flash as any).status}
            <div
                class="fixed top-24 right-6 z-[100] flex flex-col gap-3 pointer-events-auto max-w-sm w-full"
            >
                {#if flash.success}
                    <Alert variant="success" dismissible={true}
                        >{flash.success}</Alert
                    >
                {/if}
                {#if flash.error}
                    <Alert variant="danger" dismissible={true}
                        >{flash.error}</Alert
                    >
                {/if}
                {#if flash.info}
                    <Alert variant="info" dismissible={true}>{flash.info}</Alert
                    >
                {/if}
                {#if flash.warning}
                    <Alert variant="warning" dismissible={true}
                        >{flash.warning}</Alert
                    >
                {/if}
                {#if (flash as any).status}
                    <Alert variant="success" dismissible={true}
                        >{(flash as any).status}</Alert
                    >
                {/if}
            </div>
        {/if}

        <main
            class="flex-1 w-full {fullWidth
                ? ''
                : 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8'}"
        >
            <slot />
        </main>
    </div>
</div>
